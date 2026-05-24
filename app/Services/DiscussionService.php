<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionComment;
use App\Models\ProjectDiscussionCategory;
use App\Models\Staff;
use App\Models\Notification;
use App\Models\StaffNotificationPreference;
use App\Models\ProjectActivity;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DiscussionService
{
    public function updateDiscussion(Project $project, ProjectDiscussion $discussion, array $data): void
    {
        $discussion->update([
            'subject' => $data['subject'] ?? $discussion->subject,
            'description' => $data['description'] ?? $discussion->description,
            'show_to_customer' => $data['show_to_customer'] ?? $discussion->show_to_customer,
            'category_id' => $data['category_id'] ?? $discussion->category_id,
        ]);
    }

    public function updateComment(ProjectDiscussionComment $comment, array $data): void
    {
        $comment->update([
            'content' => strip_tags($data['content'] ?? '', '<br><p><b><i><u><a><strong><em><ul><ol><li>'),
        ]);
    }

    public function addCommentWithFile(Project $project, ProjectDiscussion $discussion, array $data, $file = null): ProjectDiscussionComment
    {
        $sanitized = strip_tags($data['content'], '<br><p><b><i><u><a><strong><em><ul><ol><li>');

        $commentData = [
            'discussion_id' => $discussion->id,
            'discussion_type' => 'discussion',
            'content' => $sanitized,
            'staff_id' => Auth::id(),
            'created' => now(),
            'parent' => $data['parent_id'] ?? null,
        ];

        if ($file) {
            $path = $file->store('discussion_files/' . $discussion->id, 'public');
            $commentData['file_name'] = $path;
            $commentData['original_file_name'] = $file->getClientOriginalName();
            $commentData['file_mime_type'] = $file->getMimeType();
            $commentData['file_size'] = $file->getSize();
        }

        $mentions = $this->parseMentions($data['content']);
        if (!empty($mentions)) {
            $commentData['mentions'] = json_encode($mentions);
        }

        $comment = ProjectDiscussionComment::create($commentData);

        if (!empty($mentions)) {
            foreach ($mentions as $staffId) {
                \App\Models\ProjectDiscussionMention::create([
                    'discussion_id' => $discussion->id,
                    'comment_id' => $comment->id,
                    'mentioned_staff_id' => $staffId,
                ]);
            }
        }

        $discussion->update(['last_activity' => now()]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_discussion_commented',
            'additional_data' => json_encode([
                'discussion_subject' => $discussion->subject,
                'comment_content' => substr($comment->content, 0, 100),
            ]),
            'dateadded' => now(),
        ]);

        $this->notifyParticipants($project, $discussion, $comment);
        $this->notifyMentions($project, $discussion, $comment, $mentions);

        return $comment;
    }

    public function searchDiscussions(Project $project, string $query, ?int $categoryId = null): array
    {
        $discussions = $project->discussions()
            ->with(['staff', 'comments'])
            ->where(function ($q) use ($query) {
                $q->where('subject', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });

        if ($categoryId) {
            $discussions->where('category_id', $categoryId);
        }

        return $discussions->orderBy('last_activity', 'desc')->get()->toArray();
    }

    public function searchComments(Project $project, string $query): array
    {
        $discussionIds = $project->discussions()->pluck('id');
        return ProjectDiscussionComment::whereIn('discussion_id', $discussionIds)
            ->where('content', 'like', "%{$query}%")
            ->with(['discussion', 'staff'])
            ->get()
            ->toArray();
    }

    public function parseMentions(string $content): array
    {
        preg_match_all('/@([\w.\-]+)/', $content, $matches);
        $names = array_unique($matches[1] ?? []);
        if (empty($names)) return [];

        $staffIds = [];
        foreach ($names as $name) {
            $parts = explode('.', $name);
            if (count($parts) === 2) {
                $staff = Staff::where('firstname', $parts[0])->where('lastname', $parts[1])->first();
            } else {
                $staff = Staff::where('firstname', 'like', "%{$name}%")
                    ->orWhere('lastname', 'like', "%{$name}%")
                    ->first();
            }
            if ($staff) {
                $staffIds[] = $staff->staffid;
            }
        }
        return array_unique($staffIds);
    }

    public function notifyMentions(Project $project, ProjectDiscussion $discussion, ProjectDiscussionComment $comment, array $mentionedIds): void
    {
        if (empty($mentionedIds)) return;

        $author = Auth::user()->full_name ?? 'Someone';
        $message = "{$author} mentioned you in a discussion <strong>" . e($discussion->subject) . '</strong>';
        $link = route('admin.projects.discussions', $project->id);

        Notification::createForMultipleStaff(
            $mentionedIds,
            $message,
            $link,
            notificationType: 'discussion_mention',
            groupingKey: 'discussion_mention_' . $discussion->id
        );
    }

    public function notifyParticipants(Project $project, ProjectDiscussion $discussion, ProjectDiscussionComment $comment): void
    {
        $commenters = ProjectDiscussionComment::where('discussion_id', $discussion->id)
            ->where('staff_id', '!=', Auth::id())
            ->whereNotNull('staff_id')
            ->distinct()
            ->pluck('staff_id')
            ->toArray();

        $watchers = $project->watchers()
            ->where('staff_id', '!=', Auth::id())
            ->pluck('staff_id')
            ->toArray();

        $recipients = array_unique(array_merge($commenters, $watchers));

        if (empty($recipients)) return;

        $author = Auth::user()->full_name ?? 'Someone';
        $message = "{$author} commented on discussion <strong>" . e($discussion->subject) . '</strong>';
        $link = route('admin.projects.discussions', $project->id);

        Notification::createForMultipleStaff(
            $recipients,
            $message,
            $link,
            notificationType: 'discussion_comment',
            groupingKey: 'discussion_' . $discussion->id
        );

        $this->sendEmailNotifications($recipients, $discussion, $comment, $project);
    }

    protected function sendEmailNotifications(array $staffIds, ProjectDiscussion $discussion, ProjectDiscussionComment $comment, Project $project): void
    {
        $author = Auth::user()->full_name ?? 'Someone';
        $subject = "Re: [{$project->name}] {$discussion->subject}";

        foreach ($staffIds as $staffId) {
            $pref = StaffNotificationPreference::where('staffid', $staffId)
                ->where('notification_type', 'discussion_comment')
                ->first();

            if ($pref && !$pref->email) continue;

            $staff = Staff::find($staffId);
            if (!$staff || !$staff->email) continue;

            try {
                $viewData = [
                    'subject' => $subject,
                    'body' => view('emails.discussion-comment', [
                        'author' => $author,
                        'discussion' => $discussion,
                        'comment' => $comment,
                        'project' => $project,
                        'recipient' => $staff,
                    ])->render(),
                    'logo' => SettingsHelper::get('company_logo', ''),
                    'company_name' => SettingsHelper::get('companyname', config('app.name')),
                    'company_address' => SettingsHelper::get('companyaddress', ''),
                ];

                Mail::send('emails.base', $viewData, function ($mail) use ($staff, $subject) {
                    $mail->to($staff->email, $staff->full_name);
                    $mail->subject($subject);
                    $mail->from(
                        SettingsHelper::get('smtp_email', config('mail.from.address')),
                        SettingsHelper::get('companyname', config('mail.from.name'))
                    );
                });
            } catch (\Exception $e) {
                Log::error("Discussion email notification failed for {$staff->email}: " . $e->getMessage());
            }
        }
    }

    public function createEmailIntegration(): void
    {
        // Stub for email-to-discussion integration
        // Future: Configure mailbox parsing to create discussion comments from emails
    }
}

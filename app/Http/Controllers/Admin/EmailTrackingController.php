<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailClickTracking;
use App\Models\EmailTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailTrackingController extends Controller
{
    public function trackOpen(string $messageId)
    {
        $tracking = EmailTracking::where('message_id', $messageId)->first();

        if ($tracking) {
            $tracking->markOpened();

            if (!$tracking->user_agent) {
                $tracking->update([
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                ]);
            }
        }

        return response()->gif(base64_decode(
            'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'
        ));
    }

    public function trackClick(Request $request, string $messageId)
    {
        $tracking = EmailTracking::where('message_id', $messageId)->first();

        $url = $request->query('url');
        if (!$url) {
            abort(404);
        }

        $decodedUrl = base64_decode($url);
        if (!$decodedUrl) {
            abort(404);
        }

        if ($tracking) {
            $tracking->increment('click_count');

            EmailClickTracking::create([
                'email_tracking_id' => $tracking->id,
                'url' => $decodedUrl,
                'clicked_at' => now(),
            ]);
        }

        return redirect()->away($decodedUrl);
    }

    public function preview(string $slug)
    {
        $data = request()->except('_token');
        $preview = app(\App\Services\EmailService::class)->getPreviewData($slug, $data);

        if (isset($preview['error'])) {
            return response()->json($preview, 404);
        }

        if (request()->wantsJson()) {
            return response()->json($preview);
        }

        return view('admin.settings.email_preview', [
            'subject' => $preview['subject'],
            'body' => $preview['body'],
            'fromName' => $preview['from_name'],
            'fromEmail' => $preview['from_email'],
            'template' => $preview['template'],
        ]);
    }

    public function testSend(Request $request)
    {
        $request->validate([
            'slug' => 'required|string',
            'to' => 'required|email',
            'data' => 'nullable|array',
        ]);

        $emailService = app(\App\Services\EmailService::class);
        $result = $emailService->send(
            $request->to,
            $request->slug,
            $request->data ?? [],
        );

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Test email queued for delivery']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send test email'], 500);
    }
}

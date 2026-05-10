<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseGroup;

class HomeController extends Controller
{
    public function index()
    {
        // Check if KB is enabled
        if (\App\Helpers\SettingsHelper::get('use_knowledge_base', '1') == '0') {
            return view('guest.home_simple');
        }

        // Show a few latest KB articles on the landing page
        $articles = \App\Models\KnowledgeBase::where('active', 1)
            ->orderBy('article_order')
            ->limit(6)
            ->get();

        return view('guest.home', compact('articles'));
    }

    public function kb(Request $request)
    {
        if (\App\Helpers\SettingsHelper::get('use_knowledge_base', '1') == '0') {
            abort(404);
        }
        
        $query = $request->query('q');
        $group = $request->query('group');
        
        $articles = KnowledgeBase::query()
            ->when($query, function ($q) use ($query) {
                return $q->where(function($sub) use ($query) {
                    $sub->where('subject', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            })
            ->when($group, function ($q) use ($group) {
                return $q->where('articlegroup', $group);
            })
            ->where('active', 1)
            ->orderBy('article_order')
            ->get();

        $allGroups = KnowledgeBaseGroup::where('active', 1)->orderBy('group_order')->get();

        return view('guest.knowledge_base', compact('articles', 'allGroups'));
    }

    public function kbArticle($slug)
    {
        $article = KnowledgeBase::where('slug', $slug)->where('active', 1)->firstOrFail();
        
        $feedbackCounts = \App\Models\KnowledgeBaseArticleFeedback::where('articleid', $article->articleid)
            ->selectRaw("SUM(CASE WHEN answer = 1 THEN 1 ELSE 0 END) as likes, SUM(CASE WHEN answer = 0 THEN 1 ELSE 0 END) as dislikes")
            ->first();
        
        return view('guest.kb_article', compact('article', 'feedbackCounts'));
    }

    public function kbFeedback(Request $request, $id)
    {
        $article = KnowledgeBase::findOrFail($id);
        
        \App\Models\KnowledgeBaseArticleFeedback::create([
            'articleid' => $article->articleid,
            'answer' => $request->input('helpful'),
            'ip' => $request->ip(),
            'date' => now(),
        ]);
        
        return back()->with('success', 'Thank you for your feedback!');
    }

    public function staffLogin()
    {
        return view('auth.staff.login');
    }

    public function clientLogin()
    {
        return view('auth.client.login');
    }
}

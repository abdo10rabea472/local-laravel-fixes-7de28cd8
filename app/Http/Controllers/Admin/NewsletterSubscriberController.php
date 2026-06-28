<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->q, fn ($q, $t) => $q->where('email', 'like', "%$t%"))
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $total = NewsletterSubscriber::count();
        $active = NewsletterSubscriber::where('active', true)->count();

        return view('admin.content.subscribers.index', compact('subscribers','total','active'));
    }

    public function toggle(NewsletterSubscriber $subscriber)
    {
        $subscriber->update([
            'active' => ! $subscriber->active,
            'unsubscribed_at' => $subscriber->active ? now() : null,
        ]);
        return back();
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'تم الحذف.');
    }

    public function export(): StreamedResponse
    {
        $filename = 'newsletter-'.now()->format('Ymd-His').'.csv';
        return response()->streamDownload(function () {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['id','email','active','subscribed_at','unsubscribed_at']);
            NewsletterSubscriber::orderBy('id')->chunk(500, function ($rows) use ($h) {
                foreach ($rows as $r) {
                    fputcsv($h, [$r->id, $r->email, $r->active ? 1 : 0, (string) $r->subscribed_at, (string) $r->unsubscribed_at]);
                }
            });
            fclose($h);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:150']);

        NewsletterSubscriber::updateOrCreate(
            ['email' => $data['email']],
            ['active' => true, 'subscribed_at' => now(), 'unsubscribed_at' => null]
        );

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'تم الاشتراك بنجاح']);
        }

        return back()->with('success', 'تم اشتراكك في النشرة البريدية بنجاح');
    }
}

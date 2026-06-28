<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><title>{{ $post->title }}</title></head>
<body style="font-family:Tahoma,Arial,sans-serif;background:#f5f7fb;padding:24px;">
    <div style="max-width:600px;margin:auto;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden">
        <div style="background:#7c3aed;color:#fff;padding:18px 24px"><h2 style="margin:0">مقال جديد من UNI-LAB MARKET</h2></div>
        @if($post->image)
            <img src="{{ $post->image_url }}" alt="" style="width:100%;display:block">
        @endif
        <div style="padding:24px;color:#0f172a;line-height:1.7">
            <h1 style="margin:0 0 12px;font-size:22px">{{ $post->title }}</h1>
            @if($post->excerpt)
                <p style="color:#475569">{{ $post->excerpt }}</p>
            @endif
            <p style="text-align:center;margin:28px 0">
                <a href="{{ $url }}" style="background:#7c3aed;color:#fff;text-decoration:none;padding:12px 28px;border-radius:999px;font-weight:bold">قراءة المقال</a>
            </p>
            <p style="font-size:12px;color:#64748b;word-break:break-all">{{ $url }}</p>
            <p style="margin-top:30px;font-size:12px;color:#64748b">— فريق UNI-LAB MARKET</p>
        </div>
    </div>
</body>
</html>

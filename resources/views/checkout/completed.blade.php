@extends('layouts.app')

@section('content')
@php
    $isPaid     = in_array($order->payment_status, ['paid'], true);
    $isCod      = $order->payment_gateway === 'cod';
    $isPending  = in_array($order->payment_status, ['pending', 'unpaid'], true);
    $isFailed   = in_array($order->payment_status, ['failed'], true);
    $payError   = session('error');
@endphp

<div class="container py-5" style="max-width:720px">

    @if($payError)
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4">
            <i class="fa-solid fa-circle-exclamation mt-1"></i>
            <div>
                <strong class="d-block mb-1">فشل بدء عملية الدفع</strong>
                <small>{{ $payError }}</small>
            </div>
        </div>
    @endif

    <div class="card shadow-sm border-0 text-center p-4 p-md-5">
        @if($isPaid)
            <div class="mb-3 text-success" style="font-size:48px"><i class="fa-solid fa-circle-check"></i></div>
            <h1 class="h4 text-success">تم الدفع بنجاح</h1>
            <p class="text-muted">شكرًا لطلبك! تم استلام دفعتك بنجاح.</p>
        @elseif($isCod)
            <div class="mb-3 text-success" style="font-size:48px"><i class="fa-solid fa-circle-check"></i></div>
            <h1 class="h4 text-success">تم إنشاء طلبك بنجاح</h1>
            <p class="text-muted">
                سيتم تحصيل المبلغ
                <strong>{{ number_format((float)$order->total, 2) }} {{ $order->currency }}</strong>
                عند الاستلام.
            </p>
        @elseif($isFailed || $payError)
            <div class="mb-3 text-danger" style="font-size:48px"><i class="fa-solid fa-circle-xmark"></i></div>
            <h1 class="h4 text-danger">لم يتم الدفع</h1>
            <p class="text-muted">
                طلبك محفوظ بحالة "بانتظار الدفع" ويمكنك المحاولة مرة أخرى.
            </p>
        @elseif($isPending)
            <div class="mb-3 text-warning" style="font-size:48px"><i class="fa-solid fa-clock"></i></div>
            <h1 class="h4 text-warning">بانتظار إتمام الدفع</h1>
            <p class="text-muted">لم نستلم تأكيد الدفع من بوابة الدفع بعد.</p>
        @else
            <div class="mb-3 text-primary" style="font-size:48px"><i class="fa-solid fa-receipt"></i></div>
            <h1 class="h4">تفاصيل الطلب</h1>
        @endif

        <hr class="my-4">

        <dl class="row text-start mb-0 small">
            <dt class="col-5 text-muted">رقم الطلب</dt>
            <dd class="col-7 fw-bold">#{{ $order->order_number }}</dd>

            <dt class="col-5 text-muted">طريقة الدفع</dt>
            <dd class="col-7 fw-bold">{{ $order->payment_gateway ?? '—' }}</dd>

            <dt class="col-5 text-muted">حالة الدفع</dt>
            <dd class="col-7 fw-bold">{{ $order->payment_status ?? '—' }}</dd>

            <dt class="col-5 text-muted">الإجمالي</dt>
            <dd class="col-7 fw-bold">{{ number_format((float)$order->total, 2) }} {{ $order->currency }}</dd>
        </dl>

        <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
            @if(! $isPaid && ! $isCod && $order->payment_gateway)
                <a href="{{ route('checkout.pay', $order) }}?gateway={{ urlencode($order->payment_gateway) }}"
                   class="btn btn-primary">
                    <i class="fa-solid fa-rotate-right ms-1"></i> إعادة محاولة الدفع
                </a>
            @endif
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">متابعة التسوق</a>
        </div>
    </div>
</div>
@endsection

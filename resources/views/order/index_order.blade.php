@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Orders') }}</div>
                <div class="card-body m-auto">
                    @foreach ($orders as $order)
                    <div class="card mb-2" style="width: 30rem;">
                        <div class="card-body">
                            <a href="{{ route('show_order', $order) }}">
                                <h5 class="card-title">Order ID {{ $order->id }}</h5>
                            </a>
                            <h6 class="card-subtitle mb-2 text-muted">By {{ $order->user->name }}</h6>
                            @if ($order->is_paid == true)
                            <p class="card-text">Paid</p>
                            @else
                            <p class="card-text">Unpaid</p>
                            @if ($order->payment_receipt)
                            <div class="d-flex flex-row justify-content-around">
                                <a href="{{ url('storage/' . $order->payment_receipt) }}" class="btn btn-primary">Show payment receipt</a>
                                @if (Auth::user()->is_admin)
                                <form action="{{ route('confirm_payment', $order) }}" method="post" onsubmit="return confirm('Apakah Anda Yakin ?');">
                                    @csrf
                                    <button class="btn btn-success" type="submit">Confirm</button>
                                </form>
                                @endif
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order</title>
</head>
<body>
    @foreach ($orders as $order)
        <p>ID: {{ $order->id }}</p>
        <p>User: {{ $order->user->name }}</p>
        <p>{{ $order->created_at }}</p>
        <p>
            @if ($order->is_paid == true)
                Paid
                @else
                Unpaid
                @if ($order->payment_receipt)
                    <a href="{{ url('storage/' . $order->payment_receipt) }}">Show payment receipt</a>
                @endif
                <form action="{{ route('confirm_payment', $order) }}" method="post" onsubmit="return confirm('Apakah Anda Yakin ?');">
                    @csrf
                    <button type="submit">Confirm</button>
                </form>
            @endif
        </p>
    @endforeach
</body>
</html> --}}
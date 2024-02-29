@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cart') }}</div>
                <div class="card-body">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    @endif
                    <div class="card-group m-auto">
                        @foreach ($carts as $cart)
                            <div class="card m-3" style="width: 14rem;">
                                <img class="card-img-top" src="{{ url('storage/' . $cart->product->image) }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $cart->product->name }}</h5>
                                    <form action="{{ route('update_cart', $cart) }}" method="post">
                                        @method('patch')
                                        @csrf
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" aria-describedby="basic-addon2" name="amount" value="{{ $cart->amount }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">Update amount</button>
                                            </div>
                                        </div>
                                    </form>
                                    <form action="{{ route('delete_cart', $cart) }}" method="post" onsubmit="return confirm('Apakah Anda Yakin ?');">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form action="{{ route('apply.discount') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <label for="discount_code">Discount Code:</label><br>
                        <input type="text" id="discount_code" name="discount_code"><br><br>
                        <button type="submit">Apply Discount</button>
                    </form>
                    @isset($total_price)
                    <div class="d-flex flex-column justify-content-end align-items-end">
                        <h2>Total Price: Rp.{{ $total_price }}</h2>
                        <form action="{{ route('checkout') }}" method="post">
                            @csrf
                            <input type="hidden" name="total_price" value="{{ $total_price }}">
                            <button type="submit" class="btn btn-primary" @if ($carts->isEmpty()) disabled @endif>Checkout</button>
                        </form>
                    </div>
                    @endisset                                         
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

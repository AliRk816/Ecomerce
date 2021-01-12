@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @foreach (Auth()->user()->orders as $order)
                        <div class="card">
                            <div class="card-header">
                                This order is placed on {{ Carbon\Carbon::parse($order->payment_created_at)->
                                format('d/m/Y H:i') }} the amount of <strong>{{ getPrice($order->amount) }}</strong>
                            </div>
                            <div class="card-body">
                                <h3>Products list</h3>
                                @foreach (unserialize($order->products) as $product)
                                    <div>Product name: {{ $product[0] }}</div>
                                    <div>Price: {{ getPrice($product[1]) }}</div>
                                    <div>Quantity: {{ $product[2] }}</div>
                                    <hr>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    
                    <a href="{{ route('products.index') }}">Go to our store.</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

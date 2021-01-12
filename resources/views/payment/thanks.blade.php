@extends('layouts.master')

@section('content')
    <div class="container thanks mt-4">
        <h1 class="mt-4">Thanks!</h1>
        <h5 class="mt-2">Your Order has been processed successfully</h5>
        <button class="btn btn-primary mt-4">
            <a href="{{ route('products.index') }}" style="color: white;">Go back to store</a>
        </button>
    </div>
@endsection
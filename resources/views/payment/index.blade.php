@extends('layouts.master')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('script')
    <script src="https://js.stripe.com/v3/"></script>
@endsection

@section('content')
    <div class="col-md-12 payment mt-4">
        <button class="btn btn-primary mt-4" style="float: left">
            <a href="{{ route('products.index') }}" style="color: white; ">Back to store</a>
        </button>
        <h1 class="mt-4" style="margin-right: 100px">Proceed To Payment</h1>
        <div class="row">
            <div class="col-md-6 mt-4  form-pay">
                <form action="{{ route('payment.store') }}" method="POST" id="payment-form" class="my-4">
                    @csrf
                    <div id="card-element">
                        <!-- Elements will create input elements here -->
                    </div>
                    
                    <!-- We'll put the error messages in this element -->
                    <div id="card-errors" role="alert"></div>
                    
                    <button class="btn btn-success" id="submit" style="margin-top: 45px;width: 50%;">Pay ({{ getPrice($total) }}) </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var stripe = Stripe('pk_test_k1yjJerIEy7ozO5OAyhHPhdt00W9eKTD3L');
        var elements = stripe.elements();
        var style = {
            base: {
            color: "#32325d",
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: "antialiased",
            fontSize: "16px",
            "::placeholder": {
                color: "#aab7c4"
            }
            },
            invalid: {
            color: "#fa755a",
            iconColor: "#fa755a"
            }
        };
        var card = elements.create("card", { style: style });
        card.mount("#card-element");
        card.on('change', ({error}) => {
            let displayError = document.getElementById('card-errors');
            if (error) {
                displayError.classList.add('alert', 'alert-danger');
                displayError.textContent = error.message;
            } else {
                displayError.classList.remove('alert', 'alert-danger');
                displayError.textContent = '';
            }
        });
        var submitButton = document.getElementById('submit');

        submitButton.addEventListener('click', function(ev) {
        ev.preventDefault();
        submitButton.disabled = true;
        stripe.confirmCardPayment("{{ $clientSecret }}", {
            payment_method: {
            card: card
            }
        }).then(function(result) {
            if (result.error) {
            // Show error to your customer (e.g., insufficient funds)
            submitButton.disabled = false;
            console.log(result.error.message);
            } else {
            // The payment has been processed!
            if (result.paymentIntent.status === 'succeeded') {
                var paymentIntent = result.paymentIntent;
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                var form = document.getElementById('payment-form');
                var url = form.action;

                fetch(
                    url,
                    {
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json, text-plain, */* ",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": token
                        },
                        method: 'post',
                        body: JSON.stringify({
                            paymentIntent: paymentIntent
                        })
                    }).then((data) => {
                        console.log(data);
                            if (data.status === 400) {
                                redirect = '/store';
                            } else {
                                redirect = '/thanks';
                            }
                            form.reset();
                            window.location.href = redirect;
                        }).catch((error) => {
                        console.log(error)
                    })
                }
            }
        });
    });
</script>
@endsection

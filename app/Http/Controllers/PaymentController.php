<?php

namespace App\Http\Controllers;

use DateTime;
use App\Order;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Product;
use Gloudemans\Shoppingcart\Facades\Cart;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Cart::count() <= 0 ) {
            return redirect()->route('products.index');
        }

        Stripe::setApiKey('sk_test_E9SVRniizkocsbAfz5kUbYTs009NPqsDvW');

        if (request()->session()->has('coupon')) {
            $total = (Cart::subtotal() - request()->session()->get('coupon')['remise']) + (Cart::subtotal() - request()->session()->get('coupon')['remise']) * (config('cart.tax') / 100);
        } else {
            $total = Cart::total();
        }
        
        $intent = PaymentIntent::create([
            'amount' => round($total),
            'currency' => 'usd',
            // Verify your integration in this guide by including this parameter
            // 'metadata' => ['integration_check' => 'accept_a_payment'],
        ]);
        
        $clientSecret = Arr::get($intent, 'client_secret');

        return view('payment.index', [
            'clientSecret' => $clientSecret,
            'total' => $total
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->noLongerStock()) {
            Session::flash('error', 'The product is invailable in your cart.');
            return response()->json(['success' => false], 400);
        }

        $data = $request->json()->all();

        $order = new order();

        $order->payment_intent_id = $data['paymentIntent']['id'];
        $order->amount = $data['paymentIntent']['amount'];
        $order->payment_created_at = (new DateTime())->setTimestamp($data['paymentIntent']['created'])->format('Y-m-d H:i:s');
        $products = [];
        $i = 0;

        foreach (Cart::content() as $product) {
            $products['product_' . $i][] = $product->model->title;
            $products['product_' . $i][] = $product->model->price;
            $products['product_' . $i][] = $product->qty;
            $i++;
        }

        $order->products = serialize($products);
        $order->user_id = Auth()->user()->id;

        $order->save();

        if ($data['paymentIntent']['status'] === 'succeeded') {
            $this->updateStock();
            Cart::destroy();
            Session::flash('success', 'Your Order has been processed successfully.');
            return response()->json(['success' => 'Payment Intent Succeeded']);
        } else {
            return response()->json(['error' => 'Payment Intent Not Succeeded']);
        }
    }

    public function thanks()
    {
        return Session::has('success') ? view('payment.thanks') : redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function noLongerStock()
    {
        foreach (Cart::content() as $item) {
            $product = Product::find($item->model->id);

            if ($item->qty > $product->stock) {
                return true;
            }
        }
        return false;
    }

    private function updateStock()
    {
        foreach (Cart::content() as $item) {
            $product = Product::find($item->model->id);
            $product->update(['stock' => $product->stock - $item->qty]);
        }
    }
}

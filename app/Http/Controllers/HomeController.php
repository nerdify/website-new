<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );
    }

    public function plans()
    {
        $user = auth()->user();
        $list = [];
        $infoPlans = $this->stripe->plans->all(['active' => true]);
        foreach ($infoPlans['data'] as $key => $plan) {
            $product = $this->stripe->products->retrieve(
                $plan['product'],
                []
            );

            $name = preg_replace('/\s+/', '_', $product['name']);
            $checkout = $user
                ->newSubscription($name, $plan['id'])
                ->checkout([
                    'success_url' => route('home'),
                ]);

            $list[] = [
                'id' => $plan['id'],
                'amount' => $plan['amount'] ? $plan['amount']/100 : 0,
                'monthly' => $plan['interval_count'],
                'product_name' => $product['name'],
                'image' => $product['images'][0],
                'checkout' => $checkout->url,
            ];
        }

        return $list;
    }

    public function getPlanName($plan)
    {
        $infoPlan = $this->stripe->plans->retrieve(
            $plan,
            []
        );

        $product = $this->stripe->products->retrieve(
            $infoPlan['product'],
            []
        );

        return strtolower(preg_replace('/\s+/', '_', $product['name']));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->subscribed('michi_plan')) {
            return redirect('invoices')->with('success', "Ya esta subscrito");
        }

        $intent = $user->createSetupIntent();



        $plans = $this->plans();

        return view('home', compact('intent', 'plans'));
    }

    public function process(Request $request)
    {
        $user = auth()->user();
        $plan = $request->input('plan');
        $paymentMethod = $request->input('payment_method');
        $name = $this->getPlanName($plan);

        $user->newSubscription($name, $plan)->create($paymentMethod, [
            'email' => $user->email,
        ]);

        return redirect('home')->with('success', "Subscription correcta!");
    }

    public function invoiceList()
    {
        $user = auth()->user();
        $invoices = $user->invoices();

        return view('invoices', compact('invoices'));
    }

    public function invoice($invoice)
    {
        return auth()->user()->downloadInvoice($invoice);
    }


}

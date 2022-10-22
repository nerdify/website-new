@extends('layouts.app')

@section('custom-css')
    <style>
        #card-element {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            background: #f8fafc;
            padding: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h1>Laravel Cashier</h1></div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if(session()->get('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif

                        <form id="payment-form" method="POST" action="{{ route('process') }}">
                            @csrf

                            <div class="d-flex flex-row bd-highlight mb-3">
                                @foreach($plans as $key => $plan)
                                    <div>
                                        <div>
                                            <img style="max-height: 100px;" src="{{ $plan['image'] }}" class="img-thumbnail" alt="{{ $plan['product_name'] }}">
                                        </div>
                                        <div class="form-check p-3">
                                            <input class="form-check-input" {{ !$key ? 'checked' : '' }} type="radio" value="{{ $plan['id'] }}" name="plan" id="plan_{{ $key }}">
                                            <label class="form-check-label" for="plan_{{ $key }}">
                                                {{ $plan['product_name'] }} ($ {{ number_format($plan['amount'],2) }})
                                            </label>
                                        </div>
                                        <div>
                                            <a target="_blank" class="btn btn-outline-info" href="{{ $plan['checkout'] }}">
                                                Checkout
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <input class="form-control mb-2" id="card-holder-name" type="text">
                            <input type="hidden" value="" name="payment_method" id="payment_method">

                            <!-- Stripe Elements Placeholder -->
                            <div id="card-element"></div>

                            <button class="btn btn-success mt-2" data-secret="{{ $intent->client_secret }}" id="card-button">
                                Update Payment Method
                            </button>


                            <div class="alert alert-danger mt-5" id="error-message">
                                <!-- Display error message to your customers here -->
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        $(function() {
            const stripe = Stripe('{{ env('STRIPE_KEY') }}');

            const elements = stripe.elements();
            const cardElement = elements.create('card');

            cardElement.mount('#card-element');

            const cardHolderName = $('#card-holder-name').val();
            const cardButton = $('#card-button');
            const clientSecret = cardButton.data('secret');

            cardButton.on('click', async (e) => {
                e.preventDefault();
                const { setupIntent, error } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: { name: cardHolderName }
                        }
                    }
                );

                if (error) {
                    console.log({error})
                    $("#error-message").text(error.message).show();
                } else {
                    console.log({setupIntent})
                    $('#payment_method').val(setupIntent.payment_method);
                    $('#payment-form').submit();
                }
            });

            $("#error-message").hide();
        });

    </script>
@endsection

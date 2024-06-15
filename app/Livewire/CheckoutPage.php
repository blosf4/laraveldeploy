<?php

namespace App\Livewire;

use App\Helpers\CartMangement;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use YooKassa\Client;



#[Title('Оформление | ShopCMS')]

class CheckoutPage extends Component
{

    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;

    public function mount()
    {
        $cart_items = CartMangement::getCartItemsFromCookie();
        if(count($cart_items) == 0) {
            return redirect('/products');
        }
    }
    public function getClient(): Client
    {
        $client = new Client();
        $client->setAuth('396657', 'test_eMagH-g3aDO3ptlci7wv3h76ZshxL-rgOKUTlHyoTss');
        return $client;
    }

    public function createPayment(float $amount, string $description, string $redirectUrl)
    {
        $client = $this->getClient();
        $payment = $client->createPayment([
            'amount' => [
                'value' => $amount,
                'currency' => 'RUB',
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('success'),
            ],
            'description' => $description,
        ],uniqid('', true));

        return $payment->getConfirmation()->getConfirmationUrl();
    }

    public function placeOrder()
    {

        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required',
        ]);


        $cart_items = CartMangement::getCartItemsFromCookie();
        $line_items = [];

        foreach ($cart_items as $item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'RUB',
                    'unit_price' => $item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'quantity' => $item['quantity'],
                ]
            ];
        }

        $ytotal = CartMangement::calculateGrandTotal($cart_items);
        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartMangement::calculateGrandTotal($cart_items);
        $order->payment_method = $this->payment_method;
        $order->payment_status = 'pending';
        $order->status = 'new';
        $order->currency = 'RUB';
        $order->shipping_amount = 0;
        $order->shipping_method = 'none';
        $order->notes = 'Заказ размещен '.auth()->user()->name;

        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->street_address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;

        $redirect_url = url('/');

        if($this->payment_method == 'stripe'){
//            $this->createPayment($ytotal, 'оплата заказа', $redirect_url);
            $client = new Client();
            $client->setAuth('396657', 'test_eMagH-g3aDO3ptlci7wv3h76ZshxL-rgOKUTlHyoTss');
            $payment = $client->createPayment(
                array(
                    'amount' => array(
                        'value' => $ytotal,
                        'currency' => 'RUB',
                    ),
                    'confirmation' => array(
                        'type' => 'redirect',
                        'return_url' => route('success'),
                    ),
                    'capture' => true,
                    'description' => 'Заказ оформлен',
                ),
                uniqid('', true)
            );
            $redirect_url = $payment->getConfirmation()->getConfirmationUrl();

        }else{
            $redirect_url = route('success');

        }

        $order->save();
        $address->order_id = $order->id;
        $address->save();
        $order->items()->createMany($cart_items);
        CartMangement::clearCartItems($cart_items);
        return redirect($redirect_url);

    }


    public function render()
    {
        $cart_items = CartMangement::getCartItemsFromCookie();
        $grand_total = CartMangement::calculateGrandTotal($cart_items);

        return view('livewire.checkout-page',[
            'cart_items' => $cart_items,
            'grand_total' => $grand_total
        ]);
    }
}

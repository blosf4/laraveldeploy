<?php

namespace App\Livewire;

use App\Helpers\CartMangement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;


#[Title('Подробнее | ShopCMS')]
class ProductDetailPage extends Component
{
    use LivewireAlert;
    public $slug;
    public $quantity = 1;

    public function mount($slug){
        $this->slug = $slug;
    }
    public function increaseQty()
    {
        $this->quantity++;
    }

    public function decreaseQty(){
        if($this->quantity > 1){
            $this->quantity--;
        }
    }
    // add
    public function addToCart($product_id)
    {
        $total_count = CartMangement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);

        $this->alert('success', 'Товар добавлен в корзину', [
            'position' => 'bottom',
            'timer' => 3000,
            'toast' => true,
        ]);
    }


    public function render()
    {
        return view('livewire.product-detail-page', [
            'product' => Product::where('slug', $this->slug)->firstOrFail(),
        ]);
    }
}

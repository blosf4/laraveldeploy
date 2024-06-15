<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CanelPage;
use App\Livewire\CartPage;
use App\Livewire\CategotiesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class);
Route::get('/categories', CategotiesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/cart', CartPage::class);
Route::get('/products/{slug}', ProductDetailPage::class);

Route::middleware('guest')->group(function () {
    Route::get('/logins', LoginPage::class)->name('login');
    Route::get('/registers', RegisterPage::class);
    Route::get('/forgot', ForgotPasswordPage::class);
    Route::get('/reset', ResetPasswordPage::class);
});



Route::middleware('auth')->group(function () {
    Route::get('/logout', function (){
        auth()->logout();
        return redirect('/');
    });
    Route::get('/checkout', CheckoutPage::class);
    Route::get('/my-orders', MyOrdersPage::class);
    Route::get('/my-orders/{order_id}', MyOrderDetailPage::class);
    Route::get('/success', SuccessPage::class)->name('success');
    Route::get('/cancel', CanelPage::class);
});


//Route::view('dashboard', 'dashboard')
//    ->middleware(['auth', 'verified'])
//    ->name('dashboard');
//
//Route::view('profile', 'profile')
//    ->middleware(['auth'])
//    ->name('profile');

//require __DIR__.'/auth.php';

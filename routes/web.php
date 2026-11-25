<?php

use App\Http\Controllers\Admin\AlbumController as AdminAlbumController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\RentalStatusController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('shop.index');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/product/{album}', [ProductController::class, 'show'])->name('product.show');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/rental/select/{album}', [RentalController::class, 'select'])->name('rental.select');
    Route::post('/rental/confirm', [RentalController::class, 'confirm'])->name('rental.confirm');

    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [PaymentController::class, 'process'])->name('checkout.process');
    Route::get('/order/{order}/status', [PaymentController::class, 'status'])->name('order.status');
    Route::get('/order/{order}/pay', [PaymentController::class, 'showPayForm'])->name('order.pay.show');
    Route::post('/order/{order}/pay', [PaymentController::class, 'uploadProof'])->name('order.pay.submit');
    Route::get('/order/{order}/pay-fine', [PaymentController::class, 'showPayFineForm'])->name('order.pay-fine.show');
    Route::post('/order/{order}/pay-fine', [PaymentController::class, 'uploadFineProof'])->name('order.pay-fine.submit');
    Route::get('/order-history', [OrderHistoryController::class, 'index'])->name('orders.history');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{album}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/{album}', [WishlistController::class, 'remove'])->name('wishlist.remove');

    Route::post('/album/{album}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::delete('/review/{review}', [ReviewController::class, 'destroy'])->name('review.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/albums/{album}/reviews', [AdminAlbumController::class, 'reviews'])->name('albums.reviews');
    Route::delete('/reviews/{review}', [AdminAlbumController::class, 'destroyReview'])->name('reviews.destroy');
    Route::resource('/albums', AdminAlbumController::class)->except(['show']);

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/accept', [AdminOrderController::class, 'accept'])->name('orders.accept');
    Route::patch('/orders/{order}/reject', [AdminOrderController::class, 'reject'])->name('orders.reject');
    Route::patch('/orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
    Route::patch('/orders/{order}/mark-paid', [AdminOrderController::class, 'markPaid'])->name('orders.mark-paid');

    Route::get('/rentals', [RentalStatusController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/{rental}', [RentalStatusController::class, 'show'])->name('rentals.show');
    Route::patch('/rentals/{rental}/verify-fine', [RentalStatusController::class, 'verifyFine'])->name('rentals.verify-fine');
    Route::patch('/rentals/{rental}', [RentalStatusController::class, 'update'])->name('rentals.update');
});

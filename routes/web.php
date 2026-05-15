<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ConsumableController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PartsRequestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group whichf
| contains the "web" middleware group. Now create something great!
|
*/



Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/service', [ServiceController::class, 'index'])->name('service');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/parts-requests', [PartsRequestController::class, 'store'])->name('parts-requests.store');
    Route::get('/consumable', [ConsumableController::class, 'index'])->name('consumable');
    Route::get('/consumable-product/{id}', [ConsumableController::class, 'show'])->name('consumable_product.show');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/news', [NewsController::class, 'index'])->name('news');
    Route::get('/new/{id}', [NewsController::class, 'show'])->name('new.details');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/career', [CareerController::class, 'index'])->name('career');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.details');
    Route::post('/product/{product}/request', [ProductController::class, 'storeRequest'])->name('product.request');
    
    Route::get('/products/{category?}/{subcategory?}', [ProductController::class, 'index'])
        ->name('products.index');
    
    
    Route::get('/brands', [BrandController::class, 'index'])->name('brands');

    Route::post('/career/apply', [CareerController::class, 'apply'])->name('career.apply');

    // Frontend Page Routes
    Route::get('/terms-and-conditions', function () {
        $page = \App\Models\Page::where('type', 1)->first();
        if (!$page) {
            return view('user.not-found');
        }
        return view('user.page', compact('page'));
    })->name('front.page.terms');

    Route::get('/privacy-policy', function () {
        $page = \App\Models\Page::where('type', 2)->first();
        if (!$page) {
            return view('user.not-found');
        }
        return view('user.page', compact('page'));
    })->name('front.page.privacy');
});

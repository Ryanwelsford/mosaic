<?php

/******************************
 *Controller includes section
 ******************************/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DatesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\WasteListController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\LogoutController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\StockOnHandController;

/******************************
 *Web routes, get, post, delete routes in use so far
 *routes can be named in order to be easily useable and prevent changes being need in multiple locations
 ******************************/

//route for homepage, should default to some form of dashboard
Route::get('/', [ProductController::class, 'home']);
Route::get('home', [ProductController::class, 'home'])->name('home');

// test route contains alot of design specific attributes
Route::get('/test', function () {
    return view('test', ["title" => "Test Page"]);
});


/******************************
 *Authorisation control
 ******************************/
//Login Controller
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authorise']);

//logout controller
Route::get('/logout', [LogoutController::class, 'index'])->name('logout.index');

/******************************
 *General area and none complete controllers
 ******************************/

//general controllers
//TODO move all of these controllers into a new section for eahc controller
Route::get('/order/home', [OrderController::class, 'home'])->name('order.home');
Route::get('/inventory/home', [InventoryController::class, 'home'])->name('inventory.home');
Route::get('/receiving/home', [ReceivingController::class, 'home'])->name('receiving.home');
Route::get('/waste/home', [WasteController::class, 'home'])->name('waste.home');
Route::get('/dates/home', [DatesController::class, 'home'])->name('dates.home');
Route::get('/StockOnHand/home', [StockOnHandController::class, 'home'])->name('soh.home');


/******************************
 *Admin routes
 ******************************/
//TODO i dont need the confirmation routes at all they are not required if anything is makes it slightly harder
//stores routes
Route::get('/store/home', [StoreController::class, 'home'])->name('store.home');
Route::get('/store/new', [StoreController::class, 'store'])->name('store.new');
Route::post('/store/new', [StoreController::class, 'save']);
Route::get('/store/view', [StoreController::class, 'view'])->name('store.view');
//Route::get('/store/confirm', [StoreController::class, 'confirm'])->name('store.confirm');
Route::delete('/store/destroy/{store}', [StoreController::class, 'destroy'])->name('store.destroy');

//Waste List routes
Route::get('/waste/list/home', [WasteListController::class, 'home'])->name('wastelist.home');
Route::get('/waste/list/new', [WasteListController::class, 'store'])->name('wastelist.new');
Route::post('/waste/list/new', [WasteListController::class, 'save']);
Route::get('/waste/list/confirmation', [WasteListController::class, 'confirm'])->name('wastelist.confirm');
Route::get('/waste/list/view', [WasteListController::class, 'view'])->name('wastelist.view');
Route::delete('/waste/list/destroy/{wastelist}', [WasteListController::class, 'destroy'])->name('wastelist.destroy');

//product routes
Route::get('/product/home', [ProductController::class, 'home'])->name('product.home');
Route::get('/product/view', [ProductController::class, 'view'])->name('product.view');
Route::get('/product/new', [ProductController::class, 'store'])->name('product.new');
Route::post('/product/new', [ProductController::class, 'save']);
Route::delete('/product/destroy/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
Route::get('/product/confirmation', [ProductController::class, 'confirm'])->name('product.confirm');

//menu routes
Route::get('/menu/home', [MenuController::class, 'home'])->name('menu.home');
Route::get('/menu/new', [MenuController::class, 'store'])->name('menu.new');
Route::post('/menu/new', [MenuController::class, 'save']);
Route::get('/menu/assign', [MenuController::class, 'assign'])->name('menu.assign');
Route::post('/menu/assign', [MenuController::class, 'assignToMenu']);
Route::get('/menu/view', [MenuController::class, 'view'])->name('menu.view');
Route::get('/menu/confirmation', [Menu::class, 'confirm'])->name('menu.confirm');
Route::delete('/menu/destroy/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');


/******************************
 *Store level routes
 ******************************/

//forecasting routes
Route::get('/forecasting/home', [ForecastingController::class, 'home'])->name('forecasting.home');
Route::get('/forecasting/date/select', [ForecastingController::class, 'dateSelect'])->name('forecasting.date');
Route::get('/forecasting/new', [ForecastingController::class, 'store'])->name('forecasting.new');



//wth is this route
Route::get('/product/confirm', [ProductController::class, 'home'])->name('product.home');

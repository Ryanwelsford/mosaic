<?php

/******************************
 *Controller includes section
 ******************************/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AdminController;
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
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\StockOnHandController;

/******************************
 *Web routes, get, post, delete routes in use so far
 *routes can be named in order to be easily useable and prevent changes being needed in multiple locations
 ******************************/

//route for homepage, should default to some form of dashboard
Route::get('home', [ProductController::class, 'home'])->name('home');

// test route contains alot of design specific attributes


/******************************
 *Authorisation control
 ******************************/
//Login Controller
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authorise']);
Route::get('/restricted', [GeneralController::class, 'restricted'])->name('general.restricted');
Route::get('/', [GeneralController::class, 'welcome'])->name('general.welcome');
//logout controller
Route::get('/logout', [LogoutController::class, 'index'])->name('logout.index');

/******************************
 *General area and none complete controllers
 ******************************/

//general controllers
//TODO move all of these controllers into a new section for eahc controller
Route::get('/dates/home', [DatesController::class, 'home'])->name('dates.home');


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
//TODO change waste list routes to wastelist/home etc
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

//admin user routes
Route::get('admin/home', [AdminController::class, 'home'])->name('admin.home');
Route::get('admin/new', [AdminController::class, 'store'])->name('admin.new');
Route::post('admin/new', [AdminController::class, 'save']);
Route::get('admin/view', [AdminController::class, 'view'])->name('admin.view');
Route::delete('admin/destroy/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');
/******************************
 *Store level routes
 ******************************/

//forecasting routes
Route::get('/forecasting/home', [ForecastingController::class, 'home'])->name('forecasting.home');
Route::get('/forecasting/date/select', [ForecastingController::class, 'dateSelect'])->name('forecasting.date');
Route::get('/forecasting/new', [ForecastingController::class, 'store'])->name('forecasting.new');
Route::post('/forecasting/new', [ForecastingController::class, 'save']);
Route::get('/forecasting/report/{date}', [ForecastingController::class, 'forecastWeekByDate'])->name('forecasting.week');
Route::get('/forecasting/monthly/report', [ForecastingController::class, 'monthly'])->name('forecasting.monthly');
Route::get('/forecasting/monthly/select', [ForecastingController::class, 'monthSelect'])->name('forecasting.monthSelect');

//order routes
Route::get('/order/home', [OrderController::class, 'home'])->name('order.home');
Route::get('/order/new', [OrderController::class, 'store'])->name('order.new');
Route::post('/order/new', [OrderController::class, 'pick']);
Route::post('/order/save', [OrderController::class, 'save'])->name("order.save");
Route::get('/order/view', [OrderController::class, 'view'])->name('order.view');
Route::get('/order/summary', [OrderController::class, 'summary'])->name('order.summary');
Route::get('/order/print', [OrderController::class, 'print'])->name('order.print');
Route::get('/order/destroy', [OrderController::class, 'view']);
Route::delete('/order/destroy/{order}', [OrderController::class, 'destroy'])->name('order.destroy');
Route::get('/order/report/select', [OrderController::class, 'weekSelect'])->name('order.weekSelect');
Route::get('/order/report/weekly', [OrderController::class, 'weeklyOrder'])->name('order.weekly');
Route::get('/order/report/monthly', [OrderController::class, 'monthlySummary'])->name('order.monthly');

//reciept routes
Route::get('/receiving/home', [ReceivingController::class, 'home'])->name('receiving.home');
Route::get('/receiving/new', [ReceivingController::class, 'store'])->name('receiving.new');
Route::post('/receiving/new', [ReceivingController::class, 'select']);
Route::post('/receiving/save', [ReceivingController::class, 'save'])->name('receiving.save');
Route::get('/receiving/view', [ReceivingController::class, 'view'])->name('receiving.view');
Route::delete('/receiving/destroy/{receipt}', [ReceivingController::class, 'destroy'])->name('receiving.destroy');
Route::get('/receiving/summary/{receipt}', [ReceivingController::class, 'summary'])->name('receiving.summary');
Route::get('/receiving/print/{receipt}', [ReceivingController::class, 'print'])->name('receiving.print');

//Stock on Hand routes

Route::get('/stockonhand/home', [StockOnHandController::class, 'home'])->name('soh.home');
Route::get('/stockonhand/new', [StockOnHandController::class, 'store'])->name('soh.new');
Route::post('/stockonhand/new', [StockOnHandController::class, 'saveCount']);
Route::get('/stockonhand/assign', [StockOnHandController::class, 'assign'])->name('soh.assign');
Route::post('/stockonhand/assign', [StockOnHandController::class, 'saveAssigned']);
Route::get('/stockonhand/view', [StockOnHandController::class, 'view'])->name('soh.view');
Route::delete('/stockonhand/destroy/{soh}', [StockOnHandController::class, 'destroy'])->name('soh.destroy');

//waste routes
Route::get('/waste/home', [WasteController::class, 'home'])->name('waste.home');
Route::get('/waste/new', [WasteController::class, 'store'])->name('waste.new');
Route::get('/waste/view', [WasteController::class, 'view'])->name('waste.view');
Route::Get('/waste/print/{waste}', [WasteController::class, "print"])->name("waste.print");
Route::Get('/waste/summary/{waste}', [WasteController::class, "summary"])->name("waste.summary");
Route::post('/waste/new', [WasteController::class, 'save']);
Route::post('/waste/category', [WasteController::class, 'categoryReturn']);
Route::delete('/waste/view/{id}', [WasteController::class, 'destroy'])->name('waste.destroy');
Route::Get('/waste/date/select', [WasteController::class, "dateSelect"])->name("waste.date");
Route::Get('/waste/weekly', [WasteController::class, "weekly"])->name("waste.weekly");

//inventory routes
Route::get('/inventory/home', [InventoryController::class, 'home'])->name('inventory.home');
Route::get('/inventory/new', [InventoryController::class, 'store'])->name('inventory.new');
Route::post('/inventory/new', [InventoryController::class, 'save']);
Route::get('/inventory/view', [InventoryController::class, 'view'])->name('inventory.view');
Route::get('/inventory/summary/{inventory}', [InventoryController::class, 'countSummary'])->name('inventory.summary');
Route::get('/inventory/latest', [InventoryController::class, 'routeToLatest'])->name('inventory.latest');
Route::get('/inventory/print/{inventory}', [InventoryController::class, 'print'])->name('inventory.print');
Route::get('/inventory/summary/{inventory}/{category}', [InventoryController::class, 'countDive'])->name('inventory.depth');
Route::delete('/inventory/view/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

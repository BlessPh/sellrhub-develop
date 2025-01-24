<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/all-routes', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'method' => $route->methods(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    });

    return response()->json($routes);
})->name('all-routes');

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// User Address
Route::middleware(['auth:sanctum'])->prefix('user')
    ->group(function () {
        Route::get('/address', '\App\Http\Controllers\AddressController@getUserAddress');
        Route::post('/address/store', '\App\Http\Controllers\AddressController@addUserAddress');
        Route::put('/address/update/{address}', '\App\Http\Controllers\AddressController@updateUserAddress');
    });

// Shop Address
Route::middleware(['auth:sanctum'])->prefix('shop')
    ->group(function () {
        Route::get('/address', '\App\Http\Controllers\AddressController@getShopAddress');
        Route::post('/address/store', '\App\Http\Controllers\AddressController@addShopAddress');
        Route::put('/address/update/{address}', '\App\Http\Controllers\AddressController@updateUserAddress');
    });

// Shops
Route::get('shops/all', '\App\Http\Controllers\Shop\ShopController@index')->name('shops.all');
Route::get('/shops/products/{shop}', '\App\Http\Controllers\Shop\ShopController@productsShow');
Route::get('/shops/reviews/{shop}', '\App\Http\Controllers\Shop\ShopController@reviewsShow');
Route::middleware(['auth:sanctum'])->prefix('shops')
    ->group(function () {
        Route::post('/store', '\App\Http\Controllers\Shop\ShopController@store');
        Route::get('/orders/{shop}', '\App\Http\Controllers\Shop\ShopController@ordersShow');
    });

// Following Shops
Route::middleware('auth:sanctum')->prefix('/shop')
    ->group(function () {
        Route::post('/follow/{shop}', '\App\Http\Controllers\FollowController@follow');
        Route::delete('unfollow/{shop}', '\App\Http\Controllers\FollowController@unfollow');
        Route::get('/following', '\App\Http\Controllers\FollowController@getFollowingShops');
        Route::get('/followers/{shop}', '\App\Http\Controllers\FollowController@getFollowerShops')->middleware('seller');
    });

// Promotions
Route::middleware('auth:sanctum')->prefix('/promotions')
    ->group(function () {
        Route::get('/all', 'App\Http\Controllers\PromotionController@index');
        Route::get('/products', 'App\Http\Controllers\PromotionController@getProductsOnPromotion');
    });

Route::middleware(['auth:sanctum', 'seller', ''])->prefix('/promotions')
    ->group(function () {
        Route::post('/create', 'App\Http\Controllers\PromotionController@store')->name('promotion.create');
    });

// Products
Route::get('/products', 'App\Http\Controllers\ProductController@index');
Route::get('/product/{product}', 'App\Http\Controllers\ProductController@show');
Route::get('/product/reviews/{product}', 'App\Http\Controllers\ProductController@reviewsShow');
Route::get('/products/{product}/recommendations', 'App\Http\Controllers\ProductController@getRecommendedProducts');
Route::middleware(['auth:sanctum', 'seller'])
    ->prefix('/product')->group(function () {
        Route::post('/store', 'App\Http\Controllers\ProductController@store')->name('product.store');
        Route::put('/{product}', 'App\Http\Controllers\ProductController@update')->name('product.update');
        Route::delete('/{product}', 'App\Http\Controllers\ProductController@destroy')->name('product.destroy');
});

// Categories
Route::get('/categories', 'App\Http\Controllers\CategoryController@index');
Route::get('/category/{category}', 'App\Http\Controllers\CategoryController@show');
Route::middleware(['auth:sanctum', 'super_admin'])
    ->prefix('category')->group(function () {
        Route::post('/store', 'App\Http\Controllers\CategoryController@store')->name('category.store');
        Route::put('/{category}', 'App\Http\Controllers\CategoryController@update')->name('category.update');
        Route::delete('/{category}', 'App\Http\Controllers\CategoryController@destroy')->name('category.destroy');
    });
Route::get('/getProductsByCategory', 'App\Http\Controllers\CategoryController@getProductsByCategory');
Route::get('/categories_products', 'App\Http\Controllers\CategoryController@categoriesWithProducts');

// Search
Route::post('/search', 'App\Http\Controllers\SearchController@search');

Route::middleware('auth:sanctum')->prefix('/wishlist')
    ->group(function () {
        Route::get('/all', 'App\Http\Controllers\WishlistController@index');
        Route::post('/add', 'App\Http\Controllers\WishlistController@add');
        Route::delete('/remove/{wishlist}', 'App\Http\Controllers\WishlistController@remove');
    });

// Cart
Route::prefix('cart')->group(function () {
    Route::get('/{id}', 'App\Http\Controllers\Cart\CartController@index');
    Route::post('/add', 'App\Http\Controllers\Cart\CartController@addToCart');
    Route::post('/remove/{cartItemId}', 'App\Http\Controllers\Cart\CartController@removeItem');
});

// Orders
Route::middleware(['auth:sanctum'])->prefix('orders')
    ->group(function () {
        Route::get('/get', 'App\Http\Controllers\Order\OrderController@index')->name('orders.index');
        Route::get('/show/{order}', 'App\Http\Controllers\Order\OrderController@show')->name('order.show');
        Route::post('/store', 'App\Http\Controllers\Order\OrderController@store')->name('order.store');
        Route::delete('/delete/{order}', 'App\Http\Controllers\Order\OrderController@destroy')->name('order.destroy');

        // Notifications
        Route::get('/notifications/all', 'App\Http\Controllers\SellerAdminController@notifications')->name('notifications.all');
        Route::get('/notifications/unread', 'App\Http\Controllers\SellerAdminController@unreadNotifications')->name('notifications.unread');
        Route::get('/notifications/mark-as-read/{notification}', 'App\Http\Controllers\SellerAdminController@markNotificationAsRead')->name('order.markNotificationAsRead');

        // Ship Order
        Route::post('/ship/{order}', 'App\Http\Controllers\Order\OrderController@changeOrderStatus')->name('order.ship');
        Route::post('/deliver/{order}', 'App\Http\Controllers\Order\OrderController@changeOrderStatus')->name('order.deliver');
        Route::post('/received/{order}', 'App\Http\Controllers\Order\OrderController@changeOrderStatus')->name('order.received');
        Route::post('/return/{order}', 'App\Http\Controllers\Order\OrderController@changeOrderStatus')->name('order.return');
        Route::post('/refund/{order}', 'App\Http\Controllers\Order\OrderController@changeOrderStatus')->name('order.refund');
        Route::post('/cancel/{order}', 'App\Http\Controllers\Order\OrderController@changeOrderStatus')->name('order.cancel');
    });

// Reviews
Route::middleware(['auth:sanctum'])->prefix('reviews')
    ->group(function () {
        Route::post('/store', 'App\Http\Controllers\Review\ReviewController@store');
    });

// Roles
Route::middleware(['auth:sanctum', 'super_admin'])
    ->prefix('roles')->group(function () {
        Route::get('/all', 'App\Http\Controllers\RoleController@index')->name('roles.index');
        Route::post('/store', 'App\Http\Controllers\RoleController@store')->name('roles.store');
        Route::put('/{role}', 'App\Http\Controllers\RoleController@update')->name('roles.update');
        Route::delete('/{role}', 'App\Http\Controllers\RoleController@destroy')->name('roles.destroy');
        Route::post('/givePermission', 'App\Http\Controllers\RoleController@givePermission')->name('roles.givePermission');
    });

// Permissions
Route::middleware(['auth:sanctum', 'super_admin'])
    ->prefix('permissions')->group(function () {
        Route::get('/', 'App\Http\Controllers\PermissionController@index')->name('permissions.index');
        Route::post('/store', 'App\Http\Controllers\PermissionController@store')->name('permissions.store');
        Route::post('/update/{permission}', 'App\Http\Controllers\PermissionController@update')->name('permissions.update');
        Route::post('/destroy/{permission}', 'App\Http\Controllers\PermissionController@destroy')->name('permissions.destroy');
    });

// Admin dashboard
Route::middleware(['auth:sanctum', 'super_admin'])
    ->prefix('/dashboard')->group(function () {
        Route::get('/customers', 'App\Http\Controllers\AdminDashboardController@totalCustomers');
        Route::get('/total-sales', 'App\Http\Controllers\AdminDashboardController@totalSales');
        Route::get('/total-orders', 'App\Http\Controllers\AdminDashboardController@totalOrders');
        Route::get('total-products', 'App\Http\Controllers\AdminDashboardController@totalProducts');
        Route::get('/recent-orders', 'App\Http\Controllers\AdminDashboardController@recentOrders');
        Route::get('/recent-products', 'App\Http\Controllers\AdminDashboardController@recentProducts');
        Route::get('/recent-sellers', 'App\Http\Controllers\AdminDashboardController@recentSellers');
    });

Route::resource('product_route', 'App\Http\Controllers\ProductController');

// Subscriptions & Plans
Route::middleware(['auth:sanctum'])->prefix('/subscription')
    ->group(function () {
        Route::get('/plans', 'App\Http\Controllers\PlanController@index');
    });


require __DIR__.'/auth.php';

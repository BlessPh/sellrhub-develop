<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function totalSales()
    {
        $sales = [
            'today' => Order::whereDate('created_at', Carbon::today())->sum('total_price'),
            'weekly' => Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price'),
            'monthly' => Order::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_price'),
            'yearly' => Order::whereYear('created_at', Carbon::now()->year)->sum('total_price'),
        ];
        return response()->json($sales);
    }

    public function getSales(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $sales = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_price');

        return response()->json([
            'sales' => $sales,
        ]);
    }

    public function daySales()
    {
        $dailySales = Order::selectRaw('DATE(created_at) as date, SUM(total_price) as total_price')
            ->groupBy('date')
            ->get();

        return response()->json([
            'sales' => $dailySales,
        ]);
    }

    public function weeklySales()
    {
        $weeklySales = Order::selectRaw('WEEK(created_at) as week, SUM(total_price) as total_price')
            ->groupBy('week')->get();

        return response()->json([
            'sales' => $weeklySales,
        ]);
    }

    public function monthlySales()
    {
        $monthlySales = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_price) as total_price')
            ->groupBy('year', 'month')->get();

        return response()->json([
            'sales' => $monthlySales,
        ]);
    }

    public function yearlySales()
    {
        $yearlySales = Order::selectRaw('YEAR(created_at) as year, SUM(total_price) as total_price')
            ->groupBy('year')->get();

        return response()->json([
            'sales' => $yearlySales,
        ]);
    }

    public function salesByCategory(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $salesByCategory = OrderItem::with('product.category')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()->groupBy(function ($orderItem) {
                return $orderItem->product->category->name;
            })->map(function ($group) {
                return $group->reduce(function ($carry, $orderItem) {
                    return $carry + ($orderItem->quantity * $orderItem->unit_price);
                }, 0);
            });

        return response()->json([
            'status' => 'success',
            'salesByCategory' => $salesByCategory,
        ]);
    }

    public function topSellingProducts(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $topSellingProducts = OrderItem::with('product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()->groupBy(function ($orderItem) {
                return $orderItem->product->name;
            })->map(function ($group) {
                return $group->reduce(function ($carry, $orderItem) {
                    return $carry + $orderItem->quantity;
                }, 0);
            })->sortDesc()->take(10);

        return response()->json([
            'status' => 'success',
            'data' => $topSellingProducts,
        ]);
    }

    public function averageOrderValue(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_price');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return response()->json([
            'status' => 'success',
            'data' => $averageOrderValue
        ]);
    }

    public function bestSalesDay()
    {
        $bestSaleDay = Order::selectRaw('DATE(created_at) as date, SUM(total_price) as total_sales')
            ->groupBy('date')
            ->orderByDesc('SUM(total_price)')->first();

        return response()->json([
            'status' => 'success',
            'data' => $bestSaleDay
        ]);
    }

    public function bestSalesWeek()
    {
        $bestSaleWeek = Order::selectRaw('YEARWEEK(created_at, 1) as week, SUM(total_price) as total_sales')
            ->groupBy('week')->orderByDesc('SUM(total_price)')->first();

        return response()->json([
            'status' => 'success',
            'data' => $bestSaleWeek
        ]);
    }

    public function bestSalesMonth()
    {
        $bestSaleMonth = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total_sales')
            ->groupBy('month')->orderByDesc('SUM(total_price)')->first();

        return response()->json([
            'status' => 'success',
            'data' => $bestSaleMonth
        ]);
    }

    public function bestSalesPeriod(Request $request)
    {
        $period = $request->input('period', 'day');

        $groupBy = match ($period) {
            'month' => 'DATE_FORMAT(created_at, "%Y-%m")',
            'year' => 'YEAR(created_at)',
            'week' => 'YEARWEEK(created_at, 1)',
            default => 'DATE(created_at)',
        };

        $bestPeriod = Order::selectRaw("$groupBy as period, SUM(total_price) as total_sales")
            ->groupBy('period')
            ->orderByDesc('SUM(total_price)')
            ->first();

        return response()->json([
            'status' => 'success',
            'best_period' => $bestPeriod,
            'grouped_by' => $period,
        ]);
    }

    public function outOfStockProducts()
    {
        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)
            ->with(['category', 'shop'])
            ->get();

        return response()->json([
            'status' => 'success',
            'out_of_stock_products' => $outOfStockProducts,
        ]);
    }

    public function lowStockProducts()
    {
        $threshold = 10;
        $lowStockProducts = Product::where('product_quantity', '<=', $threshold)
            ->with(['category', 'shop'])
            ->get();

        return response()->json([
            'status' => 'success',
            'low_stock_products' => $lowStockProducts,
        ]);
    }

    public function topRatedProducts()
    {
        $topRatedProducts = Product::withAvg('reviews', 'rating')
            ->with(['category', 'shop'])
            ->orderByDesc('reviews_avg_rating')
            ->take(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'top_rated_products' => $topRatedProducts,
        ]);
    }

    public function topRatedProductsByCategory($categoryId)
    {
        $topRatedProducts = Product::where('category_id', $categoryId)
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'top_rated_products' => $topRatedProducts,
        ]);
    }

    public function promotedProducts()
    {
        $promotedProducts = Product::whereHas('promotions', function ($query) {
            $query->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        })
            ->with('promotions')
            ->get();

        return response()->json([
            'status' => 'success',
            'promoted_products' => $promotedProducts->map(function ($product) {
                return [
                    'product_name' => $product->product_name,
                    'original_price' => $product->product_price,
                    'promotional_price' => $product->promotions()->first()->pivot->promotional_price ?? null,
                    'promotion_start' => $product->promotions()->first()->start_date ?? null,
                    'promotion_end' => $product->promotions()->first()->end_date ?? null,
                ];
            }),
        ]);
    }

    public function orderStatistics()
    {
        $orderStatistics = Order::selectRaw("
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'preparing' THEN 1 ELSE 0 END) as preparing_orders,
        SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
        SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled_orders
        SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders
        SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned_orders

    ")->first();

        return response()->json([
            'status' => 'success',
            'statistics' => $orderStatistics,
        ]);
    }

    public function categoryStatistics()
    {
        $categoryStatistics = Category::select('category_name')
            ->withCount('products')->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('categories.category_name as category_name, SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            ->groupBy('categories.id', 'categories.category_name')
            ->get()->map(function ($category) {
                return [
                    'category_name' => $category->category_name,
                    'products_count' => $category->products_count ?? 0,
                    'total_revenue' => $category->total_revenue ?? 0,
                ];
            });

        return response()->json([
            'status' => 'success',
            'category_statistics' => $categoryStatistics,
        ]);
    }

    public function totalProducts()
    {
        return Product::all()->count();
    }

    public function recentOrders()
    {
        return Order::orderBy('created_at', 'desc')->take(10)->get();
    }

    public function orderInDelivery()
    {
        $orderInDelivery = Order::where('status', 'shipped')
            ->orWhere('status', 'pending')->with('user')->get();

        return response()->json([
            'status' => 'success',
            'order_in_delivery' => $orderInDelivery,
        ]);
    }

    public function getAverageDeliveryTime()
    {
        $distribution = Order::where('status', 'delivered')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, delivered_at)) as average_delivery_time')
            ->value('average_delivery_time');
    }

    public function recentProducts()
    {
        return Product::orderBy('created_at', 'desc')->take(10)->get();
    }

    public function totalCustomers()
    {
        return User::role('customer')->count();
    }

    public function activeCustomers()
    {
        $activeCustomers = User::whereHas('orders')->count();

        return response()->json([
            'active_customers' => $activeCustomers,
        ]);
    }

    public function newCustomers()
    {
        $dailyNewCustomers = User::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')->get();

        $weeklyNewCustomers = User::selectRaw('YEARWEEK(created_at) as week, COUNT(*) as total')
            ->groupBy('week')->get();

        $monthlyNewCustomers = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')->get();

        $yearlyNewCustomers = User::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
            ->groupBy('year')->get();

        return response()->json([
            'daily' => $dailyNewCustomers,
            'weekly' => $weeklyNewCustomers,
            'monthly' => $monthlyNewCustomers,
            'yearly' => $yearlyNewCustomers,
        ]);
    }

    public function topSpendingCustomers()
    {
        $topSpendingCustomers = User::join('orders', 'users.id', '=', 'orders.user_id')
            ->selectRaw('users.id, users.firstname, SUM(orders.total_price) as total_spent')
            ->groupBy('users.id', 'users.firstname')
            ->orderByDesc('total_spent')->limit(10)->get();

        return response()->json([
            'status' => 'success',
            'top_customers' => $topSpendingCustomers,
        ]);
    }

    public function mostFrequentCustomers()
    {
        $frequentCustomers = User::join('orders', 'users.id', '=', 'orders.user_id')
            ->selectRaw('users.id, users.firstname, COUNT(orders.id) as total_orders')
            ->groupBy('users.id', 'users.firstname')
            ->orderByDesc('total_orders')->limit(10)->get();

        return response()->json([
            'status' => 'success',
            'frequent_customers' => $frequentCustomers,
        ]);
    }

    public function totalSellers()
    {
        $totalSellers = User::role('seller')->count();

        return response()->json([
            'status' => 'success',
            'total_sellers' => $totalSellers,
        ]);
    }

    public function recentSellers()
    {
        $recentSellers = User::role('seller')->with('shop')->whereHas('shop', function ($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        })->get();

        return response()->json([
            'status' => 'success',
            'recent_sellers' => $recentSellers,
        ]);
    }

    public function averageOrderProcessingTime()
    {
        $averageTime = Order::whereIn('status', ['preparing', 'paid'])
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_processing_time')
            ->value('avg_processing_time');

        return response()->json([
            'status' => 'success',
            'average_time' => $averageTime . 'minutes',
        ]);
    }

    public function paymentMethodUsage()
    {
        $paymentMethods = Order::select('payment_methods.name as payment_method', DB::raw('COUNT(*) as usage_count'))
            ->groupBy('payment_methods.name')->orderByDesc('usage_count')->get();

        return response()->json([
            'status' => 'success',
            'payment_methods' => $paymentMethods,
        ]);
    }

    public function transactionStats()
    {
        $successCount = Order::where('status', '=', 'success')->count();
        $failedCount = Order::where('status', '=', 'failed')->count();

        return response()->json([
            'success_count' => $successCount,
            'failed_count' => $failedCount,
        ]);
    }

    public function pendingPayments()
    {
        $pendingPayments = Order::where('status', '=', 'pending')->count();

        return response()->json([
            'status' => 'success',
            'pending_payments' => $pendingPayments,
        ]);
    }

    public function paymentHistoryByCustomer($userId)
    {
        $customer = User::role('customer')->findOrFail($userId);

        $paymentHistory = $customer->orders()->select('id', 'total_price', 'status', 'created_at', 'updated_at')
            ->orderBy('created_at')->get();

        return response()->json([
            'status' => 'success',
            'customer' => $customer->firstname . ' ' . $customer->lastname,
            'payment_history' => $paymentHistory,
        ]);
    }

    public function paymentHistoryBySeller($userId)
    {
        $subscription = Subscription::wherehas('users', function ($query) use ($userId) {
            $query->where('id', $userId)->whereHas('roles', function ($query) {
                $query->where('name', 'seller');
            });
        })->with('plan', 'paymentMethod')->get();

        return response()->json([
            'status' => 'success',
            'subscription' => $subscription,
        ]);
    }
}

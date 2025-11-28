<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request, default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $startOfPeriod = Carbon::parse($startDate)->startOfDay();
        $endOfPeriod = Carbon::parse($endDate)->endOfDay();

        // Calculate previous period duration for comparison
        $periodDays = $startOfPeriod->diffInDays($endOfPeriod) + 1;
        $previousPeriodEnd = $startOfPeriod->copy()->subDay();
        $previousPeriodStart = $previousPeriodEnd->copy()->subDays($periodDays - 1)->startOfDay();

        // Period Statistics
        $totalSales = (float) Sale::whereBetween('sale_date', [$startOfPeriod, $endOfPeriod])
            ->sum('total_amount');

        $totalTransactions = (int) Sale::whereBetween('sale_date', [$startOfPeriod, $endOfPeriod])
            ->count();

        $totalProductsSold = (int) SaleItem::whereHas('sale', function ($q) use ($startOfPeriod, $endOfPeriod) {
            $q->whereBetween('sale_date', [$startOfPeriod, $endOfPeriod]);
        })->sum('quantity');

        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Previous period statistics for comparison
        $previousPeriodSales = (float) Sale::whereBetween('sale_date', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('total_amount');

        $previousPeriodTransactions = (int) Sale::whereBetween('sale_date', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $previousPeriodProducts = (int) SaleItem::whereHas('sale', function ($q) use ($previousPeriodStart, $previousPeriodEnd) {
            $q->whereBetween('sale_date', [$previousPeriodStart, $previousPeriodEnd]);
        })->sum('quantity');

        // Calculate percentage changes
        $salesChange = $previousPeriodSales > 0
            ? (($totalSales - $previousPeriodSales) / $previousPeriodSales) * 100
            : ($totalSales > 0 ? 100 : 0);

        $transactionsChange = $previousPeriodTransactions > 0
            ? (($totalTransactions - $previousPeriodTransactions) / $previousPeriodTransactions) * 100
            : ($totalTransactions > 0 ? 100 : 0);

        $productsChange = $previousPeriodProducts > 0
            ? (($totalProductsSold - $previousPeriodProducts) / $previousPeriodProducts) * 100
            : ($totalProductsSold > 0 ? 100 : 0);

        // Daily sales data for chart
        $dailySales = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$startOfPeriod, $endOfPeriod])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare chart data (fill missing dates with 0)
        $chartLabels = [];
        $chartData = [];
        $currentDate = $startOfPeriod->copy();

        while ($currentDate <= $endOfPeriod) {
            $dateStr = $currentDate->format('Y-m-d');
            $chartLabels[] = $currentDate->format('d/m');

            $dayData = $dailySales->firstWhere('date', $dateStr);
            $chartData[] = $dayData ? (float) $dayData->total : 0;

            $currentDate->addDay();
        }

        // All selling products sorted by quantity
        $topProducts = SaleItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('sale', function ($q) use ($startOfPeriod, $endOfPeriod) {
                $q->whereBetween('sale_date', [$startOfPeriod, $endOfPeriod]);
            })
            ->with('product.category')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->get();

        // Sales by category
        $salesByCategory = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->join('products', 'sale_items.product_id', '=', 'products.product_id')
            ->join('categories', 'products.categories_id', '=', 'categories.categories_id')
            ->select(
                'categories.categories_id',
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->whereBetween('sales.sale_date', [$startOfPeriod, $endOfPeriod])
            ->groupBy('categories.categories_id', 'categories.name')
            ->orderByDesc('total_quantity')
            ->get();

        return view('reports.sales', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalTransactions',
            'totalProductsSold',
            'averageTransaction',
            'salesChange',
            'transactionsChange',
            'productsChange',
            'chartLabels',
            'chartData',
            'topProducts',
            'salesByCategory',
            'startOfPeriod',
            'endOfPeriod'
        ));
    }

    public function exportPdf(Request $request)
    {
        // Get date range from request
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $startOfPeriod = Carbon::parse($startDate)->startOfDay();
        $endOfPeriod = Carbon::parse($endDate)->endOfDay();

        // Calculate previous period for comparison
        $periodDays = $startOfPeriod->diffInDays($endOfPeriod) + 1;
        $previousPeriodEnd = $startOfPeriod->copy()->subDay();
        $previousPeriodStart = $previousPeriodEnd->copy()->subDays($periodDays - 1)->startOfDay();

        // Period Statistics
        $totalSales = (float) Sale::whereBetween('sale_date', [$startOfPeriod, $endOfPeriod])
            ->sum('total_amount');

        $totalTransactions = (int) Sale::whereBetween('sale_date', [$startOfPeriod, $endOfPeriod])
            ->count();

        $totalProductsSold = (int) SaleItem::whereHas('sale', function ($q) use ($startOfPeriod, $endOfPeriod) {
            $q->whereBetween('sale_date', [$startOfPeriod, $endOfPeriod]);
        })->sum('quantity');

        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Previous period statistics for comparison
        $previousPeriodSales = (float) Sale::whereBetween('sale_date', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('total_amount');

        $previousPeriodTransactions = (int) Sale::whereBetween('sale_date', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $previousPeriodProducts = (int) SaleItem::whereHas('sale', function ($q) use ($previousPeriodStart, $previousPeriodEnd) {
            $q->whereBetween('sale_date', [$previousPeriodStart, $previousPeriodEnd]);
        })->sum('quantity');

        // Calculate percentage changes
        $salesChange = $previousPeriodSales > 0
            ? (($totalSales - $previousPeriodSales) / $previousPeriodSales) * 100
            : ($totalSales > 0 ? 100 : 0);

        $transactionsChange = $previousPeriodTransactions > 0
            ? (($totalTransactions - $previousPeriodTransactions) / $previousPeriodTransactions) * 100
            : ($totalTransactions > 0 ? 100 : 0);

        $productsChange = $previousPeriodProducts > 0
            ? (($totalProductsSold - $previousPeriodProducts) / $previousPeriodProducts) * 100
            : ($totalProductsSold > 0 ? 100 : 0);

        // All selling products sorted by quantity
        $topProducts = SaleItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('sale', function ($q) use ($startOfPeriod, $endOfPeriod) {
                $q->whereBetween('sale_date', [$startOfPeriod, $endOfPeriod]);
            })
            ->with('product.category')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->get();

        // Sales by category
        $salesByCategory = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->join('products', 'sale_items.product_id', '=', 'products.product_id')
            ->join('categories', 'products.categories_id', '=', 'categories.categories_id')
            ->select(
                'categories.categories_id',
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->whereBetween('sales.sale_date', [$startOfPeriod, $endOfPeriod])
            ->groupBy('categories.categories_id', 'categories.name')
            ->orderByDesc('total_quantity')
            ->get();

        // Daily sales for the period
        $dailySales = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$startOfPeriod, $endOfPeriod])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $periodLabel = $startOfPeriod->format('d/m/Y') . ' - ' . $endOfPeriod->format('d/m/Y');

        $data = compact(
            'startDate',
            'endDate',
            'periodLabel',
            'totalSales',
            'totalTransactions',
            'totalProductsSold',
            'averageTransaction',
            'salesChange',
            'transactionsChange',
            'productsChange',
            'topProducts',
            'salesByCategory',
            'dailySales',
            'startOfPeriod',
            'endOfPeriod'
        );

        $pdf = Pdf::loadView('reports.sales-report-pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Penjualan-' . $startDate . '-to-' . $endDate . '.pdf');
    }
}

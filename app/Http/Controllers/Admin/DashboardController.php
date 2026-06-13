<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\InventoryItem;
use App\Models\NotificationMbg;
use App\Services\InventarisService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly InventarisService $service
    ) {}

    public function index()
    {
        $rekap = $this->service->rekapStok();

        // Recent transactions
        $recentTransactions = \App\Models\StockTransaction::with(['item', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent audit logs
        $recentLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Items needing reorder
        $lowStockItems = InventoryItem::active()
            ->lowStock()
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'rekap', 'recentTransactions', 'recentLogs', 'lowStockItems'
        ));
    }
}

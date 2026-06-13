<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Services\InventarisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventarisController extends Controller
{
    public function __construct(
        private readonly InventarisService $service
    ) {}

    /**
     * GET /admin/inventaris - Halaman utama inventaris dengan filter & search
     */
    public function index(Request $request)
    {
        $query = InventoryItem::query()->with('purchaseLimit');

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        // Filter status
        $status = $request->get('status', 'active');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter stok
        if ($stokFilter = $request->get('stok_filter')) {
            match($stokFilter) {
                'rendah' => $query->whereColumn('stock', '<', 'reorder_point')->where('stock', '>', 0),
                'habis'  => $query->where('stock', 0),
                'normal' => $query->whereColumn('stock', '>=', 'reorder_point'),
                default  => null,
            };
        }

        // Sort
        $sortBy  = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');
        $allowed = ['name', 'sku', 'stock', 'unit_price', 'category', 'status'];
        if (in_array($sortBy, $allowed)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        $items   = $query->paginate(25)->withQueryString();
        $rekap   = $this->service->rekapStok();
        $categories = config('mbg.categories');

        return view('admin.inventaris.index', compact('items', 'rekap', 'categories'));
    }

    /**
     * GET /admin/inventaris/create - Form tambah item baru
     */
    public function create()
    {
        $this->authorizeManage();
        return view('admin.inventaris.create');
    }

    /**
     * POST /admin/inventaris - Simpan item baru
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'sku'           => 'required|string|max:20|unique:inventory_items,sku',
            'name'          => 'required|string|max:150',
            'category'      => 'required|string|in:' . implode(',', config('mbg.categories')),
            'supplier'      => 'required|string|max:150',
            'unit'          => 'required|string|in:' . implode(',', config('mbg.units')),
            'unit_price'    => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'max_stock'     => 'nullable|numeric|min:0|gte:reorder_point',
            'stock'         => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:500',
        ]);

        $item = InventoryItem::create([
            ...$data,
            'currency'   => 'IDR',
            'status'     => 'active',
            'created_by' => auth()->id(),
        ]);

        AuditLog::catat($item, 'created', [], $data);

        return redirect()->route('admin.inventaris.show', $item)
            ->with('success', "Item \"{$item->name}\" berhasil ditambahkan.");
    }

    /**
     * GET /admin/inventaris/{item} - Detail item dengan tabs
     */
    public function show(InventoryItem $item, Request $request)
    {
        $tab = $request->get('tab', 'transaksi');

        $transactions = $item->transactions()
            ->with('creator')
            ->orderBy('transacted_at', 'desc')
            ->paginate(20, ['*'], 'tx_page');

        $auditLogs = AuditLog::where('loggable_type', 'InventoryItem')
            ->where('loggable_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'log_page');

        $maxPembelian  = $this->service->hitungMaxPembelian($item);
        $purchaseLimit = $item->purchaseLimit;

        return view('admin.inventaris.show', compact(
            'item', 'tab', 'transactions', 'auditLogs', 'maxPembelian', 'purchaseLimit'
        ));
    }

    /**
     * GET /admin/inventaris/{item}/edit - Form edit item
     */
    public function edit(InventoryItem $item)
    {
        $this->authorizeManage();
        return view('admin.inventaris.edit', compact('item'));
    }

    /**
     * PUT /admin/inventaris/{item} - Update item
     */
    public function update(Request $request, InventoryItem $item): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'category'      => 'required|string|in:' . implode(',', config('mbg.categories')),
            'supplier'      => 'required|string|max:150',
            'unit'          => 'required|string|in:' . implode(',', config('mbg.units')),
            'unit_price'    => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'max_stock'     => 'nullable|numeric|min:0|gte:reorder_point',
            'status'        => 'required|in:active,backordered,discontinued',
            'notes'         => 'nullable|string|max:500',
        ]);

        $oldValues = $item->only(array_keys($data));
        $item->update($data);
        AuditLog::catat($item, 'updated', $oldValues, $data);

        return redirect()->route('admin.inventaris.show', $item)
            ->with('success', "Item \"{$item->name}\" berhasil diperbarui.");
    }

    /**
     * DELETE /admin/inventaris/{item} - Hapus item (BR-06)
     */
    public function destroy(InventoryItem $item): RedirectResponse
    {
        $this->authorizeManage();

        // BR-06: Tidak boleh hapus jika ada transaksi
        if ($item->hasTransactions()) {
            return redirect()->back()
                ->with('error', "Item \"{$item->name}\" tidak bisa dihapus karena memiliki riwayat transaksi. Gunakan \"Nonaktifkan\" untuk menonaktifkan item.");
        }

        $name = $item->name;
        AuditLog::catat($item, 'deleted', $item->toArray(), []);
        $item->delete();

        return redirect()->route('admin.inventaris.index')
            ->with('success', "Item \"{$name}\" berhasil dihapus.");
    }

    /**
     * POST /admin/inventaris/{item}/nonaktifkan - Nonaktifkan item
     */
    public function nonaktifkan(InventoryItem $item): RedirectResponse
    {
        $this->authorizeManage();

        $item->update(['status' => 'discontinued']);
        AuditLog::catat($item, 'updated', ['status' => 'active'], ['status' => 'discontinued']);

        return redirect()->back()
            ->with('success', "Item \"{$item->name}\" berhasil dinonaktifkan.");
    }

    /**
     * POST /admin/inventaris/{item}/stok-masuk - Catat stok masuk
     */
    public function stokMasuk(Request $request, InventoryItem $item): JsonResponse
    {
        if (!auth()->user()->canTransact()) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki izin.'], 403);
        }

        $data = $request->validate([
            'quantity'      => 'required|numeric|min:0.001|max:99999',
            'unit_price'    => 'nullable|numeric|min:0',
            'reason'        => 'required|in:purchase,adjustment,return,other',
            'supplier'      => 'nullable|string|max:150',
            'reference_no'  => 'nullable|string|max:50',
            'notes'         => 'nullable|string|max:500',
            'transacted_at' => 'required|date|before_or_equal:today',
        ]);

        try {
            $tx = $this->service->prosesStokMasuk($item, $data);
            $item->refresh();

            return response()->json([
                'success' => true,
                'message' => "Stok masuk berhasil dicatat. {$item->name} +{$data['quantity']} {$item->unit} (total: {$item->stock} {$item->unit})",
                'data'    => [
                    'item_id'        => $item->id,
                    'stock_before'   => (float) $tx->stock_before,
                    'stock_after'    => (float) $tx->stock_after,
                    'transaction_id' => $tx->id,
                    'new_stock'      => (float) $item->stock,
                ],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /admin/inventaris/{item}/stok-keluar - Catat stok keluar
     */
    public function stokKeluar(Request $request, InventoryItem $item): JsonResponse
    {
        if (!auth()->user()->canTransact()) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki izin.'], 403);
        }

        $data = $request->validate([
            'quantity'      => 'required|numeric|min:0.001',
            'reason'        => 'required|in:cooking,waste,adjustment,return,other',
            'notes'         => 'nullable|string|max:500',
            'transacted_at' => 'required|date|before_or_equal:today',
        ]);

        try {
            $tx = $this->service->prosesStokKeluar($item, $data);
            $item->refresh();

            return response()->json([
                'success' => true,
                'message' => "Stok keluar berhasil dicatat. {$item->name} -{$data['quantity']} {$item->unit} (sisa: {$item->stock} {$item->unit})",
                'data'    => [
                    'item_id'        => $item->id,
                    'stock_before'   => (float) $tx->stock_before,
                    'stock_after'    => (float) $tx->stock_after,
                    'transaction_id' => $tx->id,
                    'new_stock'      => (float) $item->stock,
                    'is_low_stock'   => $item->is_low_stock,
                ],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * GET /admin/inventaris/{item}/info-kebutuhan - Info batas pembelian (AJAX)
     */
    public function infoKebutuhan(InventoryItem $item): JsonResponse
    {
        $maxPembelian  = $this->service->hitungMaxPembelian($item);
        $purchaseLimit = $item->purchaseLimit()->first();

        return response()->json([
            'item_id'         => $item->id,
            'name'            => $item->name,
            'sku'             => $item->sku,
            'stock'           => (float) $item->stock,
            'unit'            => $item->unit,
            'unit_price'      => (float) $item->unit_price,
            'reorder_point'   => (float) $item->reorder_point,
            'max_stock'       => $item->max_stock ? (float) $item->max_stock : null,
            'status'          => $item->status,
            'max_purchase'    => $maxPembelian,
            'daily_need'      => $purchaseLimit ? (float) $purchaseLimit->daily_need : 0,
            'planning_days'   => $purchaseLimit ? $purchaseLimit->planning_days : 7,
            'calculated_need' => $purchaseLimit ? (float) $purchaseLimit->calculated_need : 0,
        ]);
    }

    /**
     * GET /admin/inventaris/export - Export inventaris (Print to PDF/HTML)
     */
    public function export()
    {
        if (!auth()->user()->canExport()) {
            abort(403);
        }

        $data = $this->service->exportInventarisCsv();
        return view('admin.inventaris.export-pdf', compact('data'));
    }

    /**
     * GET /admin/inventaris/export-transaksi - Export transaksi (Print to PDF/HTML)
     */
    public function exportTransaksi(Request $request)
    {
        if (!auth()->user()->canExport()) {
            abort(403);
        }

        $filters = $request->only(['from', 'to', 'item_id', 'type']);
        $data    = $this->service->exportTransaksiCsv($filters);

        return view('admin.inventaris.export-transaksi-pdf', compact('data', 'filters'));
    }

    private function authorizeManage(): void
    {
        if (!auth()->user()->canManageItems()) {
            abort(403, 'Hanya Admin Program atau Super Admin yang dapat mengelola data item.');
        }
    }
}

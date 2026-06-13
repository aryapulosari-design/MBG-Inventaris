@extends('layouts.admin')

@section('title', 'Inventaris Bahan Baku')
@section('page-title', 'Inventaris Bahan Baku')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inventaris</li>
@endsection

@section('content')
<div class="fade-in-up">

{{-- ══════ SUMMARY CARDS ══════ --}}
<div class="row g-3 mb-4">
    {{-- Total SKU Aktif --}}
    <div class="col-6 col-md-3 fade-in-up fade-in-up-1">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total SKU Aktif</div>
                    <div class="stat-value text-primary">{{ $rekap['total_sku_aktif'] }}</div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">item bahan baku</div>
                </div>
                <div class="stat-icon" style="background:#EBF4FF;color:var(--mbg-primary)">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Perlu Reorder --}}
    <div class="col-6 col-md-3 fade-in-up fade-in-up-2">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Perlu Reorder</div>
                    <div class="stat-value {{ $rekap['perlu_reorder'] > 0 ? 'text-warning' : 'text-success' }}">
                        {{ $rekap['perlu_reorder'] }}
                    </div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">item stok rendah</div>
                </div>
                <div class="stat-icon" style="background:#FFFBEB;color:#D97706">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
            @if($rekap['perlu_reorder'] > 0)
                <div class="mt-2 pt-2 border-top" style="font-size:.75rem;color:#D97706">
                    <i class="bi bi-arrow-right-circle"></i> Segera lakukan reorder
                </div>
            @endif
        </div>
    </div>

    {{-- Total Nilai Stok --}}
    <div class="col-6 col-md-3 fade-in-up fade-in-up-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total Nilai Stok</div>
                    <div class="stat-value text-success" style="font-size:1.3rem">
                        Rp {{ number_format($rekap['total_nilai_stok'], 0, ',', '.') }}
                    </div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">total inventaris</div>
                </div>
                <div class="stat-icon" style="background:#ECFDF5;color:#059669">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Item Stok Habis --}}
    <div class="col-6 col-md-3 fade-in-up fade-in-up-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Stok Habis</div>
                    <div class="stat-value {{ $rekap['item_stok_habis'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $rekap['item_stok_habis'] }}
                    </div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">item kosong</div>
                </div>
                <div class="stat-icon" style="background:#FEF2F2;color:#DC3545">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════ ACTION BUTTONS & FILTER ══════ --}}
<div class="table-card mb-4">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user()->canManageItems())
            <a href="{{ route('admin.inventaris.create') }}" class="btn btn-mbg btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Item
            </a>
            @endif
            <button class="btn btn-sm btn-outline-primary" onclick="openModalMasuk()">
                <i class="bi bi-arrow-down-circle me-1"></i> Catat Masuk
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="openModalKeluar()">
                <i class="bi bi-arrow-up-circle me-1"></i> Catat Keluar
            </button>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->canExport())
            <a href="{{ route('admin.inventaris.export') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> Export CSV
            </a>
            @endif
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-3 border-bottom" style="background:#F8FAFC">
        <form method="GET" action="{{ route('admin.inventaris.index') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Cari nama, SKU, supplier..."
                               value="{{ request('search') }}" style="border-radius:0 8px 8px 0">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach(config('mbg.categories') as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="active" {{ (request('status', 'active')) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="backordered" {{ request('status') == 'backordered' ? 'selected' : '' }}>Backordered</option>
                        <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="stok_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Stok</option>
                        <option value="habis"  {{ request('stok_filter') == 'habis'  ? 'selected' : '' }}>Stok Habis (= 0)</option>
                        <option value="rendah" {{ request('stok_filter') == 'rendah' ? 'selected' : '' }}>Stok Rendah (&lt; ROP)</option>
                        <option value="normal" {{ request('stok_filter') == 'normal' ? 'selected' : '' }}>Stok Normal</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-mbg flex-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.inventaris.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Inventaris --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="inventarisTable">
            <thead>
                <tr>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'sku', 'sort_dir' => request('sort_by') == 'sku' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}"
                           class="text-decoration-none text-muted">
                            SKU @sortIcon('sku')
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_dir' => request('sort_by') == 'name' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}"
                           class="text-decoration-none text-muted">
                            Nama Bahan @sortIcon('name')
                        </a>
                    </th>
                    <th>Supplier</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'stock', 'sort_dir' => request('sort_by') == 'stock' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}"
                           class="text-decoration-none text-muted">
                            Stok @sortIcon('stock')
                        </a>
                    </th>
                    <th>Reorder Point</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'unit_price', 'sort_dir' => request('sort_by') == 'unit_price' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}"
                           class="text-decoration-none text-muted">
                            Harga/Unit @sortIcon('unit_price')
                        </a>
                    </th>
                    <th>Nilai Stok</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $rowClass = '';
                        if ($item->stock <= 0) $rowClass = 'row-danger';
                        elseif ($item->is_low_stock) $rowClass = 'row-warning';
                        if ($item->status === 'discontinued') $rowClass .= ' row-discontinued';
                    @endphp
                    <tr class="{{ $rowClass }}" id="row-{{ $item->id }}">
                        <td>
                            <code style="font-size:.8rem;color:var(--mbg-primary)">{{ $item->sku }}</code>
                        </td>
                        <td>
                            <div class="fw-600">{{ $item->name }}</div>
                            <div style="font-size:.75rem;color:var(--mbg-text-muted)">
                                <span class="badge" style="background:{{ config('mbg.category_colors.'.$item->category, '#6c757d') }}20;color:{{ config('mbg.category_colors.'.$item->category, '#6c757d') }};font-size:.65rem">
                                    {{ $item->category }}
                                </span>
                            </div>
                        </td>
                        <td style="font-size:.85rem;color:var(--mbg-text-muted)">{{ $item->supplier }}</td>
                        <td>
                            <span class="stock-value text-{{ $item->stock_color }}" id="stock-{{ $item->id }}">
                                {{ number_format($item->stock, 1) }}
                            </span>
                            <span style="font-size:.75rem;color:var(--mbg-text-muted)"> {{ $item->unit }}</span>
                            @if($item->stock <= 0)
                                <div><span class="badge bg-danger" style="font-size:.65rem">Habis</span></div>
                            @elseif($item->is_low_stock)
                                <div><span class="badge bg-warning text-dark" style="font-size:.65rem">⚠ Rendah</span></div>
                            @endif
                        </td>
                        <td style="font-size:.85rem">
                            {{ number_format($item->reorder_point, 1) }} {{ $item->unit }}
                        </td>
                        <td style="font-size:.85rem">
                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}/{{ $item->unit }}
                        </td>
                        <td style="font-size:.85rem;font-weight:600">
                            Rp {{ number_format($item->nilai_stok, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="status-dot {{ $item->status }}"></span>
                            <span style="font-size:.8rem">
                                {{ match($item->status) { 'active' => 'Aktif', 'backordered' => 'Backordered', 'discontinued' => 'Discontinued', default => $item->status } }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end flex-wrap">
                                @if(auth()->user()->canTransact() && $item->status !== 'discontinued')
                                    <button class="btn-action btn-sm"
                                            style="background:#EBF4FF;color:var(--mbg-primary)"
                                            onclick="openModalMasuk({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->unit }}')"
                                            title="Catat Stok Masuk">
                                        <i class="bi bi-arrow-down-circle"></i>
                                    </button>
                                    <button class="btn-action btn-sm"
                                            style="background:#FEF2F2;color:#DC3545"
                                            onclick="openModalKeluar({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->stock }}, '{{ $item->unit }}', {{ $item->reorder_point }})"
                                            title="Catat Stok Keluar">
                                        <i class="bi bi-arrow-up-circle"></i>
                                    </button>
                                @endif
                                <a href="{{ route('admin.inventaris.show', $item) }}"
                                   class="btn-action btn-sm"
                                   style="background:#F0FDF4;color:#059669"
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->canManageItems())
                                    <a href="{{ route('admin.inventaris.edit', $item) }}"
                                       class="btn-action btn-sm"
                                       style="background:#FFFBEB;color:#D97706"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div style="color:var(--mbg-text-muted)">
                                <i class="bi bi-inbox d-block fs-1 mb-3"></i>
                                <div class="fw-600">Tidak ada item ditemukan</div>
                                <small>Coba ubah filter atau tambahkan item baru</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($items->hasPages())
    <div class="d-flex align-items-center justify-content-between p-3 border-top">
        <div style="font-size:.8rem;color:var(--mbg-text-muted)">
            Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} item
        </div>
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- ══════ REKAP PER KATEGORI ══════ --}}
@if(!empty($rekap['per_kategori']))
<div class="table-card fade-in-up">
    <div class="card-header-custom">
        <div>
            <h6 class="mb-0 fw-700">📊 Rekap Stok Per Kategori</h6>
            <div style="font-size:.75rem;color:var(--mbg-text-muted)">
                Total: <strong>{{ number_format($rekap['total_stok_semua'], 1) }}</strong> unit |
                Nilai: <strong>Rp {{ number_format($rekap['total_nilai_stok'], 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>
    <div class="p-4">
        <div class="row g-3">
            <div class="col-md-7">
                @php $totalStok = max($rekap['total_stok_semua'], 1); @endphp
                @foreach($rekap['per_kategori'] as $cat => $data)
                @php
                    $pct   = round(($data['total_stok'] / $totalStok) * 100);
                    $color = config('mbg.category_colors.'.$cat, '#6c757d');
                @endphp
                <div class="category-bar mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="cat-name">{{ $cat }}</span>
                        <span class="cat-value">
                            {{ $pct }}% · {{ number_format($data['total_stok'], 1) }} unit
                            ({{ $data['jumlah_item'] }} item)
                        </span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar"
                             style="width:{{ $pct }}%;background:{{ $color }}"
                             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div style="font-size:.72rem;color:var(--mbg-text-muted);margin-top:2px">
                        Nilai: Rp {{ number_format($data['total_nilai'], 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-md-5">
                <canvas id="categoryChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

</div>

{{-- ══════ MODAL STOK MASUK ══════ --}}
<div class="modal fade" id="modalMasuk" tabindex="-1" aria-labelledby="modalMasukLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1B6CA8,#2563EB);color:#fff">
                <h5 class="modal-title" id="modalMasukLabel">
                    <i class="bi bi-arrow-down-circle me-2"></i>
                    📦 Catat Stok Masuk
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Item Selector --}}
                <div class="mb-3">
                    <label class="form-label fw-600">Item Bahan Baku <span class="text-danger">*</span></label>
                    <select class="form-select" id="masukItemId" onchange="onItemChangeMasuk(this.value)">
                        <option value="">— Pilih item —</option>
                        @foreach(\App\Models\InventoryItem::active()->orderBy('name')->get() as $itm)
                            <option value="{{ $itm->id }}" data-stock="{{ $itm->stock }}" data-unit="{{ $itm->unit }}"
                                    data-price="{{ $itm->unit_price }}" data-rop="{{ $itm->reorder_point }}">
                                {{ $itm->name }} ({{ $itm->sku }}) — Stok: {{ $itm->stock }} {{ $itm->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info Box Kebutuhan --}}
                <div id="masukInfoBox" class="info-box mb-3 d-none">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                        <div class="flex-1">
                            <div class="fw-600 mb-2" style="font-size:.9rem" id="masukInfoTitle">📊 Informasi Kebutuhan</div>
                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <div style="font-size:.7rem;color:var(--mbg-text-muted)">Stok Saat Ini</div>
                                    <div class="fw-700 text-primary" id="masukCurrentStock">—</div>
                                </div>
                                <div class="col-4">
                                    <div style="font-size:.7rem;color:var(--mbg-text-muted)">Kebutuhan 7 Hari</div>
                                    <div class="fw-700 text-warning" id="masukNeed">—</div>
                                </div>
                                <div class="col-4">
                                    <div style="font-size:.7rem;color:var(--mbg-text-muted)">Batas Beli Maks</div>
                                    <div class="fw-700 text-success" id="masukMaxBuy">—</div>
                                </div>
                            </div>
                            <div id="masukWarning" class="mt-2 d-none" style="font-size:.8rem;color:#D97706">
                                <i class="bi bi-exclamation-triangle"></i>
                                Jika membeli melebihi batas, transaksi akan ditolak sistem.
                            </div>
                            <div id="masukSufficient" class="mt-2 d-none" style="font-size:.8rem;color:#059669">
                                <i class="bi bi-check-circle"></i>
                                Stok sudah mencukupi kebutuhan 7 hari. Gunakan alasan "Penyesuaian" atau "Retur".
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Jumlah Masuk <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="masukQty"
                                   min="0.001" step="0.001" placeholder="0.000"
                                   oninput="updateMasukPreview()">
                            <span class="input-group-text" id="masukUnit">unit</span>
                        </div>
                        <div class="invalid-feedback" id="masukQtyError"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Alasan <span class="text-danger">*</span></label>
                        <select class="form-select" id="masukReason" onchange="updateMasukPreview()">
                            <option value="purchase">Pembelian dari Supplier</option>
                            <option value="adjustment">Penyesuaian / Koreksi</option>
                            <option value="return">Retur dari Dapur</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="masukSupplierRow">
                        <label class="form-label fw-600">Nama Supplier</label>
                        <input type="text" class="form-control" id="masukSupplier" placeholder="Nama supplier...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">No. Referensi (Surat Jalan/PO)</label>
                        <input type="text" class="form-control" id="masukRefNo" placeholder="SJ-2026-0001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Harga per Unit (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="masukPrice" min="0" step="100"
                                   placeholder="0" oninput="updateMasukPreview()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Tanggal Penerimaan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="masukDate"
                               value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600">Catatan</label>
                        <textarea class="form-control" id="masukNotes" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>

                {{-- Preview stok setelah transaksi --}}
                <div class="border-top mt-3 pt-3" id="masukPreview" style="font-size:.875rem;color:var(--mbg-text-muted)">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-right-circle text-primary"></i>
                        <span id="masukPreviewText">Pilih item dan isi jumlah untuk melihat preview</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-mbg" onclick="submitMasuk()" id="btnSubmitMasuk">
                    <i class="bi bi-check-lg me-1"></i> Simpan Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════ MODAL STOK KELUAR ══════ --}}
<div class="modal fade" id="modalKeluar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#DC3545,#EF4444);color:#fff">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-up-circle me-2"></i>
                    📤 Catat Stok Keluar <span id="keluarItemNameTitle"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Item Selector (Ditampilkan jika dipanggil secara global) --}}
                <div class="mb-3" id="keluarItemSelectorContainer">
                    <label class="form-label fw-600">Item Bahan Baku <span class="text-danger">*</span></label>
                    <select class="form-select" id="keluarItemId" onchange="onItemChangeKeluar(this.value)">
                        <option value="">— Pilih item yang akan dikeluarkan —</option>
                        @foreach(\App\Models\InventoryItem::active()->where('stock', '>', 0)->orderBy('name')->get() as $itm)
                            <option value="{{ $itm->id }}" data-stock="{{ $itm->stock }}" data-unit="{{ $itm->unit }}" data-rop="{{ $itm->reorder_point }}">
                                {{ $itm->name }} ({{ $itm->sku }}) — Stok: {{ $itm->stock }} {{ $itm->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="info-box mb-3" id="keluarInfoBox">
                    <div class="row text-center">
                        <div class="col-4">
                            <div style="font-size:.7rem;color:var(--mbg-text-muted)">Stok Tersedia</div>
                            <div class="fw-700 text-primary fs-5" id="keluarCurrentStock">0</div>
                        </div>
                        <div class="col-4">
                            <div style="font-size:.7rem;color:var(--mbg-text-muted)">Satuan</div>
                            <div class="fw-600" id="keluarUnit">—</div>
                        </div>
                        <div class="col-4">
                            <div style="font-size:.7rem;color:var(--mbg-text-muted)">Reorder Point</div>
                            <div class="fw-600 text-warning" id="keluarRop">0</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Jumlah Keluar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="keluarQty"
                                   min="0.001" step="0.001" placeholder="0.000"
                                   oninput="updateKeluarPreview()">
                            <span class="input-group-text" id="keluarUnitInput">unit</span>
                        </div>
                        <div class="invalid-feedback" id="keluarQtyError"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Alasan Keluar <span class="text-danger">*</span></label>
                        <select class="form-select" id="keluarReason">
                            <option value="cooking">Digunakan untuk Memasak</option>
                            <option value="waste">Terbuang / Rusak</option>
                            <option value="adjustment">Penyesuaian</option>
                            <option value="return">Retur ke Supplier</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="keluarDate"
                               value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Catatan</label>
                        <textarea class="form-control" id="keluarNotes" rows="2"
                                  placeholder="Contoh: Untuk memasak menu Senin 500 porsi"></textarea>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="mt-3 pt-3 border-top" id="keluarPreviewBox">
                    <div style="font-size:.875rem">
                        <span id="keluarPreviewText" class="text-muted">Isi jumlah untuk melihat preview</span>
                    </div>
                    <div class="mt-2 d-none" id="keluarWarningRop">
                        <div class="info-box warning">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                            <span style="font-size:.85rem">Stok setelah transaksi akan mendekati/melewati reorder point. Segera lakukan reorder.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitKeluar()" id="btnSubmitKeluar">
                    <i class="bi bi-check-lg me-1"></i> Simpan Transaksi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ═══════════════ STATE ═══════════════
let currentItemId   = null;
let currentItemData = null;
let keluarItemId    = null;

// ─── Category Chart ────────────────────────────────────────────
@if(!empty($rekap['per_kategori']))
const ctx = document.getElementById('categoryChart');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($rekap['per_kategori']->keys()->toArray()) !!},
            datasets: [{
                data: {!! json_encode($rekap['per_kategori']->pluck('total_stok')->toArray()) !!},
                backgroundColor: [
                    @foreach($rekap['per_kategori'] as $cat => $data)
                        '{{ config("mbg.category_colors.{$cat}", "#6c757d") }}',
                    @endforeach
                ],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right', labels: { font: { size: 11, family: 'Inter' }, padding: 12 }},
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${parseFloat(ctx.raw).toFixed(1)} unit`
                    }
                }
            },
            cutout: '65%',
        }
    });
}
@endif

// ═══════════════ MODAL STOK MASUK ═══════════════
const modalMasuk = new bootstrap.Modal(document.getElementById('modalMasuk'));

function openModalMasuk(itemId, itemName, unit) {
    // Reset form
    document.getElementById('masukItemId').value  = itemId || '';
    document.getElementById('masukQty').value     = '';
    document.getElementById('masukReason').value  = 'purchase';
    document.getElementById('masukSupplier').value = '';
    document.getElementById('masukRefNo').value   = '';
    document.getElementById('masukPrice').value   = '';
    document.getElementById('masukNotes').value   = '';
    document.getElementById('masukDate').value    = '{{ date("Y-m-d") }}';
    document.getElementById('masukInfoBox').classList.add('d-none');
    document.getElementById('masukPreviewText').textContent = 'Pilih item dan isi jumlah untuk melihat preview';

    if (itemId) {
        onItemChangeMasuk(itemId);
    }
    modalMasuk.show();
}

async function onItemChangeMasuk(itemId) {
    if (!itemId) {
        document.getElementById('masukInfoBox').classList.add('d-none');
        return;
    }
    currentItemId = itemId;

    try {
        const res  = await fetch(`/admin/inventaris/${itemId}/info-kebutuhan`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const info = await res.json();
        currentItemData = info;

        // Update unit
        document.getElementById('masukUnit').textContent = info.unit;
        if (info.unit_price) document.getElementById('masukPrice').value = info.unit_price;

        // Show info box
        const box = document.getElementById('masukInfoBox');
        box.classList.remove('d-none');
        document.getElementById('masukInfoTitle').textContent = `📊 Informasi Kebutuhan — ${info.name}`;
        document.getElementById('masukCurrentStock').textContent = `${parseFloat(info.stock).toFixed(1)} ${info.unit}`;
        document.getElementById('masukNeed').textContent         = `${parseFloat(info.calculated_need).toFixed(1)} ${info.unit}`;

        if (info.max_purchase > 0) {
            document.getElementById('masukMaxBuy').textContent = `${parseFloat(info.max_purchase).toFixed(1)} ${info.unit}`;
            document.getElementById('masukWarning').classList.remove('d-none');
            document.getElementById('masukSufficient').classList.add('d-none');
            document.getElementById('masukQty').max = info.max_purchase;
        } else {
            document.getElementById('masukMaxBuy').textContent = 'Tidak perlu';
            document.getElementById('masukWarning').classList.add('d-none');
            document.getElementById('masukSufficient').classList.remove('d-none');
        }

        updateMasukPreview();
    } catch(e) {
        console.error('Error fetching item info:', e);
    }
}

function updateMasukPreview() {
    if (!currentItemData) return;
    const qty    = parseFloat(document.getElementById('masukQty').value) || 0;
    const price  = parseFloat(document.getElementById('masukPrice').value) || 0;
    const stock  = parseFloat(currentItemData.stock);
    const unit   = currentItemData.unit;
    const after  = stock + qty;
    const total  = qty * price;

    let txt = `Stok setelah transaksi: <strong>${stock.toFixed(1)} + ${qty.toFixed(1)} = ${after.toFixed(1)} ${unit}</strong>`;
    if (price > 0 && qty > 0) {
        txt += ` · Total nilai: <strong>Rp ${total.toLocaleString('id-ID')}</strong>`;
    }
    document.getElementById('masukPreviewText').innerHTML = txt;
}

async function submitMasuk() {
    const itemId = document.getElementById('masukItemId').value;
    const qty    = document.getElementById('masukQty').value;
    const reason = document.getElementById('masukReason').value;
    const date   = document.getElementById('masukDate').value;

    if (!itemId || !qty || !reason || !date) {
        showToast('Mohon lengkapi semua field wajib.', 'danger');
        return;
    }

    const btn = document.getElementById('btnSubmitMasuk');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    try {
        const res = await fetch(`/admin/inventaris/${itemId}/stok-masuk`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                quantity:      parseFloat(qty),
                unit_price:    parseFloat(document.getElementById('masukPrice').value) || null,
                reason:        reason,
                supplier:      document.getElementById('masukSupplier').value || null,
                reference_no:  document.getElementById('masukRefNo').value || null,
                notes:         document.getElementById('masukNotes').value || null,
                transacted_at: date,
            })
        });

        const data = await res.json();

        if (data.success) {
            modalMasuk.hide();
            showToast(data.message, 'success');
            // Update stok di tabel tanpa reload
            const stockEl = document.getElementById(`stock-${itemId}`);
            if (stockEl && data.data) {
                stockEl.textContent = parseFloat(data.data.new_stock).toFixed(1);
            }
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'danger');
        }
    } catch(e) {
        showToast('Terjadi kesalahan koneksi. Silakan coba lagi.', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Transaksi';
    }
}

// ═══════════════ MODAL STOK KELUAR ═══════════════
const modalKeluar = new bootstrap.Modal(document.getElementById('modalKeluar'));

function openModalKeluar(itemId = null, itemName = null, currentStock = 0, unit = '', rop = 0) {
    keluarItemId = itemId;
    document.getElementById('keluarQty').value               = '';
    document.getElementById('keluarNotes').value             = '';
    document.getElementById('keluarDate').value              = '{{ date("Y-m-d") }}';
    document.getElementById('keluarPreviewText').textContent = 'Isi jumlah untuk melihat preview';
    document.getElementById('keluarWarningRop').classList.add('d-none');
    
    const selectorContainer = document.getElementById('keluarItemSelectorContainer');
    const selectEl = document.getElementById('keluarItemId');
    const infoBox = document.getElementById('keluarInfoBox');
    
    if (itemId) {
        // Dipanggil dari baris tabel (Item sudah spesifik)
        selectorContainer.classList.add('d-none');
        infoBox.classList.remove('d-none');
        document.getElementById('keluarItemNameTitle').textContent = `— ${itemName}`;
        document.getElementById('keluarCurrentStock').textContent = parseFloat(currentStock || 0).toFixed(1);
        document.getElementById('keluarUnit').textContent         = unit || 'unit';
        document.getElementById('keluarUnitInput').textContent    = unit || 'unit';
        document.getElementById('keluarRop').textContent          = parseFloat(rop || 0).toFixed(1) + ' ' + (unit || '');
        document.getElementById('keluarQty').max                 = currentStock;
        
        keluarData = { stock: parseFloat(currentStock), unit, rop: parseFloat(rop) };
        selectEl.value = itemId;
    } else {
        // Dipanggil dari tombol global
        selectorContainer.classList.remove('d-none');
        infoBox.classList.add('d-none');
        document.getElementById('keluarItemNameTitle').textContent = '';
        selectEl.value = '';
        keluarData = {};
    }

    modalKeluar.show();
}

function onItemChangeKeluar(val) {
    if (!val) {
        document.getElementById('keluarInfoBox').classList.add('d-none');
        keluarItemId = null;
        keluarData = {};
        return;
    }
    
    keluarItemId = val;
    const selectEl = document.getElementById('keluarItemId');
    const option = selectEl.options[selectEl.selectedIndex];
    
    const stock = parseFloat(option.getAttribute('data-stock'));
    const unit = option.getAttribute('data-unit');
    const rop = parseFloat(option.getAttribute('data-rop'));
    
    document.getElementById('keluarInfoBox').classList.remove('d-none');
    document.getElementById('keluarCurrentStock').textContent = stock.toFixed(1);
    document.getElementById('keluarUnit').textContent         = unit;
    document.getElementById('keluarUnitInput').textContent    = unit;
    document.getElementById('keluarRop').textContent          = rop.toFixed(1) + ' ' + unit;
    document.getElementById('keluarQty').max                 = stock;
    
    keluarData = { stock: stock, unit: unit, rop: rop };
    updateKeluarPreview();
}

let keluarData = {};

function updateKeluarPreview() {
    const qty   = parseFloat(document.getElementById('keluarQty').value) || 0;
    const stock = keluarData.stock || 0;
    const unit  = keluarData.unit  || '';
    const rop   = keluarData.rop   || 0;
    const after = stock - qty;

    if (qty <= 0) {
        document.getElementById('keluarPreviewText').textContent = 'Isi jumlah untuk melihat preview';
        document.getElementById('keluarWarningRop').classList.add('d-none');
        return;
    }

    if (qty > stock) {
        document.getElementById('keluarPreviewText').innerHTML =
            `<span class="text-danger"><i class="bi bi-x-circle"></i> Jumlah melebihi stok tersedia (${stock.toFixed(1)} ${unit})</span>`;
        return;
    }

    document.getElementById('keluarPreviewText').innerHTML =
        `Stok setelah: <strong>${stock.toFixed(1)} - ${qty.toFixed(1)} = <span class="${after < rop ? 'text-warning' : 'text-success'}">${after.toFixed(1)} ${unit}</span></strong>`;

    if (after < rop) {
        document.getElementById('keluarWarningRop').classList.remove('d-none');
    } else {
        document.getElementById('keluarWarningRop').classList.add('d-none');
    }
}

async function submitKeluar() {
    if (!keluarItemId) {
        showToast('Mohon pilih item terlebih dahulu.', 'warning');
        return;
    }
    const qty    = document.getElementById('keluarQty').value;
    const reason = document.getElementById('keluarReason').value;
    const date   = document.getElementById('keluarDate').value;

    if (!qty || !reason || !date) {
        showToast('Mohon lengkapi semua field wajib.', 'danger');
        return;
    }

    if (parseFloat(qty) > keluarData.stock) {
        showToast(`Stok tidak cukup! Stok saat ini: ${keluarData.stock.toFixed(1)} ${keluarData.unit}`, 'danger');
        return;
    }

    const btn = document.getElementById('btnSubmitKeluar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    try {
        const res = await fetch(`/admin/inventaris/${keluarItemId}/stok-keluar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                quantity:      parseFloat(qty),
                reason:        reason,
                notes:         document.getElementById('keluarNotes').value || null,
                transacted_at: date,
            })
        });

        const data = await res.json();

        if (data.success) {
            modalKeluar.hide();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'danger');
        }
    } catch(e) {
        showToast('Terjadi kesalahan koneksi. Silakan coba lagi.', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Transaksi';
    }
}

// ─── Sort icon helper ──────────────────────────────────────────
// (handled in PHP via @sortIcon directive - defined below)
</script>

{{-- Sort Icon Blade Directive (inline) --}}
<style>
.sort-icon { font-size: .7rem; }
</style>
@endpush

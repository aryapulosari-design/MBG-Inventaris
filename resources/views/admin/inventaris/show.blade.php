@extends('layouts.admin')
@section('title', 'Detail Item: ' . $item->sku)
@section('page-title', 'Detail Inventaris')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.inventaris.index') }}">Inventaris</a></li>
    <li class="breadcrumb-item active">{{ $item->sku }}</li>
@endsection

@section('content')
<div class="row g-4 fade-in-up">
    {{-- Kolom Kiri: Info Utama --}}
    <div class="col-md-4">
        {{-- Card Info Utama --}}
        <div class="table-card mb-4">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-light text-dark border">{{ $item->sku }}</span>
                    <span class="badge bg-{{ $item->status_color }}">
                        {{ match($item->status) { 'active'=>'Aktif', 'backordered'=>'Backordered', 'discontinued'=>'Discontinued', default=>$item->status } }}
                    </span>
                </div>
                
                <h4 class="fw-800 mb-1" style="color:var(--mbg-text)">{{ $item->name }}</h4>
                <div style="font-size:.85rem;color:var(--mbg-text-muted)" class="mb-4">
                    <i class="bi bi-tag-fill me-1"></i> {{ $item->category }}
                </div>

                <div class="p-3 rounded mb-4" style="background:#F8FAFC;border:1px solid var(--mbg-border)">
                    <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:1px;font-weight:700;color:var(--mbg-text-muted);margin-bottom:.5rem">
                        Stok Saat Ini
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fw-800 text-{{ $item->stock_color }}" style="font-size:2.5rem;line-height:1">
                            {{ number_format($item->stock, 1) }}
                        </span>
                        <span class="fw-600 text-muted">{{ $item->unit }}</span>
                    </div>
                    
                    @if($item->stock <= 0)
                        <div class="mt-2 badge bg-danger"><i class="bi bi-x-circle me-1"></i> Stok Habis</div>
                    @elseif($item->is_low_stock)
                        <div class="mt-2 badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i> Stok di Bawah Minimum</div>
                    @else
                        <div class="mt-2 badge bg-success"><i class="bi bi-check-circle me-1"></i> Stok Aman</div>
                    @endif
                </div>

                <div class="d-grid gap-2">
                    @if(auth()->user()->canTransact() && $item->status !== 'discontinued')
                        <button class="btn btn-outline-primary" onclick="openModalMasuk({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->unit }}')">
                            <i class="bi bi-arrow-down-circle me-1"></i> Catat Stok Masuk
                        </button>
                        <button class="btn btn-outline-danger" onclick="openModalKeluar({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->stock }}, '{{ $item->unit }}', {{ $item->reorder_point }})">
                            <i class="bi bi-arrow-up-circle me-1"></i> Catat Stok Keluar
                        </button>
                    @endif
                    @if(auth()->user()->canManageItems())
                        <a href="{{ route('admin.inventaris.edit', $item) }}" class="btn btn-light border">
                            <i class="bi bi-pencil me-1"></i> Edit Data Item
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="px-4 py-3 border-top" style="background:#F8FAFC">
                <div class="d-flex justify-content-between mb-2" style="font-size:.85rem">
                    <span class="text-muted">Harga/Unit</span>
                    <span class="fw-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:.85rem">
                    <span class="text-muted">Total Nilai Stok</span>
                    <span class="fw-600 text-success">Rp {{ number_format($item->nilai_stok, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:.85rem">
                    <span class="text-muted">Supplier Utama</span>
                    <span class="fw-600 text-end">{{ $item->supplier }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:.85rem">
                    <span class="text-muted">Reorder Point</span>
                    <span class="fw-600 text-warning">{{ number_format($item->reorder_point, 1) }} {{ $item->unit }}</span>
                </div>
                @if($item->max_stock)
                <div class="d-flex justify-content-between mb-2" style="font-size:.85rem">
                    <span class="text-muted">Max Stok</span>
                    <span class="fw-600">{{ number_format($item->max_stock, 1) }} {{ $item->unit }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between" style="font-size:.85rem">
                    <span class="text-muted">Last Restock</span>
                    <span class="fw-600">{{ $item->last_restocked ? $item->last_restocked->diffForHumans() : '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Kalkulasi Pembelian --}}
        <div class="table-card mb-4 border-primary">
            <div class="card-header-custom bg-primary text-white border-primary" style="padding:.75rem 1.25rem">
                <h6 class="mb-0 fw-700" style="font-size:.9rem"><i class="bi bi-calculator me-2"></i>Kalkulasi Kebutuhan</h6>
            </div>
            <div class="p-4">
                @if($purchaseLimit && $purchaseLimit->calculated_need > 0)
                    <div style="font-size:.85rem;color:var(--mbg-text-muted);margin-bottom:1rem">
                        Berdasarkan resep berjalan, kebutuhan rata-rata harian adalah <strong>{{ number_format($purchaseLimit->daily_need, 1) }} {{ $item->unit }}</strong>.
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border">
                        <div>
                            <div style="font-size:.7rem;color:var(--mbg-text-muted);text-transform:uppercase;font-weight:700">Kebutuhan {{ $purchaseLimit->planning_days }} Hari</div>
                            <div class="fw-800 text-primary fs-5">{{ number_format($purchaseLimit->calculated_need, 1) }} <span style="font-size:.9rem;font-weight:600">{{ $item->unit }}</span></div>
                        </div>
                        <i class="bi bi-arrow-right text-muted fs-4"></i>
                        <div class="text-end">
                            <div style="font-size:.7rem;color:var(--mbg-text-muted);text-transform:uppercase;font-weight:700">Batas Max Beli</div>
                            <div class="fw-800 text-{{ $maxPembelian > 0 ? 'success' : 'secondary' }} fs-5">{{ number_format($maxPembelian, 1) }} <span style="font-size:.9rem;font-weight:600">{{ $item->unit }}</span></div>
                        </div>
                    </div>
                    @if($maxPembelian <= 0)
                        <div class="alert alert-success py-2 mb-0 text-center" style="font-size:.8rem">
                            <i class="bi bi-check-circle-fill me-1"></i> Stok saat ini mencukupi kebutuhan mendatang.
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted py-3" style="font-size:.85rem">
                        <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                        Item ini belum digunakan dalam resep aktif apapun, sehingga batas pembelian tidak dibatasi oleh sistem.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Tabs Transaksi & Audit --}}
    <div class="col-md-8">
        <div class="table-card h-100">
            <div class="card-header-custom p-0 border-bottom-0">
                <ul class="nav nav-tabs px-3 pt-3 w-100" style="border-bottom: 2px solid var(--mbg-border)" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tab !== 'audit' ? 'active' : '' }} fw-600" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi" type="button" style="border:none;border-bottom:2px solid transparent;color:var(--mbg-text-muted);padding:.75rem 1.5rem">
                            <i class="bi bi-list-ul me-1"></i> Riwayat Transaksi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tab === 'audit' ? 'active' : '' }} fw-600" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" style="border:none;border-bottom:2px solid transparent;color:var(--mbg-text-muted);padding:.75rem 1.5rem">
                            <i class="bi bi-shield-check me-1"></i> Audit Log
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="myTabContent">
                {{-- TAB: TRANSAKSI --}}
                <div class="tab-pane fade {{ $tab !== 'audit' ? 'show active' : '' }}" id="transaksi">
                    <div class="table-responsive">
                        <table class="table mb-0" style="font-size:.85rem">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis & Alasan</th>
                                    <th>Jumlah</th>
                                    <th>Ref / Catatan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                <tr>
                                    <td class="fw-600" style="white-space:nowrap">{{ $tx->transacted_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $tx->type_badge }} mb-1 d-inline-block">
                                            {{ $tx->type === 'in' ? '↓ Masuk' : '↑ Keluar' }}
                                        </span>
                                        <div style="font-size:.75rem;color:var(--mbg-text-muted)">{{ $tx->reason_label }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-700 text-{{ $tx->type === 'in' ? 'success' : 'danger' }}">
                                            {{ $tx->type === 'in' ? '+' : '-' }}{{ number_format($tx->quantity, 1) }} {{ $item->unit }}
                                        </div>
                                        <div style="font-size:.7rem;color:var(--mbg-text-muted)">
                                            Stok: {{ number_format($tx->stock_after, 1) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($tx->reference_no) <div class="fw-600"><i class="bi bi-receipt"></i> {{ $tx->reference_no }}</div> @endif
                                        @if($tx->notes) <div class="text-muted" style="font-size:.8rem">{{ $tx->notes }}</div> @endif
                                        @if(!$tx->reference_no && !$tx->notes) <span class="text-muted">-</span> @endif
                                    </td>
                                    <td>
                                        <div class="fw-600">{{ $tx->creator->name ?? 'Sistem' }}</div>
                                        <div style="font-size:.7rem;color:var(--mbg-text-muted)">{{ $tx->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-5">Belum ada riwayat transaksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($transactions->hasPages())
                        <div class="p-3 border-top">{{ $transactions->appends(['tab' => 'transaksi'])->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>

                {{-- TAB: AUDIT LOG --}}
                <div class="tab-pane fade {{ $tab === 'audit' ? 'show active' : '' }}" id="audit">
                    <div class="p-3 bg-light border-bottom text-muted" style="font-size:.8rem">
                        <i class="bi bi-info-circle-fill me-1"></i> Audit log bersifat <em>append-only</em> (tidak dapat diubah/dihapus) sesuai pedoman keamanan.
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0" style="font-size:.8rem">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                    <th>Perubahan Detail</th>
                                    <th>Pengguna / IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditLogs as $log)
                                <tr>
                                    <td style="white-space:nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    <td><span class="badge bg-{{ $log->action_color }}">{{ $log->action_label }}</span></td>
                                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis">
                                        @if($log->action === 'updated' && $log->old_values && $log->new_values)
                                            @foreach($log->new_values as $key => $val)
                                                @if(array_key_exists($key, $log->old_values) && $log->old_values[$key] != $val)
                                                    <div class="mb-1">
                                                        <span class="fw-600">{{ $key }}:</span> 
                                                        <span class="text-danger text-decoration-line-through">{{ $log->old_values[$key] }}</span> 
                                                        <i class="bi bi-arrow-right"></i> <span class="text-success">{{ $val }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <code class="text-muted">{{ json_encode($log->new_values) }}</code>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-600">{{ $log->user_name ?? 'Sistem' }}</div>
                                        <div class="text-muted" style="font-size:.7rem">{{ $log->ip_address }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-5">Belum ada log aktivitas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($auditLogs->hasPages())
                        <div class="p-3 border-top">{{ $auditLogs->appends(['tab' => 'audit'])->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Masuk & Keluar akan di-include dari index atau dipanggil ulang jika diperlukan.
     Untuk simplicity, kita bisa copy struktur modal dari index.blade.php ke sini --}}
@include('admin.inventaris._modals')
@endsection

@push('scripts')
<style>
    .nav-tabs .nav-link.active {
        color: var(--mbg-primary) !important;
        border-bottom: 2px solid var(--mbg-primary) !important;
        background: transparent !important;
    }
</style>
<script>
    // Tab persistency logic (jika ada error form dll)
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('tab') === 'audit') {
        new bootstrap.Tab(document.querySelector('#audit-tab')).show();
    }
</script>
@endpush

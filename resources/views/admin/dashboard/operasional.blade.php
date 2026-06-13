{{-- DASHBOARD OPERASIONAL (ADMIN DAPUR / GUDANG) --}}
<div class="row g-3 mb-4">
    {{-- Action Shortcuts Besar --}}
    <div class="col-md-6 fade-in-up fade-in-up-1">
        <a href="{{ route('admin.inventaris.index') }}" class="text-decoration-none">
            <div class="stat-card" style="background:linear-gradient(135deg, #10B981 0%, #059669 100%);color:white;border:none">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-white text-success"><i class="bi bi-box-seam fs-3"></i></div>
                    <div>
                        <h4 class="mb-0 fw-800 text-white">Kelola Fisik Barang</h4>
                        <div style="font-size:.85rem;opacity:.9">Catat penerimaan & pengeluaran ke dapur hari ini.</div>
                    </div>
                    <i class="bi bi-arrow-right fs-2 ms-auto opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 fade-in-up fade-in-up-2">
        <div class="stat-card border-danger" style="background:#FFF5F5">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label text-danger">Kosong / Habis</div>
                    <div class="stat-value text-danger">{{ $rekap['item_stok_habis'] }}</div>
                </div>
                <div class="stat-icon" style="background:#FEE2E2;color:#DC3545"><i class="bi bi-exclamation-octagon"></i></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 fade-in-up fade-in-up-3">
        <div class="stat-card border-warning" style="background:#FFFBF0">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label text-warning" style="color:#D97706!important">Perlu Restock</div>
                    <div class="stat-value" style="color:#D97706">{{ $rekap['perlu_reorder'] }}</div>
                </div>
                <div class="stat-icon" style="background:#FEF3C7;color:#D97706"><i class="bi bi-arrow-repeat"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 fade-in-up">
    {{-- Daftar Barang Perlu Perhatian Segera --}}
    <div class="col-md-7">
        <div class="table-card h-100 border-warning">
            <div class="card-header-custom bg-warning text-dark border-warning" style="background-color:#FDE68A!important">
                <h6 class="mb-0 fw-700"><i class="bi bi-megaphone-fill me-2"></i>Prioritas: Stok Rendah / Habis</h6>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead style="background:#FFFBEB">
                        <tr>
                            <th class="border-bottom-0">Nama Barang</th>
                            <th class="border-bottom-0 text-end">Sisa Stok Fisik</th>
                            <th class="border-bottom-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $item)
                        <tr class="{{ $item->stock <= 0 ? 'bg-danger bg-opacity-10' : '' }}">
                            <td>
                                <div class="fw-600">{{ $item->name }}</div>
                                <code style="font-size:.75rem">{{ $item->sku }}</code>
                            </td>
                            <td class="text-end">
                                <span class="fw-800 text-{{ $item->stock_color }} fs-5">
                                    {{ number_format($item->stock, 1) }}
                                </span>
                                <span class="text-muted fw-600">{{ $item->unit }}</span>
                                <div style="font-size:.7rem;color:var(--mbg-text-muted)">
                                    Min: {{ number_format($item->reorder_point, 1) }}
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <a href="{{ route('admin.inventaris.show', $item) }}" class="btn btn-sm btn-dark">
                                    Detail / Restock
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="bi bi-emoji-smile fs-1 text-success d-block mb-3"></i>
                                <div class="fw-600 text-success">Aman Terkendali!</div>
                                <div class="text-muted" style="font-size:.85rem">Tidak ada barang yang perlu pengadaan mendesak.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Daftar Transaksi Fisik Terakhir --}}
    <div class="col-md-5">
        <div class="table-card h-100">
            <div class="card-header-custom">
                <h6 class="mb-0 fw-700">🚚 Mutasi Gudang Terakhir</h6>
            </div>
            <div class="p-0">
                <ul class="list-group list-group-flush" style="font-size:.85rem">
                    @forelse($recentTransactions as $tx)
                        <li class="list-group-item py-3 px-3 hover-bg-light">
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <strong class="text-dark text-truncate" style="max-width:200px">{{ $tx->item->name ?? '-' }}</strong>
                                <small class="text-muted">{{ $tx->transacted_at->diffForHumans() }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-{{ $tx->type_badge }} d-inline-flex align-items-center gap-1" style="font-size:.7rem">
                                    {!! $tx->type === 'in' ? '<i class="bi bi-arrow-down-circle-fill"></i> Masuk' : '<i class="bi bi-arrow-up-circle-fill"></i> Keluar' !!}
                                </span>
                                <span class="fw-800 text-{{ $tx->type === 'in' ? 'success' : 'danger' }}">
                                    {{ $tx->type === 'in' ? '+' : '-' }}{{ number_format($tx->quantity, 1) }} {{ $tx->item->unit ?? '' }}
                                </span>
                            </div>
                            <div class="text-muted mt-1" style="font-size:.75rem">
                                <em>{{ $tx->reason_label }}</em> - oleh {{ $tx->creator->name ?? 'Sistem' }}
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item py-4 text-center text-muted">Belum ada transaksi fisik hari ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

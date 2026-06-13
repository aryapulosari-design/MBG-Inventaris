{{-- DASHBOARD VIEWER (READ ONLY) --}}
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card fade-in-up fade-in-up-1" style="background:#F8FAFC">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Item Inventaris</div>
                    <div class="stat-value text-primary">{{ $rekap['total_sku_aktif'] }}</div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">SKU Aktif</div>
                </div>
                <div class="stat-icon" style="background:#E2E8F0;color:#475569"><i class="bi bi-box-seam"></i></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="stat-card fade-in-up fade-in-up-2" style="background:#F8FAFC">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Kategori Barang</div>
                    <div class="stat-value text-dark">{{ !empty($rekap['per_kategori']) ? count($rekap['per_kategori']) : 0 }}</div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">Kelompok terdaftar</div>
                </div>
                <div class="stat-icon" style="background:#E2E8F0;color:#475569"><i class="bi bi-tags"></i></div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-lg-6">
        <div class="stat-card fade-in-up fade-in-up-3" style="background:#F0FDF4; border-color:#86EFAC">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="stat-label text-success">Status Ketersediaan Umum</div>
                    <div class="fw-800 text-success mt-2" style="font-size:1.5rem">
                        {{ $rekap['perlu_reorder'] == 0 && $rekap['item_stok_habis'] == 0 ? 'Aman' : 'Perlu Pengawasan' }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#DCFCE7;color:#059669;width:64px;height:64px;font-size:2rem">
                    <i class="bi {{ $rekap['perlu_reorder'] == 0 && $rekap['item_stok_habis'] == 0 ? 'bi-shield-check' : 'bi-shield-exclamation' }}"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 fade-in-up">
    {{-- Tabel Ringkasan Terbatas --}}
    <div class="col-12">
        <div class="table-card">
            <div class="card-header-custom">
                <h6 class="mb-0 fw-700">👀 Pantauan Aktivitas Logistik</h6>
                <a href="{{ route('admin.inventaris.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Data Lengkap</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:.85rem">
                    <thead style="background:#F8FAFC">
                        <tr>
                            <th>Waktu</th>
                            <th>Barang</th>
                            <th>Aktivitas</th>
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions->take(8) as $tx)
                        <tr>
                            <td class="text-muted">{{ $tx->transacted_at->format('d M Y - H:i') }}</td>
                            <td class="fw-600">{{ $tx->item->name ?? '-' }}</td>
                            <td>
                                @if($tx->type === 'in')
                                    <span class="text-success fw-600">Terima {{ number_format($tx->quantity, 1) }} {{ $tx->item->unit ?? '' }}</span>
                                @else
                                    <span class="text-danger fw-600">Keluar {{ number_format($tx->quantity, 1) }} {{ $tx->item->unit ?? '' }}</span>
                                @endif
                                <span class="text-muted ms-1">({{ $tx->reason_label }})</span>
                            </td>
                            <td>{{ $tx->creator->name ?? 'Sistem' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada aktivitas terekam.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

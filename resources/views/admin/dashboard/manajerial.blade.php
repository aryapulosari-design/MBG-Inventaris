{{-- DASHBOARD MANAJERIAL (SUPER ADMIN & ADMIN PROGRAM) --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card fade-in-up fade-in-up-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Valuasi Stok</div>
                    <div class="stat-value text-success" style="font-size:1.35rem">Rp {{ number_format($rekap['total_nilai_stok'], 0, ',', '.') }}</div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">Aset inventaris saat ini</div>
                </div>
                <div class="stat-icon" style="background:#ECFDF5;color:#059669"><i class="bi bi-wallet2"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card fade-in-up fade-in-up-2">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total SKU Aktif</div>
                    <div class="stat-value text-primary">{{ $rekap['total_sku_aktif'] }}</div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">Item terdaftar dalam sistem</div>
                </div>
                <div class="stat-icon" style="background:#EBF4FF;color:var(--mbg-primary)"><i class="bi bi-box-seam"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card fade-in-up fade-in-up-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Perlu Reorder</div>
                    <div class="stat-value {{ $rekap['perlu_reorder'] > 0 ? 'text-warning' : 'text-success' }}">{{ $rekap['perlu_reorder'] }}</div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">Bahan baku menipis</div>
                </div>
                <div class="stat-icon" style="background:#FFFBEB;color:#D97706"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card fade-in-up fade-in-up-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Stok Habis</div>
                    <div class="stat-value {{ $rekap['item_stok_habis'] > 0 ? 'text-danger' : 'text-success' }}">{{ $rekap['item_stok_habis'] }}</div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">Segera butuh pengadaan</div>
                </div>
                <div class="stat-icon" style="background:#FEF2F2;color:#DC3545"><i class="bi bi-x-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Grafik Distribusi --}}
    @if(!empty($rekap['per_kategori']))
    <div class="col-md-8">
        <div class="table-card h-100 fade-in-up">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-700">📊 Portofolio Inventaris per Kategori</h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Unduh Laporan
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('admin.inventaris.export') }}">Rekap Stok (CSV)</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.inventaris.export-transaksi') }}">Riwayat Mutasi (CSV)</a></li>
                    </ul>
                </div>
            </div>
            <div class="p-4 d-flex flex-column justify-content-center h-100">
                <canvas id="dashCategoryChart" height="120"></canvas>
            </div>
        </div>
    </div>
    @endif

    {{-- Audit Log Cepat --}}
    <div class="col-md-4">
        <div class="table-card h-100 fade-in-up">
            <div class="card-header-custom">
                <h6 class="mb-0 fw-700">🛡️ Aktivitas Sistem Terkini</h6>
            </div>
            <div class="p-0">
                <ul class="list-group list-group-flush" style="font-size:.8rem">
                    @forelse($recentLogs as $log)
                        <li class="list-group-item py-3 px-4 hover-bg-light">
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <strong class="text-dark">{{ $log->user_name ?? 'Sistem' }}</strong>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                            <span class="badge bg-{{ $log->action_color }} me-1" style="font-size:.65rem">{{ $log->action_label }}</span>
                            <span class="text-muted d-block mt-1 text-truncate">ID: {{ $log->loggable_id }} - {{ $log->loggable_type }}</span>
                        </li>
                    @empty
                        <li class="list-group-item py-4 text-center text-muted">Belum ada aktivitas terekam.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
@if(!empty($rekap['per_kategori']))
new Chart(document.getElementById('dashCategoryChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($rekap['per_kategori']->keys()->toArray()) !!},
        datasets: [{
            label: 'Total Nilai (Rp)',
            data: {!! json_encode($rekap['per_kategori']->pluck('total_nilai')->toArray()) !!},
            backgroundColor: [
                @foreach($rekap['per_kategori'] as $cat => $data)
                    '{{ config("mbg.category_colors.{$cat}", "#6c757d") }}',
                @endforeach
            ],
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return ' Rp ' + context.raw.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: { grid: { color: '#f0f0f0' }, ticks: { font: { size: 11 }, callback: function(value) { return 'Rp ' + (value/1000) + 'K'; }}},
            x: { grid: { display: false }, ticks: { font: { size: 11 }}}
        }
    }
});
@endif
</script>
@endpush

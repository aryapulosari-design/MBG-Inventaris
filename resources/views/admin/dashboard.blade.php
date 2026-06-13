@extends('layouts.admin')
@section('title', 'Dashboard Utama')
@section('page-title', 'Dashboard Utama')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="fade-in-up">
    {{-- Welcome Banner Umum (Lebih Premium untuk Presentasi) --}}
    @php
        $roleTheme = match(auth()->user()->role) {
            'super_admin', 'admin_program' => 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)', // Biru keren
            'viewer' => 'linear-gradient(135deg, #475569 0%, #64748B 100%)',
            default => 'linear-gradient(135deg, #064E3B 0%, #059669 100%)',
        };
        $roleIcon = match(auth()->user()->role) {
            'super_admin', 'admin_program' => '🚀',
            'viewer' => '👁️',
            default => '👨‍🍳',
        };
        $roleDisplay = match(auth()->user()->role) {
            'super_admin', 'admin_program' => 'Administrator Utama',
            'viewer' => 'Viewer (Pemantau)',
            default => auth()->user()->role_label,
        };
    @endphp
    
    <div class="d-flex align-items-center gap-4 mb-4 p-4 shadow" style="background:{{ $roleTheme }};border-radius:20px;color:#fff; border: 1px solid rgba(255,255,255,0.1)">
        <div style="font-size:3.5rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2))">{{ $roleIcon }}</div>
        <div>
            <h3 class="mb-1 fw-800 tracking-tight" style="letter-spacing: -0.5px">Selamat Datang, {{ auth()->user()->name }}!</h3>
            <p class="mb-0 opacity-75 d-flex align-items-center gap-2" style="font-size:.95rem">
                <span><i class="bi bi-calendar3"></i> {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                <span class="opacity-50">|</span>
                <span><i class="bi bi-shield-check"></i> Hak Akses: <strong class="text-white">{{ $roleDisplay }}</strong></span>
            </p>
        </div>
        
        @if(in_array(auth()->user()->role, ['super_admin', 'admin_program']) && ($rekap['perlu_reorder'] > 0 || $rekap['item_stok_habis'] > 0))
        <div class="ms-auto text-end d-none d-lg-block">
            <div class="bg-white text-dark shadow rounded-4 p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #FEF2F2; color: #DC2626">
                    <i class="bi bi-bell-fill fs-5"></i>
                </div>
                <div class="text-start">
                    <div class="fw-800 text-danger" style="font-size: 1.1rem; line-height: 1">{{ $rekap['perlu_reorder'] + $rekap['item_stok_habis'] }} Peringatan</div>
                    <div class="text-muted" style="font-size: .75rem">Stok butuh perhatian segera</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Switcher Dashboard: Fokuskan Admin untuk Presentasi --}}
    @if(in_array(auth()->user()->role, ['super_admin', 'admin_program']))
        {{-- GABUNGAN TERBAIK DARI MANAJERIAL & OPERASIONAL KHUSUS PRESENTASI --}}
        
        {{-- Baris 1: Kartu Metrik Utama --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3 fade-in-up fade-in-up-1">
                <div class="stat-card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%)">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label fw-bold text-muted">Total Valuasi Aset</div>
                            <div class="stat-value text-success fw-900" style="font-size:1.4rem">Rp {{ number_format($rekap['total_nilai_stok'], 0, ',', '.') }}</div>
                        </div>
                        <div class="stat-icon rounded-circle" style="background:#DCFCE7;color:#16A34A"><i class="bi bi-cash-coin"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 fade-in-up fade-in-up-2">
                <div class="stat-card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%)">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label fw-bold text-muted">Total Jenis Barang (SKU)</div>
                            <div class="stat-value text-primary fw-900">{{ $rekap['total_sku_aktif'] }}</div>
                        </div>
                        <div class="stat-icon rounded-circle" style="background:#DBEAFE;color:#2563EB"><i class="bi bi-box2-fill"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 fade-in-up fade-in-up-3">
                <div class="stat-card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%)">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label fw-bold" style="color: #B45309">Warning Minimum Stok</div>
                            <div class="stat-value fw-900" style="color: #D97706">{{ $rekap['perlu_reorder'] }}</div>
                        </div>
                        <div class="stat-icon rounded-circle" style="background:#FEF3C7;color:#D97706"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 fade-in-up fade-in-up-4">
                <div class="stat-card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%)">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label fw-bold text-danger">Stok Kosong (Habis)</div>
                            <div class="stat-value text-danger fw-900">{{ $rekap['item_stok_habis'] }}</div>
                        </div>
                        <div class="stat-icon rounded-circle" style="background:#FEE2E2;color:#DC2626"><i class="bi bi-x-octagon-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Baris 2: Chart & Tindakan Cepat --}}
        <div class="row g-4 mb-4">
            {{-- Grafik Distribusi --}}
            @if(!empty($rekap['per_kategori']))
            <div class="col-md-8 fade-in-up">
                <div class="table-card h-100 shadow-sm border-0">
                    <div class="card-header-custom border-bottom-0 pt-4 px-4 pb-0">
                        <h6 class="mb-0 fw-800 text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Distribusi Aset Logistik</h6>
                    </div>
                    <div class="p-4 pt-2">
                        <canvas id="dashCategoryChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- Tombol Tindakan Cepat (Menunjukkan fungsionalitas aplikasi) --}}
            <div class="col-md-4 fade-in-up">
                <div class="table-card h-100 shadow-sm border-0 d-flex flex-column" style="background: linear-gradient(180deg, #F8FAFC 0%, #F1F5F9 100%)">
                    <div class="card-header-custom bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                        <h6 class="mb-0 fw-800 text-dark"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Cepat</h6>
                    </div>
                    <div class="p-4 flex-1 d-flex flex-column gap-3 justify-content-center">
                        <a href="{{ route('admin.inventaris.index') }}" class="btn btn-lg w-100 shadow-sm text-start fw-bold d-flex justify-content-between align-items-center" style="background: #2563EB; color: white; border-radius: 12px">
                            <span><i class="bi bi-box-arrow-in-down-right me-2"></i> Manajemen Stok Fisik</span>
                            <i class="bi bi-chevron-right opacity-50"></i>
                        </a>
                        <a href="{{ route('admin.inventaris.export') }}" class="btn btn-lg bg-white border border-secondary border-opacity-25 w-100 shadow-sm text-start fw-bold text-dark d-flex justify-content-between align-items-center" style="border-radius: 12px">
                            <span><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Unduh Laporan Audit (CSV)</span>
                            <i class="bi bi-download opacity-50"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Baris 3: Alert Barang Habis & Audit Log --}}
        <div class="row g-4 fade-in-up">
            <div class="col-md-6">
                <div class="table-card h-100 shadow-sm border-0">
                    <div class="card-header-custom border-bottom bg-white">
                        <h6 class="mb-0 fw-800"><i class="bi bi-shield-check text-success me-2"></i>Sistem Audit Permanen (Immutable)</h6>
                        <span class="badge bg-light text-dark border">Terbaru</span>
                    </div>
                    <div class="p-0">
                        <ul class="list-group list-group-flush" style="font-size:.85rem">
                            @forelse($recentLogs as $log)
                                <li class="list-group-item py-3 px-4 border-bottom border-light">
                                    <div class="d-flex w-100 justify-content-between mb-1">
                                        <strong class="text-dark"><i class="bi bi-person-circle text-muted me-1"></i> {{ $log->user_name ?? 'Sistem' }}</strong>
                                        <small class="text-muted fw-500">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge bg-{{ $log->action_color }} me-1 shadow-sm">{{ $log->action_label }}</span>
                                        <span class="text-muted">Item ID: {{ $log->loggable_id }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item py-5 text-center text-muted border-0">Belum ada aktivitas terekam.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-card h-100 shadow-sm border-0">
                    <div class="card-header-custom border-bottom bg-white">
                        <h6 class="mb-0 fw-800"><i class="bi bi-truck text-primary me-2"></i>Mutasi Barang Terakhir</h6>
                    </div>
                    <div class="p-0">
                        <ul class="list-group list-group-flush" style="font-size:.85rem">
                            @forelse($recentTransactions->take(5) as $tx)
                                <li class="list-group-item py-3 px-4 border-bottom border-light">
                                    <div class="d-flex w-100 justify-content-between mb-1">
                                        <strong class="text-dark text-truncate" style="max-width:250px">{{ $tx->item->name ?? '-' }}</strong>
                                        <div class="fw-800 text-{{ $tx->type === 'in' ? 'success' : 'danger' }}">
                                            {{ $tx->type === 'in' ? '+' : '-' }}{{ number_format($tx->quantity, 1) }} {{ $tx->item->unit ?? '' }}
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1 text-muted" style="font-size:.75rem">
                                        <span><i class="bi bi-tag-fill me-1 opacity-50"></i> {{ $tx->reason_label }}</span>
                                        <span>{{ $tx->transacted_at->format('H:i') }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item py-5 text-center text-muted border-0">Belum ada mutasi barang hari ini.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    @else
        {{-- VIEW UNTUK VIEWER --}}
        @include('admin.dashboard.viewer')
    @endif
</div>
@endsection

@push('scripts')
<script>
@if(in_array(auth()->user()->role, ['super_admin', 'admin_program']) && !empty($rekap['per_kategori']))
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
            borderRadius: 6,
            borderSkipped: false,
            barThickness: 40
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                titleFont: { size: 13, family: 'Inter' },
                bodyFont: { size: 14, weight: 'bold', family: 'Inter' },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return ' Rp ' + context.raw.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: { 
                grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false }, 
                ticks: { font: { size: 11, family: 'Inter', weight: '500' }, color: '#64748B', callback: function(value) { return 'Rp ' + (value/1000) + 'K'; }}
            },
            x: { 
                grid: { display: false, drawBorder: false }, 
                ticks: { font: { size: 12, family: 'Inter', weight: '600' }, color: '#475569' }
            }
        }
    }
});
@endif
</script>
@endpush

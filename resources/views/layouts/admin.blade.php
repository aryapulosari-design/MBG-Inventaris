<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="MBG Insights Hub - Sistem Manajemen Inventaris Bahan Baku Program Makan Bergizi Gratis">
    <title>@yield('title', 'Inventaris') — MBG Insights Hub</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --mbg-primary:   #1B6CA8;
            --mbg-primary-dark: #145389;
            --mbg-sidebar:   #0F1F3D;
            --mbg-sidebar-hover: #1B3060;
            --mbg-accent:    #28A745;
            --mbg-warning:   #FFC107;
            --mbg-danger:    #DC3545;
            --mbg-bg:        #F0F4F8;
            --mbg-card:      #FFFFFF;
            --mbg-border:    #E2E8F0;
            --mbg-text:      #1A202C;
            --mbg-text-muted: #718096;
            --sidebar-width: 260px;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: var(--mbg-bg);
            color: var(--mbg-text);
            min-height: 100vh;
        }

        /* ── Sidebar ───────────────────────────────── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--mbg-sidebar);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand .logo-text {
            font-weight: 800;
            font-size: 1.2rem;
            color: #fff;
            letter-spacing: -.5px;
        }
        .sidebar-brand .logo-sub {
            font-size: .7rem;
            color: rgba(255,255,255,.5);
            font-weight: 400;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .sidebar-section-title {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
            padding: 1rem 1.25rem .4rem;
        }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.7);
            padding: .6rem 1.25rem;
            border-radius: .5rem;
            margin: .1rem .75rem;
            font-size: .875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: .75rem;
            transition: all .2s ease;
        }
        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: var(--mbg-sidebar-hover);
        }
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: var(--mbg-primary);
            box-shadow: 0 4px 12px rgba(27,108,168,.4);
        }
        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
        }
        .sidebar-user {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.1);
            margin-top: auto;
        }
        .sidebar-user .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--mbg-primary);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: .9rem;
        }

        /* ── Main Layout ───────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ────────────────────────────────── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--mbg-border);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .topbar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--mbg-text);
        }
        .topbar .btn-notif {
            position: relative;
        }
        .notif-badge {
            position: absolute;
            top: -4px; right: -4px;
            background: var(--mbg-danger);
            color: #fff;
            font-size: .65rem;
            font-weight: 700;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            display: flex; align-items: center; justify-content: center;
            padding: 0 4px;
        }

        /* ── Content ───────────────────────────────── */
        .page-content {
            padding: 1.5rem;
            flex: 1;
        }

        /* ── Cards ─────────────────────────────────── */
        .stat-card {
            background: var(--mbg-card);
            border: 1px solid var(--mbg-border);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            transition: box-shadow .2s ease, transform .2s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,.08);
            transform: translateY(-2px);
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.1;
            margin: .5rem 0 .25rem;
        }
        .stat-card .stat-label {
            font-size: .8rem;
            color: var(--mbg-text-muted);
            font-weight: 500;
        }

        /* ── Tables ─────────────────────────────────── */
        .table-card {
            background: var(--mbg-card);
            border: 1px solid var(--mbg-border);
            border-radius: 12px;
            overflow: hidden;
        }
        .table-card .card-header-custom {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--mbg-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }
        .table > :not(caption) > * > * {
            padding: .75rem 1rem;
        }
        .table thead th {
            font-weight: 600;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--mbg-text-muted);
            background: #F8FAFC;
            border-bottom: 2px solid var(--mbg-border);
            white-space: nowrap;
        }
        .table tbody tr {
            transition: background .15s ease;
        }
        .table tbody tr:hover {
            background: rgba(27,108,168,.03);
        }

        /* ── Row Highlights (PRD 7.5) ─────────────── */
        .row-danger {
            background: #FFF5F5 !important;
            border-left: 4px solid #DC3545;
        }
        .row-danger:hover { background: #FFE5E5 !important; }
        .row-warning {
            background: #FFFBF0 !important;
            border-left: 4px solid #FFC107;
        }
        .row-warning:hover { background: #FFF5D6 !important; }
        .row-discontinued { opacity: .5; font-style: italic; }

        /* ── Stock Badge ─────────────────────────── */
        .stock-value {
            font-weight: 700;
            font-size: .95rem;
        }

        /* ── Filter Bar ──────────────────────────── */
        .filter-bar {
            background: #fff;
            border: 1px solid var(--mbg-border);
            border-radius: 10px;
            padding: 1rem 1.25rem;
        }
        .filter-bar .form-control, .filter-bar .form-select {
            border-radius: 8px;
            border-color: var(--mbg-border);
            font-size: .875rem;
        }
        .filter-bar .form-control:focus, .filter-bar .form-select:focus {
            border-color: var(--mbg-primary);
            box-shadow: 0 0 0 3px rgba(27,108,168,.15);
        }

        /* ── Buttons ─────────────────────────────── */
        .btn-mbg {
            background: var(--mbg-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: .875rem;
            padding: .5rem 1rem;
            transition: all .2s ease;
        }
        .btn-mbg:hover {
            background: var(--mbg-primary-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(27,108,168,.3);
        }
        .btn-action {
            padding: .3rem .6rem;
            border-radius: 6px;
            font-size: .8rem;
            border: none;
            cursor: pointer;
            transition: all .15s ease;
        }

        /* ── Progress Bars (Category Recap) ─────── */
        .category-bar .progress {
            height: 8px;
            border-radius: 4px;
        }
        .category-bar .cat-name {
            font-size: .85rem;
            font-weight: 600;
            color: var(--mbg-text);
        }
        .category-bar .cat-value {
            font-size: .8rem;
            color: var(--mbg-text-muted);
        }

        /* ── Modals ──────────────────────────────── */
        .modal-header { border-bottom: 1px solid var(--mbg-border); }
        .modal-footer { border-top: 1px solid var(--mbg-border); }
        .modal-content { border-radius: 16px; border: none; }
        .info-box {
            background: linear-gradient(135deg, #EBF4FF 0%, #F0F9FF 100%);
            border: 1px solid #BFDBFE;
            border-radius: 10px;
            padding: 1rem;
        }
        .info-box.success {
            background: linear-gradient(135deg, #ECFDF5 0%, #F0FDF4 100%);
            border-color: #A7F3D0;
        }
        .info-box.warning {
            background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
            border-color: #FDE68A;
        }
        .info-box.danger {
            background: linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%);
            border-color: #FECACA;
        }

        /* ── Toast ───────────────────────────────── */
        .toast-container { z-index: 9999; }

        /* ── Badges ──────────────────────────────── */
        .badge { font-weight: 600; letter-spacing: .3px; }

        /* ── Page Header ─────────────────────────── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-header h1 {
            font-size: 1.4rem;
            font-weight: 800;
            margin: 0;
            color: var(--mbg-text);
        }
        .page-header .breadcrumb {
            font-size: .8rem;
            margin: .25rem 0 0;
        }

        /* ── Alert dot ───────────────────────────── */
        .status-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            margin-right: 4px;
        }
        .status-dot.active  { background: #28a745; }
        .status-dot.backordered { background: #ffc107; }
        .status-dot.discontinued { background: #6c757d; }

        /* ── Animations ──────────────────────────── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp .4s ease both; }
        .fade-in-up-1 { animation-delay: .1s; }
        .fade-in-up-2 { animation-delay: .2s; }
        .fade-in-up-3 { animation-delay: .3s; }
        .fade-in-up-4 { animation-delay: .4s; }

        /* ── Scrollbar ───────────────────────────── */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 2px; }

        /* ── Responsive ──────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body>

{{-- ═══════════════ SIDEBAR ═══════════════ --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;background:var(--mbg-primary);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-box-seam text-white" style="font-size:1.1rem"></i>
            </div>
            <div>
                <div class="logo-text">MBG Hub</div>
                <div class="logo-sub">Insights Hub</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav mt-2">
        <div class="sidebar-section-title">Menu Utama</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.inventaris.index') }}"
           class="nav-link {{ request()->routeIs('admin.inventaris.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span>Inventaris</span>
            @php $lowCount = \App\Models\InventoryItem::active()->lowStock()->count(); @endphp
            @if($lowCount > 0)
                <span class="badge bg-warning text-dark ms-auto" style="font-size:.65rem">{{ $lowCount }}</span>
            @endif
        </a>

        <div class="sidebar-section-title mt-3">Laporan</div>

        <a href="{{ route('admin.inventaris.export') }}"
           class="nav-link">
            <i class="bi bi-file-earmark-arrow-down"></i>
            <span>Export Inventaris</span>
        </a>

        <a href="{{ route('admin.inventaris.export-transaksi') }}"
           class="nav-link">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Export Transaksi</span>
        </a>
    </nav>

    <div class="sidebar-user mt-auto" style="position:sticky;bottom:0;background:var(--mbg-sidebar)">
        <div class="d-flex align-items-center gap-2">
            <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div class="flex-1 overflow-hidden">
                <div style="font-size:.8rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size:.7rem;color:rgba(255,255,255,.5)">
                    {{ auth()->user()->role_label }}
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Logout">
                    <i class="bi bi-power"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ═══════════════ MAIN WRAPPER ═══════════════ --}}
<div class="main-wrapper">

    {{-- Topbar --}}
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm d-md-none" id="sidebarToggle" style="background:none;border:none">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-0" style="font-size:.75rem">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">MBG</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- Notification Bell --}}
            @php
                $unreadNotifCount = \App\Models\NotificationMbg::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm btn-notif position-relative" data-bs-toggle="dropdown" id="notifBell">
                    <i class="bi bi-bell"></i>
                    @if($unreadNotifCount > 0)
                        <span class="notif-badge">{{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg" style="width:360px;max-height:450px;overflow-y:auto;border-radius:12px;padding:.5rem" id="notifDropdown">
                    <div class="d-flex align-items-center justify-content-between px-2 pb-2 border-bottom">
                        <strong style="font-size:.9rem">Notifikasi</strong>
                        <button class="btn btn-link btn-sm p-0 text-muted" onclick="markAllRead()">Tandai semua dibaca</button>
                    </div>
                    <div id="notifList">
                        <div class="text-center text-muted py-4" style="font-size:.85rem">
                            <i class="bi bi-bell-slash d-block fs-3 mb-2"></i>
                            Memuat notifikasi...
                        </div>
                    </div>
                </div>
            </div>

            <div class="vr mx-1"></div>

            {{-- User Info --}}
            <div class="d-none d-md-flex align-items-center gap-2">
                <div class="text-end" style="font-size:.8rem">
                    <div class="fw-600">{{ auth()->user()->name }}</div>
                    <span class="badge" style="font-size:.65rem;background:{{ match(auth()->user()->role) { 'super_admin' => '#DC3545', 'admin_program' => '#1B6CA8', 'admin_dapur' => '#28A745', default => '#6C757D' } }}">
                        {{ auth()->user()->role_label }}
                    </span>
                </div>
                <div class="vr mx-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm border-0 fw-600">
                        <i class="bi bi-power"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
        @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        @endif
    </div>

    {{-- Page Content --}}
    <main class="page-content">
        @yield('content')
    </main>

    <footer class="text-center py-3" style="font-size:.75rem;color:var(--mbg-text-muted);border-top:1px solid var(--mbg-border);background:#fff">
        © 2026 MBG Insights Hub — Sistem Manajemen Inventaris Program Makan Bergizi Gratis
    </footer>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── Sidebar toggle (mobile) ───────────────────
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});

// ── Toast auto-dismiss ────────────────────────
document.querySelectorAll('.toast').forEach(toast => {
    const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
    bsToast.show();
});

// ── Show toast programmatically ───────────────
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const icon = type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill';
    const html = `
        <div class="toast align-items-center text-bg-${type} border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-${icon} me-2"></i>${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    const toastEl = container.lastElementChild;
    const bsToast = new bootstrap.Toast(toastEl, { delay: 5000 });
    bsToast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

// ── CSRF Header for fetch ─────────────────────
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ── Load Notifications ────────────────────────
async function loadNotifications() {
    try {
        const res = await fetch('{{ route("admin.notifikasi.index") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await res.json();
        const list = document.getElementById('notifList');

        if (!data.notifications || data.notifications.length === 0) {
            list.innerHTML = `<div class="text-center text-muted py-4" style="font-size:.85rem">
                <i class="bi bi-bell-slash d-block fs-3 mb-2"></i>Tidak ada notifikasi</div>`;
            return;
        }

        list.innerHTML = data.notifications.map(n => `
            <div class="d-flex gap-2 p-2 rounded mb-1 ${n.read_at ? '' : 'bg-primary bg-opacity-10'}" style="cursor:pointer"
                 onclick="markNotifRead(${n.id}, this)">
                <div class="flex-shrink-0 mt-1">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                </div>
                <div class="flex-grow-1">
                    <div style="font-size:.82rem;font-weight:600">${n.title}</div>
                    <div style="font-size:.75rem;color:var(--mbg-text-muted)">${n.message.substring(0,100)}...</div>
                    <div style="font-size:.7rem;color:var(--mbg-text-muted);margin-top:2px">
                        ${new Date(n.created_at).toLocaleString('id-ID')}
                    </div>
                </div>
            </div>`).join('');

        // Update bell badge
        const badge = document.querySelector('.notif-badge');
        if (badge) badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
        if (data.unread_count === 0 && badge) badge.remove();
    } catch(e) { console.error('Error loading notifications:', e); }
}

async function markNotifRead(id, el) {
    el.classList.remove('bg-primary', 'bg-opacity-10');
    await fetch(`/admin/notifikasi/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
}

async function markAllRead() {
    await fetch('{{ route("admin.notifikasi.mark-all-read") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
    loadNotifications();
}

// Load notifs on bell open
document.getElementById('notifBell')?.addEventListener('click', loadNotifications);
</script>

@stack('scripts')
</body>
</html>

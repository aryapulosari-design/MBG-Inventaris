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
                                {{ $itm->name }} ({{ $itm->sku }}) — Stok: {{ number_format($itm->stock, 1) }} {{ $itm->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info Box Kebutuhan --}}
                <div id="masukInfoBox" class="info-box mb-3 d-none">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                        <div class="flex-1 w-100">
                            <div class="fw-600 mb-2" style="font-size:.9rem" id="masukInfoTitle">📊 Informasi Kebutuhan</div>
                            <div class="row g-2 text-center w-100 m-0">
                                <div class="col-4">
                                    <div style="font-size:.7rem;color:var(--mbg-text-muted)">Stok Saat Ini</div>
                                    <div class="fw-700 text-primary" id="masukCurrentStock">—</div>
                                </div>
                                <div class="col-4 border-start border-end">
                                    <div style="font-size:.7rem;color:var(--mbg-text-muted)">Kebutuhan Terhitung</div>
                                    <div class="fw-700 text-warning" id="masukNeed">—</div>
                                </div>
                                <div class="col-4">
                                    <div style="font-size:.7rem;color:var(--mbg-text-muted)">Batas Beli Maks</div>
                                    <div class="fw-700 text-success" id="masukMaxBuy">—</div>
                                </div>
                            </div>
                            <div id="masukWarning" class="mt-2 d-none" style="font-size:.8rem;color:#D97706">
                                <i class="bi bi-exclamation-triangle"></i>
                                Jika membeli melebihi batas (untuk reason Pembelian), transaksi akan ditolak sistem (PRD BR-02).
                            </div>
                            <div id="masukSufficient" class="mt-2 d-none" style="font-size:.8rem;color:#059669">
                                <i class="bi bi-check-circle"></i>
                                Stok sudah mencukupi kebutuhan mendatang. Gunakan alasan Penyesuaian jika ini bukan pembelian baru.
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
                        <label class="form-label fw-600">Nama Supplier (Opsional)</label>
                        <input type="text" class="form-control" id="masukSupplier" placeholder="Nama supplier...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">No. Referensi (Opsional)</label>
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
                        <label class="form-label fw-600">Tanggal Transaksi <span class="text-danger">*</span></label>
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
                    📤 Catat Stok Keluar — <span id="keluarItemName">—</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="info-box mb-3">
                    <div class="row text-center w-100 m-0">
                        <div class="col-4">
                            <div style="font-size:.7rem;color:var(--mbg-text-muted)">Stok Tersedia</div>
                            <div class="fw-700 text-primary fs-5" id="keluarCurrentStock">0</div>
                        </div>
                        <div class="col-4 border-start border-end">
                            <div style="font-size:.7rem;color:var(--mbg-text-muted)">Satuan</div>
                            <div class="fw-600 fs-5 text-muted" id="keluarUnit">—</div>
                        </div>
                        <div class="col-4">
                            <div style="font-size:.7rem;color:var(--mbg-text-muted)">Reorder Point</div>
                            <div class="fw-600 text-warning fs-5" id="keluarRop">0</div>
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
                        <label class="form-label fw-600">Catatan Tambahan</label>
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
                        <div class="info-box warning py-2 px-3">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                            <span style="font-size:.85rem">Stok setelah transaksi akan melewati reorder point. Peringatan low stock akan dipicu otomatis.</span>
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

<script>
// Logic handlers modal for show.blade.php
let currentItemId   = null;
let currentItemData = null;
let keluarItemId    = null;
let keluarData      = {};

const modalMasuk = new bootstrap.Modal(document.getElementById('modalMasuk'));
const modalKeluar = new bootstrap.Modal(document.getElementById('modalKeluar'));

function openModalMasuk(itemId, itemName, unit) {
    document.getElementById('masukItemId').value  = itemId || '';
    document.getElementById('masukQty').value     = '';
    document.getElementById('masukReason').value  = 'purchase';
    document.getElementById('masukSupplier').value = '';
    document.getElementById('masukRefNo').value   = '';
    document.getElementById('masukPrice').value   = '';
    document.getElementById('masukNotes').value   = '';
    document.getElementById('masukInfoBox').classList.add('d-none');
    document.getElementById('masukPreviewText').textContent = 'Pilih item dan isi jumlah untuk melihat preview';

    if (itemId) onItemChangeMasuk(itemId);
    modalMasuk.show();
}

async function onItemChangeMasuk(itemId) {
    if (!itemId) { document.getElementById('masukInfoBox').classList.add('d-none'); return; }
    currentItemId = itemId;

    try {
        const res  = await fetch(`/admin/inventaris/${itemId}/info-kebutuhan`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const info = await res.json();
        currentItemData = info;

        document.getElementById('masukUnit').textContent = info.unit;
        if (info.unit_price) document.getElementById('masukPrice').value = info.unit_price;

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
            document.getElementById('masukMaxBuy').textContent = '0 (Tidak perlu)';
            document.getElementById('masukWarning').classList.add('d-none');
            document.getElementById('masukSufficient').classList.remove('d-none');
        }

        updateMasukPreview();
    } catch(e) { console.error(e); }
}

function updateMasukPreview() {
    if (!currentItemData) return;
    const qty   = parseFloat(document.getElementById('masukQty').value) || 0;
    const price = parseFloat(document.getElementById('masukPrice').value) || 0;
    const stock = parseFloat(currentItemData.stock);
    const after = stock + qty;

    document.getElementById('masukPreviewText').innerHTML = 
        `Stok setelah transaksi: <strong>${stock.toFixed(1)} + ${qty.toFixed(1)} = ${after.toFixed(1)} ${currentItemData.unit}</strong>`;
}

async function submitMasuk() {
    const itemId = document.getElementById('masukItemId').value;
    const qty    = document.getElementById('masukQty').value;
    const reason = document.getElementById('masukReason').value;

    if (!itemId || !qty || !reason) { showToast('Mohon lengkapi semua field wajib.', 'danger'); return; }

    const btn = document.getElementById('btnSubmitMasuk');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

    try {
        const res = await fetch(`/admin/inventaris/${itemId}/stok-masuk`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                quantity: parseFloat(qty),
                unit_price: parseFloat(document.getElementById('masukPrice').value) || null,
                reason: reason,
                supplier: document.getElementById('masukSupplier').value,
                reference_no: document.getElementById('masukRefNo').value,
                notes: document.getElementById('masukNotes').value,
                transacted_at: document.getElementById('masukDate').value,
            })
        });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 1500); }
        else { showToast(data.message, 'danger'); btn.disabled = false; btn.innerHTML = 'Simpan Transaksi'; }
    } catch(e) { showToast('Terjadi kesalahan.', 'danger'); btn.disabled = false; btn.innerHTML = 'Simpan Transaksi'; }
}

function openModalKeluar(itemId, itemName, currentStock, unit, rop) {
    keluarItemId = itemId;
    document.getElementById('keluarItemName').textContent    = itemName;
    document.getElementById('keluarCurrentStock').textContent = parseFloat(currentStock).toFixed(1);
    document.getElementById('keluarUnit').textContent         = unit;
    document.getElementById('keluarUnitInput').textContent    = unit;
    document.getElementById('keluarRop').textContent          = parseFloat(rop).toFixed(1);
    document.getElementById('keluarQty').value               = '';
    document.getElementById('keluarQty').max                 = currentStock;
    document.getElementById('keluarNotes').value             = '';
    
    keluarData = { stock: parseFloat(currentStock), unit, rop: parseFloat(rop) };
    modalKeluar.show();
}

function updateKeluarPreview() {
    const qty   = parseFloat(document.getElementById('keluarQty').value) || 0;
    const stock = keluarData.stock;
    const after = stock - qty;

    if (qty > stock) {
        document.getElementById('keluarPreviewText').innerHTML = `<span class="text-danger"><i class="bi bi-x-circle"></i> Melebihi stok</span>`;
        return;
    }
    
    document.getElementById('keluarPreviewText').innerHTML = 
        `Stok setelah: <strong>${stock.toFixed(1)} - ${qty.toFixed(1)} = <span class="${after < keluarData.rop ? 'text-warning' : 'text-success'}">${after.toFixed(1)} ${keluarData.unit}</span></strong>`;
        
    document.getElementById('keluarWarningRop').classList.toggle('d-none', after >= keluarData.rop);
}

async function submitKeluar() {
    if (!keluarItemId) return;
    const qty    = document.getElementById('keluarQty').value;
    const reason = document.getElementById('keluarReason').value;

    if (!qty || !reason) { showToast('Mohon lengkapi semua field wajib.', 'danger'); return; }

    const btn = document.getElementById('btnSubmitKeluar');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

    try {
        const res = await fetch(`/admin/inventaris/${keluarItemId}/stok-keluar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                quantity: parseFloat(qty),
                reason: reason,
                notes: document.getElementById('keluarNotes').value,
                transacted_at: document.getElementById('keluarDate').value,
            })
        });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 1500); }
        else { showToast(data.message, 'danger'); btn.disabled = false; btn.innerHTML = 'Simpan Transaksi'; }
    } catch(e) { showToast('Terjadi kesalahan.', 'danger'); btn.disabled = false; btn.innerHTML = 'Simpan Transaksi'; }
}
</script>

@extends('layouts.admin')
@section('title', 'Tambah Item Inventaris')
@section('page-title', 'Tambah Item Inventaris')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.inventaris.index') }}">Inventaris</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="table-card fade-in-up">
            <div class="card-header-custom">
                <h6 class="mb-0 fw-700"><i class="bi bi-box-seam me-2"></i>Informasi Bahan Baku Baru</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.inventaris.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        {{-- Baris 1: SKU & Nama --}}
                        <div class="col-md-4">
                            <label class="form-label fw-600">Kode SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" 
                                   value="{{ old('sku') }}" placeholder="Contoh: INV-001" required>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-600">Nama Bahan Baku <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Contoh: Beras Premium Kepala" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Baris 2: Kategori & Supplier --}}
                        <div class="col-md-4">
                            <label class="form-label fw-600">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">— Pilih Kategori —</option>
                                @foreach(config('mbg.categories') as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-600">Supplier/Pemasok Utama <span class="text-danger">*</span></label>
                            <input type="text" name="supplier" class="form-control @error('supplier') is-invalid @enderror" 
                                   value="{{ old('supplier') }}" placeholder="Contoh: PT Sumber Pangan Jaya" required>
                            @error('supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12"><hr class="text-muted opacity-25 my-1"></div>

                        {{-- Baris 3: Stok Awal, Unit, Harga --}}
                        <div class="col-md-4">
                            <label class="form-label fw-600">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" step="0.001" min="0" name="stock" 
                                   class="form-control @error('stock') is-invalid @enderror" 
                                   value="{{ old('stock', 0) }}" required>
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Satuan <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                                <option value="">— Pilih Satuan —</option>
                                @foreach(config('mbg.units') as $unit)
                                    <option value="{{ $unit }}" {{ old('unit') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                @endforeach
                            </select>
                            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Harga per Unit (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="100" min="0" name="unit_price" 
                                       class="form-control @error('unit_price') is-invalid @enderror" 
                                       value="{{ old('unit_price') }}" required>
                            </div>
                            @error('unit_price') <div class="text-danger" style="font-size:.875em;margin-top:.25rem">{{ $message }}</div> @enderror
                        </div>

                        {{-- Baris 4: Parameter Stok --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600">
                                Reorder Point (Minimum Stok) <span class="text-danger">*</span>
                                <i class="bi bi-info-circle text-muted ms-1" title="Batas minimum stok untuk memunculkan peringatan 'Stok Rendah'" data-bs-toggle="tooltip"></i>
                            </label>
                            <input type="number" step="0.001" min="0" name="reorder_point" 
                                   class="form-control @error('reorder_point') is-invalid @enderror" 
                                   value="{{ old('reorder_point') }}" required>
                            @error('reorder_point') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">
                                Batas Maksimum Stok (Opsional)
                                <i class="bi bi-info-circle text-muted ms-1" title="Batas maksimum penyimpanan gudang. Biarkan kosong jika tidak ada batas." data-bs-toggle="tooltip"></i>
                            </label>
                            <input type="number" step="0.001" min="0" name="max_stock" 
                                   class="form-control @error('max_stock') is-invalid @enderror" 
                                   value="{{ old('max_stock') }}">
                            @error('max_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Baris 5: Catatan --}}
                        <div class="col-12">
                            <label class="form-label fw-600">Catatan Tambahan</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3" placeholder="Informasi tambahan mengenai penyimpanan atau spesifikasi bahan baku">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.inventaris.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-mbg">
                            <i class="bi bi-save me-1"></i> Simpan Item Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush

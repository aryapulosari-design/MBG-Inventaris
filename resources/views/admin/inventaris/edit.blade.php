@extends('layouts.admin')
@section('title', 'Edit ' . $item->name)
@section('page-title', 'Edit Item Inventaris')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.inventaris.index') }}">Inventaris</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.inventaris.show', $item) }}">{{ $item->sku }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="table-card fade-in-up">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-700"><i class="bi bi-pencil-square me-2"></i>Edit Data Bahan Baku</h6>
                <span class="badge bg-light text-dark border">{{ $item->sku }}</span>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.inventaris.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label fw-600">Nama Bahan Baku <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $item->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $item->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="backordered" {{ old('status', $item->status) == 'backordered' ? 'selected' : '' }}>Backordered</option>
                                <option value="discontinued" {{ old('status', $item->status) == 'discontinued' ? 'selected' : '' }}>Discontinued (Tidak Aktif)</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-600">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                @foreach(config('mbg.categories') as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $item->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-600">Supplier/Pemasok Utama <span class="text-danger">*</span></label>
                            <input type="text" name="supplier" class="form-control @error('supplier') is-invalid @enderror" 
                                   value="{{ old('supplier', $item->supplier) }}" required>
                            @error('supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12"><hr class="text-muted opacity-25 my-1"></div>

                        <div class="col-md-4">
                            <label class="form-label fw-600">Satuan <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                                @foreach(config('mbg.units') as $unit)
                                    <option value="{{ $unit }}" {{ old('unit', $item->unit) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                @endforeach
                            </select>
                            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-600">Harga per Unit (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="100" min="0" name="unit_price" 
                                       class="form-control @error('unit_price') is-invalid @enderror" 
                                       value="{{ old('unit_price', (int)$item->unit_price) }}" required>
                            </div>
                            @error('unit_price') <div class="text-danger" style="font-size:.875em;margin-top:.25rem">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-600">Reorder Point (Minimum Stok) <span class="text-danger">*</span></label>
                            <input type="number" step="0.001" min="0" name="reorder_point" 
                                   class="form-control @error('reorder_point') is-invalid @enderror" 
                                   value="{{ old('reorder_point', (float)$item->reorder_point) }}" required>
                            @error('reorder_point') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Batas Maksimum Stok (Opsional)</label>
                            <input type="number" step="0.001" min="0" name="max_stock" 
                                   class="form-control @error('max_stock') is-invalid @enderror" 
                                   value="{{ old('max_stock', $item->max_stock ? (float)$item->max_stock : '') }}">
                            @error('max_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-600">Catatan Tambahan</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3">{{ old('notes', $item->notes) }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.inventaris.show', $item) }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-mbg">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

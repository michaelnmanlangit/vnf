@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.warehouse')

@section('title', 'Add Inventory')

@section('page-title', 'Add New Inventory Item')
@php
    $r = auth()->user()->role === 'admin' ? 'admin.inventory' : 'inventory';
@endphp

@section('styles')
<link rel="stylesheet" href="/build/assets/inventory-form-C96KQb5D.css"><script src="/build/assets/inventory-form-i_clXXmw.js" defer></script>
<style>
.product-image-panel {
    display: flex;
    align-items: center;
    gap: 28px;
    background: #f8fafc;
    border: 2px dashed #b0c4de;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
    transition: border-color .2s;
}
.product-image-panel:hover { border-color: #3498db; }
.product-img-preview-wrap {
    flex-shrink: 0;
    width: 120px;
    height: 120px;
    border-radius: 10px;
    border: 1.5px solid #d0dce8;
    background: #eef2f7;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.product-img-preview-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 9px;
    display: none;
}
.product-img-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: #8aa0ba;
    pointer-events: none;
}
.product-img-placeholder svg { opacity: .75; }
.product-img-placeholder span { font-size: .72rem; font-weight: 500; letter-spacing: .03em; }
.product-img-info { flex: 1; }
.product-img-info h4 { margin: 0 0 4px; font-size: .925rem; font-weight: 600; color: #2c3e50; }
.product-img-info p { margin: 0 0 14px; font-size: .8rem; color: #7f8c8d; line-height: 1.5; }
.btn-upload-img {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 18px;
    background: #3498db;
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
    transition: background .2s;
}
.btn-upload-img:hover { background: #2980b9; }
.btn-remove-img {
    display: none;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: transparent;
    color: #e74c3c;
    border: 1.5px solid #e74c3c;
    border-radius: 7px;
    font-size: .82rem;
    font-weight: 500;
    cursor: pointer;
    margin-left: 8px;
    transition: background .2s, color .2s;
}
.btn-remove-img:hover { background: #e74c3c; color: #fff; }
.product-img-input { display: none; }
</style>
@endsection

@section('content')
<div class="inventory-form-container">
    <form method="POST" action="{{ route($r . '.store') }}" class="inventory-form" enctype="multipart/form-data">
        @csrf

        <div class="form-section">
            <h2>Product Information</h2>

            {{-- Product Image Upload Panel --}}
            <div class="product-image-panel">
                <div class="product-img-preview-wrap">
                    <div class="product-img-placeholder" id="imgPlaceholder">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            <line x1="12" y1="12" x2="12" y2="16"/>
                            <line x1="10" y1="14" x2="14" y2="14"/>
                        </svg>
                        <span>No Image</span>
                    </div>
                    <img id="imgPreviewEl" src="" alt="Product preview">
                </div>
                <div class="product-img-info">
                    <h4>Product Photo</h4>
                    <p>Upload a clear photo of the product.<br>Supported: JPG, PNG, GIF, WEBP &mdash; Max 2 MB.</p>
                    <div style="display:flex;flex-wrap:wrap;align-items:center;">
                        <label for="productImageInput" class="btn-upload-img">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            Choose File
                        </label>
                        <button type="button" class="btn-remove-img" id="btnRemove" onclick="removeImage()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            </svg>
                            Remove
                        </button>
                    </div>
                    <input type="file" id="productImageInput" name="product_image" class="product-img-input"
                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                        onchange="handleImageSelect(event)">
                    @error('product_image')<span class="error" style="display:block;margin-top:6px;">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Row 1: Product Name | Category --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Product Name <span class="required">*</span></label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}" required>
                    @error('product_name')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Category <span class="required">*</span></label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    @error('category')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Row 2: Supplier | Date Received --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Supplier <span class="required">*</span></label>
                    <input type="text" name="supplier" value="{{ old('supplier') }}" required>
                    @error('supplier')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Date Received <span class="required">*</span></label>
                    <input type="date" name="date_received" value="{{ old('date_received', today()->toDateString()) }}" required>
                    @error('date_received')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Storage & Quantity</h2>

            {{-- Row 3: Quantity | Price --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Quantity <span class="required">*</span></label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" step="0.01" min="0" required>
                    @error('quantity')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Price per Unit (&#8369;)</label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" step="0.01" min="0">
                    @error('price')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Row 4: Unit | Storage Location --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Unit <span class="required">*</span></label>
                    <select name="unit" required>
                        <option value="">Select Unit</option>
                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liters (L)</option>
                        <option value="pieces" {{ old('unit') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                        <option value="boxes" {{ old('unit') == 'boxes' ? 'selected' : '' }}>Boxes</option>
                        <option value="sack" {{ old('unit') == 'sack' ? 'selected' : '' }}>Sack</option>
                        <option value="plastic" {{ old('unit') == 'plastic' ? 'selected' : '' }}>Plastic</option>
                    </select>
                    @error('unit')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Storage Location <span class="required">*</span></label>
                    <select name="storage_location" required>
                        <option value="">Select Storage Unit</option>
                        @foreach($storageLocations as $location)
                        <option value="{{ $location }}" {{ old('storage_location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                        @endforeach
                    </select>
                    @error('storage_location')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Row 5: Expiration Date | Notes --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Expiration Date <span class="required">*</span></label>
                    <input type="date" name="expiration_date" value="{{ old('expiration_date') }}" required>
                    @error('expiration_date')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Create Inventory Item</button>
            <a href="{{ route($r . '.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<link rel="stylesheet" href="/build/assets/inventory-form-C96KQb5D.css"><script src="/build/assets/inventory-form-i_clXXmw.js" defer></script>
<script>
function handleImageSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    const img    = document.getElementById('imgPreviewEl');
    const holder = document.getElementById('imgPlaceholder');
    const remove = document.getElementById('btnRemove');
    img.src = URL.createObjectURL(file);
    img.onload = () => URL.revokeObjectURL(img.src);
    img.style.display    = 'block';
    holder.style.display = 'none';
    remove.style.display = 'inline-flex';
}
function removeImage() {
    const input  = document.getElementById('productImageInput');
    const img    = document.getElementById('imgPreviewEl');
    const holder = document.getElementById('imgPlaceholder');
    const remove = document.getElementById('btnRemove');
    input.value  = '';
    img.src      = '';
    img.style.display    = 'none';
    holder.style.display = 'flex';
    remove.style.display = 'none';
}
</script>
@endsection

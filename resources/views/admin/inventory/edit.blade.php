@extends('layouts.admin')

@section('title', 'Edit Inventory')

@section('page-title', 'Edit Inventory Item')

@section('styles')
@vite(['resources/css/inventory-form.css', 'resources/js/inventory-form.js'])
@endsection

@section('content')
<div class="inventory-form-container">
    <form method="POST" action="{{ route('admin.inventory.update', $inventory) }}" class="inventory-form">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h2>Product Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Product Name <span class="required">*</span></label>
                    <input type="text" name="product_name" value="{{ old('product_name', $inventory->product_name) }}" required>
                    @error('product_name')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Category <span class="required">*</span></label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category', $inventory->category) == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    @error('category')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Supplier <span class="required">*</span></label>
                    <input type="text" name="supplier" value="{{ old('supplier', $inventory->supplier) }}" required>
                    @error('supplier')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Date Received <span class="required">*</span></label>
                    <input type="date" name="date_received" value="{{ old('date_received', $inventory->date_received->toDateString()) }}" required>
                    @error('date_received')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Storage & Quantity</h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Quantity <span class="required">*</span></label>
                    <input type="number" name="quantity" value="{{ old('quantity', $inventory->quantity) }}" step="0.01" min="0" required>
                    @error('quantity')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Unit <span class="required">*</span></label>
                    <select name="unit" required>
                        <option value="">Select Unit</option>
                        <option value="kg" {{ old('unit', $inventory->unit) == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                        <option value="liter" {{ old('unit', $inventory->unit) == 'liter' ? 'selected' : '' }}>Liters (L)</option>
                        <option value="pieces" {{ old('unit', $inventory->unit) == 'pieces' ? 'selected' : '' }}>Pieces</option>
                        <option value="boxes" {{ old('unit', $inventory->unit) == 'boxes' ? 'selected' : '' }}>Boxes</option>
                    </select>
                    @error('unit')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Storage Location <span class="required">*</span></label>
                    <select name="storage_location" required>
                        <option value="">Select Storage Unit</option>
                        @foreach($storageLocations as $location)
                        <option value="{{ $location }}" {{ old('storage_location', $inventory->storage_location) == $location ? 'selected' : '' }}>{{ $location }}</option>
                        @endforeach
                    </select>
                    @error('storage_location')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Status <span class="required">*</span></label>
                    <select name="status" required>
                        <option value="">Select Status</option>
                        @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ old('status', $inventory->status) == $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                        @endforeach
                    </select>
                    @error('status')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Storage Conditions</h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Temperature (°C) <span class="required">*</span></label>
                    <input type="number" name="temperature_requirement" value="{{ old('temperature_requirement', $inventory->temperature_requirement) }}" step="0.1" min="-50" max="0" required>
                    @error('temperature_requirement')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Expiration Date <span class="required">*</span></label>
                    <input type="date" name="expiration_date" value="{{ old('expiration_date', $inventory->expiration_date->toDateString()) }}" required>
                    @error('expiration_date')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3">{{ old('notes', $inventory->notes) }}</textarea>
                    @error('notes')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-submit" onclick="showUpdateModal()">Update Item</button>
            <a href="{{ route('admin.inventory.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<!-- Update Confirmation Modal -->
<div class="modal-overlay" id="updateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Update</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to update <strong id="itemNameToUpdate"></strong>'s information?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="hideUpdateModal()">Cancel</button>
            <button type="button" class="btn-modal btn-confirm" style="background: #3498db;" onclick="confirmUpdate()">Update</button>
        </div>
    </div>
</div>

@vite(['resources/css/inventory-form.css', 'resources/js/inventory-form.js'])
<script>
    let updateFormToSubmit = null;

    function showUpdateModal() {
        const inventoryForm = document.querySelector('.inventory-form');
        const productName = document.querySelector('input[name="product_name"]').value;
        
        document.getElementById('itemNameToUpdate').textContent = productName;
        document.getElementById('updateModal').style.display = 'flex';
        updateFormToSubmit = inventoryForm;
    }

    function hideUpdateModal() {
        document.getElementById('updateModal').style.display = 'none';
        updateFormToSubmit = null;
    }

    function confirmUpdate() {
        if (updateFormToSubmit) {
            updateFormToSubmit.submit();
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('updateModal');
        if (event.target === modal) {
            hideUpdateModal();
        }
    });
</script>
@endsection

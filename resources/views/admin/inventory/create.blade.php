@extends('layouts.admin')

@section('title', 'Add Inventory')

@section('page-title', 'Add New Inventory Item')

@section('styles')
@vite(['resources/css/inventory-form.css', 'resources/js/inventory-form.js'])
@endsection

@section('content')
<div class="inventory-form-container">
    <form method="POST" action="{{ route('admin.inventory.store') }}" class="inventory-form">
        @csrf

        <div class="form-section">
            <h2>Product Information</h2>
            
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

            <div class="form-row">
                <div class="form-group">
                    <label>Quantity <span class="required">*</span></label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" step="0.01" min="0" required>
                    @error('quantity')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Unit <span class="required">*</span></label>
                    <select name="unit" required>
                        <option value="">Select Unit</option>
                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liters (L)</option>
                        <option value="pieces" {{ old('unit') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                        <option value="boxes" {{ old('unit') == 'boxes' ? 'selected' : '' }}>Boxes</option>
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
                        <option value="{{ $location }}" {{ old('storage_location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                        @endforeach
                    </select>
                    @error('storage_location')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Storage Conditions</h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Temperature (°C) <span class="required">*</span></label>
                    <input type="number" name="temperature_requirement" value="{{ old('temperature_requirement') }}" step="0.1" min="-50" max="0" required>
                    @error('temperature_requirement')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Expiration Date <span class="required">*</span></label>
                    <input type="date" name="expiration_date" value="{{ old('expiration_date') }}" required>
                    @error('expiration_date')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Create Inventory Item</button>
            <a href="{{ route('admin.inventory.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

@vite(['resources/css/inventory-form.css', 'resources/js/inventory-form.js'])
@endsection

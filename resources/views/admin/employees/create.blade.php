@extends('layouts.admin')

@section('title', 'Add Employee')

@section('page-title', 'Add New Employee')

@section('styles')
@vite(['resources/css/employees-form.css', 'resources/js/employees-form.js'])
@endsection

@section('content')
<div class="employee-form-container">
    <form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data" class="employee-form">
        @csrf

        <div class="form-section">
            <h2>Profile Information</h2>
            
            <div class="image-upload-section">
                <div class="image-preview" id="imagePreview">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <p>Upload Photo</p>
                </div>
                <input type="file" id="imageInput" name="image" accept="image/*" class="image-input">
                <label for="imageInput" class="upload-label">Choose Image</label>
                <p class="upload-hint">JPG, PNG, or GIF (Max 2MB)</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>First Name <span class="required">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                    @error('first_name')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Last Name <span class="required">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                    @error('last_name')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Phone <span class="required">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Position <span class="required">*</span></label>
                    <select name="position" required>
                        <option value="">Select Position</option>
                        <optgroup label="Warehouse - Inventory">
                            <option value="Inventory Officer" {{ old('position') == 'Inventory Officer' ? 'selected' : '' }}>Inventory Officer</option>
                        </optgroup>
                        <optgroup label="Warehouse - Temperature">
                            <option value="Temperature Technician" {{ old('position') == 'Temperature Technician' ? 'selected' : '' }}>Temperature Technician</option>
                        </optgroup>
                        <optgroup label="Warehouse - Payment">
                            <option value="Payment Coordinator" {{ old('position') == 'Payment Coordinator' ? 'selected' : '' }}>Payment Coordinator</option>
                        </optgroup>
                        <optgroup label="Delivery">
                            <option value="Driver" {{ old('position') == 'Driver' ? 'selected' : '' }}>Driver</option>
                        </optgroup>
                        <optgroup label="Other">
                            <option value="Manager" {{ old('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                            <option value="Supervisor" {{ old('position') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="Specialist" {{ old('position') == 'Specialist' ? 'selected' : '' }}>Specialist</option>
                            <option value="Operator" {{ old('position') == 'Operator' ? 'selected' : '' }}>Operator</option>
                            <option value="Assistant" {{ old('position') == 'Assistant' ? 'selected' : '' }}>Assistant</option>
                        </optgroup>
                    </select>
                    @error('position')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Hire Date <span class="required">*</span></label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}" required>
                    @error('hire_date')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Employment Details</h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Department <span class="required">*</span></label>
                    <select name="department" id="departmentSelect" required>
                        <option value="">Select Department</option>
                        <option value="production" {{ old('department') == 'production' ? 'selected' : '' }}>Production</option>
                        <option value="warehouse" {{ old('department') == 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                        <option value="delivery" {{ old('department') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                        <option value="administration" {{ old('department') == 'administration' ? 'selected' : '' }}>Administration</option>
                        <option value="maintenance" {{ old('department') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('department')<span class="error">{{ $message }}</span>@enderror
                    <div id="accountNotice" style="display: none; margin-top: 0.5rem; padding: 0.75rem; background-color: #e8f4f8; border-left: 4px solid #3498db; color: #2c3e50; font-size: 0.85rem;">
                        ✓ A user account will be auto-created for this employee with default password: <strong>DefaultPass@2026</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label>Employment Status <span class="required">*</span></label>
                    <select name="employment_status" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('employment_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('employment_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('employment_status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                    @error('employment_status')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Salary</label>
                    <select name="salary">
                        <option value="">Select Salary</option>
                        @foreach($salaryRanges as $value => $label)
                            <option value="{{ $value }}" {{ old('salary') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('salary')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="House No., Street, Barangay...">
                    @error('address')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Create Employee</button>
            <a href="{{ route('admin.employees.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

@vite(['resources/css/employees-form.css', 'resources/js/employees-form.js'])
<script>
    document.getElementById('departmentSelect').addEventListener('change', function() {
        const accountNotice = document.getElementById('accountNotice');
        if (this.value === 'warehouse' || this.value === 'delivery') {
            accountNotice.style.display = 'block';
        } else {
            accountNotice.style.display = 'none';
        }
    });

    // Show notice on page load if department is already selected
    const departmentSelect = document.getElementById('departmentSelect');
    if (departmentSelect.value === 'warehouse' || departmentSelect.value === 'delivery') {
        document.getElementById('accountNotice').style.display = 'block';
    }
</script>
@endsection

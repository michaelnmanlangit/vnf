@extends('layouts.admin')

@section('title', 'Add Employee')

@section('page-title', 'Add New Employee')

@section('styles')
<link rel="stylesheet" href="/build/assets/employees-form-BzD5O2VJ.css"><script src="/build/assets/employees-form-BxA1CLbF.js" defer></script>
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
                <span id="imageError" style="display:none;color:#e74c3c;font-size:0.82rem;margin-top:4px;"></span>
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
                        <optgroup label="Production">
                            <option value="Operator" {{ old('position') == 'Operator' ? 'selected' : '' }}>Operator</option>
                        </optgroup>
                        <optgroup label="Delivery">
                            <option value="Driver" {{ old('position') == 'Driver' ? 'selected' : '' }}>Driver</option>
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
                </div>

                <div class="form-group">
                    <label>Employment Status <span class="required">*</span></label>
                    <select name="employment_status" id="employmentStatusSelect" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('employment_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('employment_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('employment_status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                    @error('employment_status')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row" id="returnDateRow" style="display: none;">
                <div class="form-group">
                    <label>Return Date <span class="required" id="returnDateRequired">*</span></label>
                    <input type="date" name="return_date" id="returnDateInput" value="{{ old('return_date') }}" min="{{ date('Y-m-d') }}">
                    <small style="color: #7f8c8d; font-size: 0.85rem; display: block; margin-top: 0.25rem;">Employee will automatically become active on this date</small>
                    @error('return_date')<span class="error">{{ $message }}</span>@enderror
                </div>

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
            </div>

            <div class="form-row" id="addressRow" style="display: none;">
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="House No., Street, Barangay...">
                    @error('address')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group"></div>
            </div>

            <div class="form-row" id="normalFieldsRow">
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

<link rel="stylesheet" href="/build/assets/employees-form-BzD5O2VJ.css"><script src="/build/assets/employees-form-BxA1CLbF.js" defer></script>
<script>
    // Image preview function
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        const errorEl = document.getElementById('imageError');

        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                errorEl.textContent = 'Image must not exceed 2MB.';
                errorEl.style.display = 'block';
                event.target.value = '';
                return;
            }
            errorEl.style.display = 'none';
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Check if preview contains an image already
                let img = preview.querySelector('img');
                
                if (img) {
                    // Update existing image
                    img.src = e.target.result;
                } else {
                    // Create new image and replace SVG/text
                    img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Employee Photo';
                    
                    // Clear the preview and add the image
                    preview.innerHTML = '';
                    preview.appendChild(img);
                }
            };
            
            reader.readAsDataURL(file);
        }
    });

    const positionSelect = document.querySelector('select[name="position"]');
    const departmentSelect = document.getElementById('departmentSelect');

    // Map positions to their departments
    const positionDepartmentMap = {
        'Inventory Officer': 'warehouse',
        'Temperature Technician': 'warehouse',
        'Payment Coordinator': 'warehouse',
        'Driver': 'delivery',
        'Manager': 'administration',
        'Supervisor': 'production',
        'Specialist': 'production',
        'Operator': 'production',
        'Assistant': 'production'
    };

    function updateDepartment() {
        const position = positionSelect.value;
        const mappedDept = positionDepartmentMap[position];

        if (mappedDept) {
            // Position has a fixed department
            departmentSelect.value = mappedDept;
            departmentSelect.style.pointerEvents = 'none';
            departmentSelect.style.opacity = '0.6';
            departmentSelect.style.cursor = 'not-allowed';
        } else {
            // Position doesn't have a fixed department, allow manual selection
            departmentSelect.style.pointerEvents = 'auto';
            departmentSelect.style.opacity = '1';
            departmentSelect.style.cursor = 'pointer';
        }
    }

    positionSelect.addEventListener('change', updateDepartment);

    // Initialize on page load
    updateDepartment();

    // Handle return date visibility based on employment status
    const employmentStatusSelect = document.getElementById('employmentStatusSelect');
    const returnDateRow = document.getElementById('returnDateRow');
    const returnDateInput = document.getElementById('returnDateInput');
    const returnDateRequired = document.getElementById('returnDateRequired');

    function toggleReturnDateField() {
        if (employmentStatusSelect.value === 'on_leave') {
            returnDateRow.style.display = 'grid';
            document.getElementById('addressRow').style.display = 'grid';
            document.getElementById('normalFieldsRow').style.display = 'none';
            returnDateInput.required = true;
            returnDateRequired.style.display = 'inline';
        } else {
            returnDateRow.style.display = 'none';
            document.getElementById('addressRow').style.display = 'none';
            document.getElementById('normalFieldsRow').style.display = 'grid';
            returnDateInput.required = false;
            returnDateRequired.style.display = 'none';
            returnDateInput.value = '';
        }
    }

    employmentStatusSelect.addEventListener('change', toggleReturnDateField);

    // Initialize on page load
    toggleReturnDateField();
</script>
@endsection

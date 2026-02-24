@extends('layouts.admin')

@section('title', 'Edit Employee')

@section('page-title', 'Edit Employee')

@section('styles')
@vite(['resources/css/employees-form.css', 'resources/js/employees-form.js'])
@endsection

@section('content')
<div class="employee-form-container">
    <form method="POST" action="{{ route('admin.employees.update', $employee) }}" enctype="multipart/form-data" class="employee-form">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h2>Profile Information</h2>
            
            <div class="image-upload-section">
                <div class="image-preview" id="imagePreview">
                    @if($employee->image && file_exists(public_path($employee->image)))
                        <img src="{{ asset($employee->image) }}" alt="{{ $employee->full_name }}">
                    @else
                        <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <p>Upload Photo</p>
                    @endif
                </div>
                <input type="file" id="imageInput" name="image" accept="image/*" class="image-input">
                <label for="imageInput" class="upload-label">Change Photo</label>
                <p class="upload-hint">JPG, PNG, or GIF (Max 2MB)</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>First Name <span class="required">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
                    @error('first_name')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Last Name <span class="required">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                    @error('last_name')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" required>
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Phone <span class="required">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                    @error('phone')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Position <span class="required">*</span></label>
                    <select name="position" required>
                        <option value="">Select Position</option>
                        <optgroup label="Warehouse - Inventory">
                            <option value="Inventory Officer" {{ old('position', $employee->position) == 'Inventory Officer' ? 'selected' : '' }}>Inventory Officer</option>
                        </optgroup>
                        <optgroup label="Warehouse - Temperature">
                            <option value="Temperature Technician" {{ old('position', $employee->position) == 'Temperature Technician' ? 'selected' : '' }}>Temperature Technician</option>
                        </optgroup>
                        <optgroup label="Warehouse - Payment">
                            <option value="Payment Coordinator" {{ old('position', $employee->position) == 'Payment Coordinator' ? 'selected' : '' }}>Payment Coordinator</option>
                        </optgroup>
                        <optgroup label="Production">
                            <option value="Operator" {{ old('position', $employee->position) == 'Operator' ? 'selected' : '' }}>Operator</option>
                        </optgroup>
                        <optgroup label="Delivery">
                            <option value="Driver" {{ old('position', $employee->position) == 'Driver' ? 'selected' : '' }}>Driver</option>
                        </optgroup>
                    </select>
                    @error('position')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Hire Date <span class="required">*</span></label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required>
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
                        <option value="production" {{ old('department', $employee->department) == 'production' ? 'selected' : '' }}>Production</option>
                        <option value="warehouse" {{ old('department', $employee->department) == 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                        <option value="delivery" {{ old('department', $employee->department) == 'delivery' ? 'selected' : '' }}>Delivery</option>
                        <option value="administration" {{ old('department', $employee->department) == 'administration' ? 'selected' : '' }}>Administration</option>
                        <option value="maintenance" {{ old('department', $employee->department) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('department')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Employment Status <span class="required">*</span></label>
                    <select name="employment_status" id="employmentStatusSelect" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('employment_status', $employee->employment_status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('employment_status', $employee->employment_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('employment_status', $employee->employment_status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                    @error('employment_status')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row" id="returnDateRow" style="display: {{ old('employment_status', $employee->employment_status) == 'on_leave' ? 'grid' : 'none' }};">
                <div class="form-group">
                    <label>Return Date <span class="required" id="returnDateRequired">*</span></label>
                    <input type="date" name="return_date" id="returnDateInput" value="{{ old('return_date', $employee->return_date ? $employee->return_date->format('Y-m-d') : '') }}" min="{{ date('Y-m-d') }}">
                    <small style="color: #7f8c8d; font-size: 0.85rem; display: block; margin-top: 0.25rem;">Employee will automatically become active on this date</small>
                    @error('return_date')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Salary</label>
                    <select name="salary">
                        <option value="">Select Salary</option>
                        @foreach($salaryRanges as $value => $label)
                            <option value="{{ $value }}" {{ old('salary', $employee->salary) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('salary')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row" id="addressRow" style="display: {{ old('employment_status', $employee->employment_status) == 'on_leave' ? 'grid' : 'none' }};">
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="{{ old('address', $employee->address) }}" placeholder="House No., Street, Barangay...">
                    @error('address')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group"></div>
            </div>

            <div class="form-row" id="normalFieldsRow" style="display: {{ old('employment_status', $employee->employment_status) == 'on_leave' ? 'none' : 'grid' }};">
                <div class="form-group">
                    <label>Salary</label>
                    <select name="salary">
                        <option value="">Select Salary</option>
                        @foreach($salaryRanges as $value => $label)
                            <option value="{{ $value }}" {{ old('salary', $employee->salary) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('salary')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="{{ old('address', $employee->address) }}" placeholder="House No., Street, Barangay...">
                    @error('address')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-submit" onclick="showUpdateModal()">Update Employee</button>
            <a href="{{ route('admin.employees.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<!-- Account Details Modal -->
<div class="modal-overlay" id="accountModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Account Created Successfully</h3>
        </div>
        <div class="modal-body">
            <div class="account-details">
                <div class="detail-item">
                    <label>Email:</label>
                    <div class="detail-value" id="accountEmail">-</div>
                </div>
                <div class="detail-item">
                    <label>Password:</label>
                    <div class="detail-value password-field">
                        <span id="accountPassword">-</span>
                    </div>
                </div>
                <div class="detail-item">
                    <label>Role:</label>
                    <div class="detail-value" id="accountRole">-</div>
                </div>
            </div>
            <p class="account-notice">Please save this password securely. The employee will need this to log in.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-confirm" onclick="goToEmployeeIndex()">Close</button>
        </div>
    </div>
</div>

<!-- Update Confirmation Modal -->
<div class="modal-overlay" id="updateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Update</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to update <strong id="employeeNameToUpdate"></strong>'s information?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="hideUpdateModal()">Cancel</button>
            <button type="button" class="btn-modal btn-confirm" style="background: #3498db;" onmouseover="this.style.background='#2980b9'" onmouseout="this.style.background='#3498db'" onclick="confirmUpdate()">Update</button>
        </div>
    </div>
</div>

@vite(['resources/css/employees-form.css', 'resources/js/employees-form.js'])
<script>
    // Position to Department Mapping
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

    // Existing modal and update logic
    let updateFormToSubmit = null;

    function showUpdateModal() {
        const employeeForm = document.querySelector('.employee-form');
        const firstName = document.querySelector('input[name="first_name"]').value;
        const lastName = document.querySelector('input[name="last_name"]').value;
        
        document.getElementById('employeeNameToUpdate').textContent = firstName + ' ' + lastName;
        document.getElementById('updateModal').style.display = 'flex';
        updateFormToSubmit = employeeForm;
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

    function closeAccountModal() {
        document.getElementById('accountModal').classList.remove('active');
    }

    function goToEmployeeIndex() {
        window.location.href = '{{ route("admin.employees.index") }}';
    }

    function showAccountModal(email, password, role) {
        document.getElementById('accountEmail').textContent = email;
        document.getElementById('accountPassword').textContent = password;
        document.getElementById('accountRole').textContent = role;
        document.getElementById('accountModal').classList.add('active');
    }

    function copyPassword() {
        const password = document.getElementById('accountPassword').textContent;
        navigator.clipboard.writeText(password).then(() => {
            alert('Password copied to clipboard!');
        });
    }

    // Check if there are account details to display
    document.addEventListener('DOMContentLoaded', function() {
        const accountDetailsStr = '{{ session("accountDetails") ? json_encode(session("accountDetails")) : "" }}';
        if (accountDetailsStr) {
            try {
                const accountDetails = JSON.parse(accountDetailsStr.replace(/&quot;/g, '"'));
                showAccountModal(accountDetails.email, accountDetails.password, accountDetails.role);
            } catch (e) {
                console.error('Error parsing account details');
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const updateModal = document.getElementById('updateModal');
            const accountModal = document.getElementById('accountModal');
            if (event.target === updateModal) {
                hideUpdateModal();
            }
            if (event.target === accountModal) {
                closeAccountModal();
            }
        });
    });
</script>
@endsection

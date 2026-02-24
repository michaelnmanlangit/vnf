@extends('layouts.admin')

@section('title', 'Edit Relocation Work')

@section('page-title', 'Edit Relocation Work')

@section('styles')
@vite(['resources/css/billing.css'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .location-card {
        background: #f0f4ff;
        border: 1px solid #c7d2fe;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        margin-top: 0.5rem;
        display: none;
    }
    .location-card.visible { display: block; }
    .location-card .loc-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .25rem;
    }
    .location-card .loc-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }
    .location-card .no-location {
        font-size: 0.9rem;
        color: #ef4444;
        font-style: italic;
    }
    .arrow-icon {
        text-align: center;
        font-size: 1.5rem;
        color: #6366f1;
        margin: 1rem 0;
        display: none;
    }
</style>
@endsection

@section('content')
<div class="billing-container">
    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tasks.update', $task) }}" method="POST" id="relocationForm">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-section">
                <h3>Relocation Details</h3>

                {{-- Worker Select --}}
                <div class="form-group">
                    <label for="employee_id">Worker <span class="required">*</span></label>
                    <select name="employee_id" id="employee_id" class="form-control" required>
                        <option value="">-- Select Worker --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}"
                                {{ old('employee_id', $task->employee_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }}
                                ({{ ucfirst($emp->department) }} – {{ $emp->position }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                    <small class="helper-text">Active production and administration workers only.</small>
                </div>

                {{-- Current Location Card (AJAX) --}}
                <div id="currentLocationCard" class="location-card">
                    <div class="loc-label"><i class="fas fa-map-marker-alt"></i> Current Location</div>
                    <div id="currentLocationValue" class="loc-value"></div>
                </div>

                {{-- Arrow --}}
                <div class="arrow-icon" id="arrowIcon">
                    <i class="fas fa-arrow-down"></i>
                </div>

                {{-- Relocate To --}}
                <div class="form-group" id="relocateToGroup" style="display:none;">
                    <label for="relocate_to_storage_unit_id">Relocate To <span class="required">*</span></label>
                    <select name="relocate_to_storage_unit_id" id="relocate_to_storage_unit_id" class="form-control">
                        <option value="">-- Select Destination --</option>
                        @foreach($storageUnits as $unit)
                            <option value="{{ $unit->id }}"
                                {{ old('relocate_to_storage_unit_id', $task->relocate_to_storage_unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('relocate_to_storage_unit_id')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Relocation</button>
            <a href="{{ route('admin.tasks.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const employeeSelect   = document.getElementById('employee_id');
    const locationCard     = document.getElementById('currentLocationCard');
    const locationValue    = document.getElementById('currentLocationValue');
    const arrowIcon        = document.getElementById('arrowIcon');
    const relocateToGroup  = document.getElementById('relocateToGroup');
    const relocateToSelect = document.getElementById('relocate_to_storage_unit_id');

    function loadEmployeeInfo(id) {
        if (!id) {
            locationCard.classList.remove('visible');
            arrowIcon.style.display = 'none';
            relocateToGroup.style.display = 'none';
            relocateToSelect.required = false;
            return;
        }

        fetch('/admin/tasks/employee/' + id + '/info', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.current_unit) {
                locationValue.innerHTML =
                    '<strong>' + data.current_unit.name + '</strong> (' + data.current_unit.code + ')';
            } else {
                locationValue.innerHTML = '<span class="no-location">No storage unit assigned yet</span>';
            }
            locationCard.classList.add('visible');
            arrowIcon.style.display = 'block';
            relocateToGroup.style.display = 'block';
            relocateToSelect.required = true;

            // Disable the current unit in the destination list
            Array.from(relocateToSelect.options).forEach(function(opt) {
                opt.disabled = data.current_unit ? (opt.value == data.current_unit.id) : false;
                if (opt.disabled && opt.selected) opt.selected = false;
            });
        });
    }

    employeeSelect.addEventListener('change', function () {
        loadEmployeeInfo(this.value);
    });

    // Auto-load on page open (existing task or validation error)
    if (employeeSelect.value) {
        loadEmployeeInfo(employeeSelect.value);
    }
})();
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Work Assignments')

@section('page-title', 'Work Assignments')

@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* Custom filter dropdown styles */
.billing-toolbar {
    display: flex;
    flex-wrap: nowrap;
    gap: 0.5rem;
    margin-bottom: 2rem;
    background: white;
    padding: 0.6rem 0.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    align-items: center;
    justify-content: flex-end;
    width: fit-content;
    margin-left: auto;
    overflow: visible;
}

.toolbar-form {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex: 0 1 auto;
    order: 1;
}

.search-box {
    flex: 0 1 280px;
    display: flex;
    align-items: center;
    position: relative;
    background: #f8fafc;
    border-radius: 8px;
    padding: 0.1rem;
}

.search-box input {
    flex: 1;
    padding: 0.6rem 1rem 0.6rem 2.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    background: white;
    color: #2d3748;
    outline: none;
    transition: all 0.3s;
}

.search-box input:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.search-icon {
    position: absolute;
    left: 0.75rem;
    color: #a0aec0;
    pointer-events: none;
    z-index: 1;
}

.filter-group {
    position: relative;
    overflow: visible;
}

.multi-select-wrapper {
    position: relative;
}

.multi-select-toggle {
    padding: 0.6rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem;
    color: #2d3748;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
    min-width: 120px;
}

.multi-select-toggle:hover {
    border-color: #4299e1;
}

.multi-select-toggle svg {
    transition: transform 0.3s;
}

.multi-select-toggle.active svg {
    transform: rotate(180deg);
}

.multi-select-dropdown {
    position: absolute;
    top: calc(100% + 5px);
    right: 0;
    left: auto;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    min-width: 250px;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    padding: 0;
}

.multi-select-dropdown.active {
    max-height: 400px;
    opacity: 1;
    visibility: visible;
    overflow-y: auto;
}

.filter-section {
    padding: 1rem;
}

.section-title {
    font-weight: 600;
    font-size: 0.875rem;
    color: #4a5568;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.filter-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0;
    cursor: pointer;
    transition: color 0.2s;
}

.filter-option:hover {
    color: #4299e1;
}

.filter-option input[type="radio"] {
    margin: 0;
    cursor: pointer;
}

.filter-option span {
    font-size: 0.9rem;
    color: #2d3748;
}

.filter-actions {
    padding: 0.75rem 1rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 0.5rem;
    background: #f8fafc;
    border-radius: 0 0 8px 8px;
}

.filter-apply {
    background: #4299e1;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background 0.2s;
}

.filter-apply:hover {
    background: #3182ce;
}

.filter-reset {
    color: #718096;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: color 0.2s;
}

.filter-reset:hover {
    color: #2d3748;
    background: #edf2f7;
}

.toolbar-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    order: 2;
}

.clear-filters {
    color: #e53e3e;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.clear-filters:hover {
    background: #fed7d7;
    color: #c53030;
}
</style>
@endsection

@section('content')
<div class="billing-grid-container">
    @if(session('success'))
        <div class="alert alert-success" id="successAlert" style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
            <span>{{ session('success') }}</span>
            <button onclick="document.getElementById('successAlert').remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;line-height:1;">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" id="errorAlert" style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
            <span>{{ session('error') }}</span>
            <button onclick="document.getElementById('errorAlert').remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;line-height:1;">&times;</button>
        </div>
    @endif
    <!-- Storage Unit Worker Allocation -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 700; color: #2c3e50; text-transform: uppercase; letter-spacing: 0.05em;">Worker Allocation per Storage Unit</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(175px, 1fr)); gap: 1rem;">
            @foreach($storageUnits as $unit)
                <div style="border: 1px solid #e8ecef; border-radius: 10px; padding: 1.25rem; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: {{ $unit->status === 'active' ? '#27ae60' : ($unit->status === 'maintenance' ? '#f39c12' : '#95a5a6') }};"></div>
                    <div style="margin-bottom: 0.75rem;">
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">{{ $unit->name }}</span>
                        <span style="font-size: 0.7rem; color: #7f8c8d; display: block; margin-top: 0.15rem;">{{ $unit->code }}</span>
                    </div>
                    <div style="display: flex; align-items: baseline; gap: 0.35rem; margin-bottom: 0.35rem;">
                        <span style="font-size: 2rem; font-weight: 700; color: #2c3e50; line-height: 1;">{{ $unit->assigned_employees_count }}</span>
                        <span style="font-size: 0.8rem; color: #7f8c8d;">worker{{ $unit->assigned_employees_count !== 1 ? 's' : '' }}</span>
                    </div>
                    <div style="font-size: 0.75rem; color: #7f8c8d; margin-bottom: 0.35rem;">
                        {{ $unit->warehouse_count }} warehouse
                    </div>
                    <div style="font-size: 0.75rem; color: #7f8c8d;">
                        {{ $unit->production_count }} production
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="billing-toolbar">
        <form method="GET" action="{{ route('admin.tasks.index') }}" id="filterForm" class="toolbar-form">
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by work title..." 
                       value="{{ request('search') }}">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>

            <!-- Filters -->
            <div class="filter-group">
                <div class="multi-select-wrapper">
                    <button type="button" class="multi-select-toggle" id="filterToggle">
                        <span class="filter-label">Filters</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="multi-select-dropdown" id="filterDropdown">
                        <div class="filter-section">
                            <div class="section-title">Employee Type</div>
                            <label class="filter-option">
                                <input type="radio" name="employee_type" value="" 
                                       {{ !request('employee_type') ? 'checked' : '' }}>
                                <span>All Types</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="employee_type" value="warehouse" 
                                       {{ request('employee_type') == 'warehouse' ? 'checked' : '' }}>
                                <span>Warehouse</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="employee_type" value="production" 
                                       {{ request('employee_type') == 'production' ? 'checked' : '' }}>
                                <span>Production</span>
                            </label>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="filter-apply">Apply</button>
                            <a href="{{ route('admin.tasks.index') }}" class="filter-reset">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Actions -->
        <div class="toolbar-actions">
            @if(request('search') || request('employee_type'))
                <a href="{{ route('admin.tasks.index') }}" class="clear-filters">Clear All</a>
            @endif
        </div>
    </div>

    <!-- Work List -->
    <div class="billing-list">
        <table class="billing-table">
            <thead>
                <tr>
                    <th>Worker</th>
                    <th>Current Unit</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $employee)
                    <tr>
                        <td>
                            <strong>{{ $employee->full_name }}</strong>
                            <br><small style="color: #7f8c8d;">{{ $employee->position }}</small>
                        </td>
                        <td>
                            @if($employee->assignedStorageUnit)
                                <strong>{{ $employee->assignedStorageUnit->name }}</strong>
                                <br><small style="color: #7f8c8d;">{{ $employee->assignedStorageUnit->code }}</small>
                            @else
                                <span style="color: #7f8c8d; font-style: italic;">No unit assigned</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge pending" style="background: #e3f2fd; color: #1976d2;">
                                {{ ucfirst($employee->department) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn-action edit" onclick="editTask({{ $employee->id }})" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Assign
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 80px; height: 80px; margin-bottom: 1rem; color: #cbd5e0;">
                                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    <path d="M9 12l2 2 4-4"></path>
                                </svg>
                                <h3>No work assignments found</h3>
                                <p>{{ (request('search') || request('employee_type')) ? 'Try adjusting your filters' : 'Create your first work assignment to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($tasks->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination">
                    {{ $tasks->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Edit Task Modal -->
<div id="editTaskModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="max-width: 520px; width: 100%; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3); max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h3>Assign Relocation</h3>
        </div>

        <form id="editTaskForm" action="{{ route('admin.tasks.store') }}" method="POST" class="task-form">
            @csrf
            
            <div class="modal-body">
                {{-- Worker Display --}}
                <div class="form-row">
                    <div class="form-group">
                        <label>Worker <span class="required">*</span></label>
                        <div id="edit_employee_display" style="padding:0.75rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;color:#2d3748;font-weight:500;text-align:center;">
                            -- Select a Worker --
                        </div>
                        <input type="hidden" name="employee_id" id="edit_employee_id">
                    </div>
                </div>

                {{-- Current Location Card --}}
                <div class="form-row" id="edit_locationCard" style="display:none;">
                    <div class="form-group">
                        <div style="background:#f0f4ff;border:1px solid #c7d2fe;border-radius:.5rem;padding:.85rem 1rem;">
                            <div style="font-size:.7rem;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">
                                <i class="fas fa-map-marker-alt"></i> Current Location
                            </div>
                            <div id="edit_locationValue" style="font-size:.95rem;font-weight:600;color:#1e293b;text-align:center;"></div>
                        </div>
                    </div>
                </div>

                {{-- Arrow --}}
                <div id="edit_arrow" style="display:none;text-align:center;font-size:1.3rem;color:#6366f1;margin:.5rem 0;">
                    <i class="fas fa-arrow-down"></i>
                </div>

                {{-- Relocate To --}}
                <div class="form-row" id="edit_relocateToRow" style="display:none;">
                    <div class="form-group">
                        <label for="edit_relocate_to">Relocate To <span class="required">*</span></label>
                        <select name="relocate_to_storage_unit_id" id="edit_relocate_to">
                            <option value="">-- Select Destination --</option>
                            @foreach($storageUnits as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->name }} ({{ $unit->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" id="updateTaskButton">Assign Relocation</button>
                <button type="button" class="btn-cancel" onclick="document.getElementById('editTaskModal').style.display='none'; document.getElementById('editTaskModal').classList.remove('active');">Cancel</button>
            </div>
        </form>
    </div>
</div>


<script>
// Filter dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    const filterDropdown = document.getElementById('filterDropdown');
    
    if (filterToggle && filterDropdown) {
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            filterToggle.classList.toggle('active');
            filterDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!filterToggle.contains(e.target) && !filterDropdown.contains(e.target)) {
                filterToggle.classList.remove('active');
                filterDropdown.classList.remove('active');
            }
        });

        // Prevent dropdown from closing when clicking inside it
        filterDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Auto-submit form when filter option is selected
    const filterOptions = document.querySelectorAll('.filter-option input[type="radio"]');
    filterOptions.forEach(option => {
        option.addEventListener('change', function() {
            // Do not auto-submit, wait for Apply button
        });
    });
});
</script>

<script>
// Employee data for modal
const employees = @json($employees);

function editTask(employeeId) {
    // Find the employee
    const employee = employees.find(e => e.id == employeeId);
    
    // Set the hidden employee_id field
    document.getElementById('edit_employee_id').value = employeeId;
    
    // Display the employee name
    if (employee) {
        const fullName = employee.first_name + ' ' + employee.last_name;
        const department = employee.department.charAt(0).toUpperCase() + employee.department.slice(1);
        document.getElementById('edit_employee_display').innerHTML = 
            '<strong>' + fullName + '</strong><br>' +
            '<small style="color:#7f8c8d;">' + employee.position + ' (' + department + ')</small>';
    }

    // Enable the submit button right away
    const assignButton = document.getElementById('updateTaskButton');
    assignButton.disabled = false;
    assignButton.style.opacity = '1';
    assignButton.style.cursor = 'pointer';

    // Load employee info (current unit)
    loadEditEmployeeInfo(employeeId);

    const modal = document.getElementById('editTaskModal');
    modal.style.display = 'flex';
    modal.classList.add('active');
}

function loadEditEmployeeInfo(id) {
    const locCard = document.getElementById('edit_locationCard');
    const locVal = document.getElementById('edit_locationValue');
    const arrow = document.getElementById('edit_arrow');
    const toRow = document.getElementById('edit_relocateToRow');
    const toSel = document.getElementById('edit_relocate_to');

    if (!id) {
        locCard.style.display = 'none';
        arrow.style.display = 'none';
        toRow.style.display = 'none';
        toSel.required = false;
        return;
    }

    fetch('/admin/tasks/employee/' + id + '/info', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.current_unit) {
            locVal.innerHTML = '<strong>' + data.current_unit.name + '</strong> (' + data.current_unit.code + ')';
        } else {
            locVal.innerHTML = '<span style="color:#ef4444;font-style:italic;">No storage unit assigned yet</span>';
        }
        locCard.style.display = 'block';
        arrow.style.display = 'block';
        toRow.style.display = 'block';
        toSel.required = true;
        // Reset destination dropdown selection
        toSel.value = '';
        Array.from(toSel.options).forEach(function(opt) {
            opt.disabled = data.current_unit ? (opt.value == data.current_unit.id) : false;
            if (opt.disabled && opt.selected) opt.selected = false;
        });
    });
}

// Employee change in modal (in case user changes selection)
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    const editTaskModal = document.getElementById('editTaskModal');
    if (editTaskModal) {
        editTaskModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                this.classList.remove('active');
            }
        });
    }
});
</script>

@endsection

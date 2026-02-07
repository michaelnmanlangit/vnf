@extends('layouts.admin')

@section('title', 'Employee Management')

@section('page-title', 'Employee Management')
@section('styles')
@vite(['resources/css/employees.css', 'resources/js/employees.js'])
@endsection
@section('content')
<div class="employee-grid-container">
    <!-- Search & Filter Toolbar -->
    <div class="employee-toolbar">
        <form method="GET" action="{{ route('admin.employees.index') }}" id="filterForm" class="toolbar-form">
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by name..." 
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
                            <div class="section-title">Department</div>
                            <label class="filter-option">
                                <input type="radio" name="department" value="" 
                                       {{ !request('department') ? 'checked' : '' }}>
                                <span>All Departments</span>
                            </label>
                            @foreach($departments as $dept)
                                <label class="filter-option">
                                    <input type="radio" name="department" value="{{ $dept }}" 
                                           {{ request('department') == $dept ? 'checked' : '' }}>
                                    <span>{{ ucfirst($dept) }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="filter-section">
                            <div class="section-title">Status</div>
                            <label class="filter-option">
                                <input type="radio" name="status" value="" 
                                       {{ !request('status') ? 'checked' : '' }}>
                                <span>All Status</span>
                            </label>
                            @foreach($statuses as $st)
                                <label class="filter-option">
                                    <input type="radio" name="status" value="{{ $st }}" 
                                           {{ request('status') == $st ? 'checked' : '' }}>
                                    <span>{{ ucfirst(str_replace('_', ' ', $st)) }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="filter-section">
                            <div class="section-title">Sort By</div>
                            <label class="filter-option">
                                <input type="radio" name="sort" value="latest" 
                                       {{ request('sort', 'latest') == 'latest' ? 'checked' : '' }}>
                                <span>Newest First</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="sort" value="oldest" 
                                       {{ request('sort') == 'oldest' ? 'checked' : '' }}>
                                <span>Oldest First</span>
                            </label>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="filter-apply">Apply</button>
                            <a href="{{ route('admin.employees.index') }}" class="filter-reset">Reset</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="toolbar-actions">
                @if(request('search') || request('department') || request('status'))
                    <a href="{{ route('admin.employees.index') }}" class="clear-filters">Clear All</a>
                @endif
                <a href="{{ route('admin.employees.create') }}" class="add-employee-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Employee
                </a>
            </div>
        </form>
    </div>

    <!-- Employees List -->
    @if($employees->count() > 0)
        <div class="employees-list">
            <table class="employees-table" data-route="{{ route('admin.employees.index') }}">
                <thead>
                    <tr>
                        <th class="sortable" data-column="id">
                            ID
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th class="sortable" data-column="first_name">
                            Name
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th class="sortable" data-column="position">
                            Position
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th class="sortable" data-column="department">
                            Department
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th class="sortable" data-column="employment_status">
                            Status
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr class="employee-row">
                        <td class="employee-id">{{ str_pad($employee->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="employee-name">{{ $employee->full_name }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ ucfirst($employee->department) }}</td>
                        <td>
                            <span class="status-badge status-{{ $employee->employment_status }}">
                                {{ ucfirst(str_replace('_', ' ', $employee->employment_status)) }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-action edit" title="Edit Employee">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" style="display: inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action delete" onclick="showDeleteModal('{{ $employee->id }}', '{{ $employee->first_name }} {{ $employee->last_name }}')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h2>No Employees Yet</h2>
            <p>Get started by adding your first employee to the system.</p>
            <a href="{{ route('admin.employees.create') }}" class="empty-state-btn">Add First Employee</a>
        </div>
    @endif

    <!-- Pagination -->
    @if($employees->hasPages())
    <div class="pagination-wrapper">
        {{ $employees->appends(request()->query())->render('pagination.bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete <strong id="employeeToDelete"></strong>? <span style="color: #dc3545; font-weight: 600;">This action cannot be undone.</span></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="hideDeleteModal()">Cancel</button>
            <button type="button" class="btn-modal btn-confirm" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

@vite(['resources/css/employees.css', 'resources/js/employees.js'])
<script>
    let deleteFormToSubmit = null;

    function showDeleteModal(employeeId, employeeName) {
        document.getElementById('employeeToDelete').textContent = employeeName;
        document.getElementById('deleteModal').classList.add('active');
        deleteFormToSubmit = document.querySelector(`form.delete-form`);
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        deleteFormToSubmit = null;
    }

    function confirmDelete() {
        if (deleteFormToSubmit) {
            deleteFormToSubmit.submit();
        }
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) {
                    hideDeleteModal();
                }
            });
        }
    });
</script>
@endsection

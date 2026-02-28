@extends('layouts.admin')

@section('title', 'Employee Management')

@section('page-title', 'Employee Management')
@section('styles')
<link rel="stylesheet" href="/build/assets/employees-B_IfWkp5.css"><script src="/build/assets/employees-CDxtRMBS.js" defer></script>
@endsection
@section('content')
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
<div class="employee-grid-container">
    <!-- Department Statistics -->
    <div class="department-stats">
        <div class="dept-cards">
            <div class="dept-card dept-total">
                <div class="dept-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"></path>
                    </svg>
                </div>
                <div class="dept-content">
                    <span class="dept-name">Total</span>
                    <div class="dept-bottom">
                        <span class="dept-count">{{ $stats['total'] }}</span>
                        <span class="dept-percentage">employees</span>
                    </div>
                </div>
            </div>
            <div class="dept-card dept-production">
                <div class="dept-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
                <div class="dept-content">
                    <span class="dept-name">Production</span>
                    <div class="dept-bottom">
                        <span class="dept-count">{{ $stats['by_department']['production'] }}</span>
                        <span class="dept-percentage">{{ $stats['total'] > 0 ? round(($stats['by_department']['production'] / $stats['total'] * 100), 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
            <div class="dept-card dept-warehouse">
                <div class="dept-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <div class="dept-content">
                    <span class="dept-name">Warehouse</span>
                    <div class="dept-bottom">
                        <span class="dept-count">{{ $stats['by_department']['warehouse'] }}</span>
                        <span class="dept-percentage">{{ $stats['total'] > 0 ? round(($stats['by_department']['warehouse'] / $stats['total'] * 100), 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
            <div class="dept-card dept-delivery">
                <div class="dept-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </div>
                <div class="dept-content">
                    <span class="dept-name">Delivery</span>
                    <div class="dept-bottom">
                        <span class="dept-count">{{ $stats['by_department']['delivery'] }}</span>
                        <span class="dept-percentage">{{ $stats['total'] > 0 ? round(($stats['by_department']['delivery'] / $stats['total'] * 100), 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        {{ $employees->appends(request()->query())->render('pagination.bootstrap-5') }}
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

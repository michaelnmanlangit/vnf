@extends('layouts.admin')

@section('title', 'Customers')

@section('page-title', 'Customers')
@section('styles')
@vite(['resources/css/billing.css', 'resources/css/employees-form.css'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
<!-- Back Button -->
<a href="{{ route('admin.billing.index') }}" class="manage-customers-btn" style="display: inline-flex; margin-bottom: 1rem;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="19" y1="12" x2="5" y2="12"></line>
        <polyline points="12 19 5 12 12 5"></polyline>
    </svg>
    Back to Invoices
</a>

<div class="billing-grid-container">
    <!-- Search & Filter Toolbar -->
    <div class="billing-toolbar">
        <form method="GET" action="{{ route('admin.billing.customers') }}" id="filterForm" class="toolbar-form">
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" name="search" placeholder="Search customers..." 
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
                            <div class="section-title">Status</div>
                            <label class="filter-option">
                                <input type="radio" name="status" value="" 
                                       {{ !request('status') ? 'checked' : '' }}>
                                <span>All Status</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="active" 
                                       {{ request('status') == 'active' ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="inactive" 
                                       {{ request('status') == 'inactive' ? 'checked' : '' }}>
                                <span>Inactive</span>
                            </label>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="filter-apply">Apply</button>
                            <a href="{{ route('admin.billing.customers') }}" class="filter-reset">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Actions -->
        <div class="toolbar-actions">
            @if(request('search') || request('status'))
                <a href="{{ route('admin.billing.customers') }}" class="clear-filters">Clear All</a>
            @endif
            <button type="button" class="add-invoice-btn" onclick="document.getElementById('customerModal').classList.add('active')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Customer
            </button>
        </div>
    </div>

    <!-- Customers List -->
    <div class="billing-list">
        <table class="billing-table">
            <thead>
                <tr>
                    <th class="sortable" data-column="business_name">
                        Business Name
                        <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M19 12l-7 7-7-7"></path>
                        </svg>
                    </th>
                    <th>Contact Person</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td><strong>{{ $customer->business_name }}</strong></td>
                        <td>{{ $customer->contact_person }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $customer->customer_type)) }}</td>
                        <td>
                            <span class="status-badge {{ $customer->status }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn-action edit" onclick="editCustomer({{ $customer->id }})" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <form action="{{ route('admin.billing.customer.delete', $customer->id) }}" method="POST" style="display: inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete" onclick="showDeleteModal('{{ $customer->id }}', '{{ $customer->business_name }}')" title="Delete">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <h3>No customers found</h3>
                                <p>{{ request('search') || request('status') ? 'Try adjusting your filters' : 'Add your first customer to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add Customer Modal -->
<div id="customerModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Customer</h3>
        </div>

        <form action="{{ route('admin.billing.customer.store') }}" method="POST" class="customer-form">
            @csrf
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Business Name <span class="required">*</span></label>
                        <input type="text" name="business_name" required>
                    </div>

                    <div class="form-group">
                        <label>Contact Person <span class="required">*</span></label>
                        <input type="text" name="contact_person" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>

                    <div class="form-group">
                        <label>Phone <span class="required">*</span></label>
                        <input type="text" name="phone" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Customer Type <span class="required">*</span></label>
                        <select name="customer_type" required>
                            <option value="">Select Customer Type</option>
                            <option value="wet_market">Wet Market</option>
                            <option value="restaurant">Restaurant</option>
                            <option value="meat_supplier">Meat Supplier</option>
                            <option value="fishery">Fishery</option>
                            <option value="grocery">Grocery</option>
                            <option value="distribution_company">Distribution Company</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status <span class="required">*</span></label>
                        <select name="status" required>
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Address <span class="required">*</span></label>
                        <textarea name="address" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" id="addCustomerButton">Add Customer</button>
                <button type="button" class="btn-cancel" onclick="document.getElementById('customerModal').classList.remove('active')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal -->
<div id="editCustomerModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Customer</h3>
        </div>

        <form id="editCustomerForm" method="POST" class="customer-form">
            @csrf
            @method('PUT')
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Business Name <span class="required">*</span></label>
                        <input type="text" name="business_name" id="edit_business_name" required>
                    </div>

                    <div class="form-group">
                        <label>Contact Person <span class="required">*</span></label>
                        <input type="text" name="contact_person" id="edit_contact_person" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email">
                    </div>

                    <div class="form-group">
                        <label>Phone <span class="required">*</span></label>
                        <input type="text" name="phone" id="edit_phone" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Customer Type <span class="required">*</span></label>
                        <select name="customer_type" id="edit_customer_type" required>
                            <option value="">Select Customer Type</option>
                            <option value="wet_market">Wet Market</option>
                            <option value="restaurant">Restaurant</option>
                            <option value="meat_supplier">Meat Supplier</option>
                            <option value="fishery">Fishery</option>
                            <option value="grocery">Grocery</option>
                            <option value="distribution_company">Distribution Company</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status <span class="required">*</span></label>
                        <select name="status" id="edit_status" required>
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Address <span class="required">*</span></label>
                        <textarea name="address" id="edit_address" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" id="edit_notes" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" id="updateCustomerButton">Update Customer</button>
                <button type="button" class="btn-cancel" onclick="document.getElementById('editCustomerModal').classList.remove('active')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete customer <strong id="customerToDelete"></strong>? <span style="color: #dc3545; font-weight: 600;">This action cannot be undone.</span></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="hideDeleteModal()">Cancel</button>
            <button type="button" class="btn-modal btn-confirm" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

<script>
const customers = @json($customers->items());
let initialCustomerFormState = {};

function editCustomer(id) {
    const customer = customers.find(c => c.id === id);
    if (!customer) return;

    document.getElementById('editCustomerForm').action = `/admin/billing/customer/${id}`;
    document.getElementById('edit_business_name').value = customer.business_name;
    document.getElementById('edit_contact_person').value = customer.contact_person;
    document.getElementById('edit_email').value = customer.email || '';
    document.getElementById('edit_phone').value = customer.phone;
    document.getElementById('edit_customer_type').value = customer.customer_type;
    document.getElementById('edit_status').value = customer.status;
    document.getElementById('edit_address').value = customer.address;
    document.getElementById('edit_notes').value = customer.notes || '';

    // Store initial form state
    const editForm = document.getElementById('editCustomerForm');
    const formData = new FormData(editForm);
    initialCustomerFormState = {};
    formData.forEach((value, key) => {
        initialCustomerFormState[key] = value;
    });

    // Reset button state
    const updateButton = document.getElementById('updateCustomerButton');
    updateButton.disabled = true;
    updateButton.style.opacity = '0.5';
    updateButton.style.cursor = 'not-allowed';

    // Add change listeners
    const formInputs = editForm.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('change', checkCustomerFormChanges);
        input.addEventListener('input', checkCustomerFormChanges);
    });

    document.getElementById('editCustomerModal').classList.add('active');
}

function checkCustomerFormChanges() {
    const editForm = document.getElementById('editCustomerForm');
    const currentFormState = new FormData(editForm);
    let hasChanges = false;
    
    // Check if any field has changed
    for (let [key, value] of currentFormState.entries()) {
        if (initialCustomerFormState[key] !== value) {
            hasChanges = true;
            break;
        }
    }
    
    const updateButton = document.getElementById('updateCustomerButton');
    
    // Enable or disable button based on changes
    if (hasChanges) {
        updateButton.disabled = false;
        updateButton.style.opacity = '1';
        updateButton.style.cursor = 'pointer';
    } else {
        updateButton.disabled = true;
        updateButton.style.opacity = '0.5';
        updateButton.style.cursor = 'not-allowed';
    }
}

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
    }

    // Table column sorting
    const sortableHeaders = document.querySelectorAll('th.sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-column');
            const isAsc = this.classList.contains('asc');
            
            // Remove active class from all headers
            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc');
            });
            
            // Determine sort direction
            const sortDirection = isAsc ? 'desc' : 'asc';
            this.classList.add(sortDirection);
            
            // Build URL with filters and sort parameters
            const search = document.querySelector('input[name="search"]') ? document.querySelector('input[name="search"]').value : '';
            const statusChecked = document.querySelector('input[name="status"]:checked');
            const status = statusChecked ? statusChecked.value : '';
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (status) params.append('status', status);
            params.append('sort_column', column);
            params.append('sort_direction', sortDirection);
            
            // Navigate to URL with query parameters
            window.location.href = '{{ route("admin.billing.customers") }}' + '?' + params.toString();
        });
    });

    // Restore sort state from query parameters
    const urlParams = new URLSearchParams(window.location.search);
    const sortColumn = urlParams.get('sort_column');
    const sortDirection = urlParams.get('sort_direction');
    if (sortColumn) {
        const header = document.querySelector(`th.sortable[data-column="${sortColumn}"]`);
        if (header) {
            header.classList.add(sortDirection || 'asc');
        }
    }
});
// Delete modal functions
let deleteFormToSubmit = null;

function showDeleteModal(customerId, customerName) {
    document.getElementById('customerToDelete').textContent = customerName;
    document.getElementById('deleteModal').classList.add('active');
    deleteFormToSubmit = document.querySelector('form.delete-form');
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
document.addEventListener('click', function(e) {
    const modal = document.getElementById('deleteModal');
    if (e.target === modal) {
        hideDeleteModal();
    }
    const customerModal = document.getElementById('customerModal');
    if (e.target === customerModal) {
        customerModal.classList.remove('active');
    }
    const editCustomerModal = document.getElementById('editCustomerModal');
    if (e.target === editCustomerModal) {
        editCustomerModal.classList.remove('active');
    }
});

// Enhanced form change detection like employee form
document.addEventListener('DOMContentLoaded', function() {
    // Add customer form change detection
    const addCustomerForm = document.querySelector('#customerModal .customer-form');
    const editCustomerForm = document.querySelector('#editCustomerModal .customer-form');
    
    if (addCustomerForm) {
        const addButton = document.getElementById('addCustomerButton');
        const addFormInputs = addCustomerForm.querySelectorAll('input, select, textarea');
        
        let addFormTouched = false;
        
        addFormInputs.forEach(input => {
            input.addEventListener('change', function() {
                addFormTouched = true;
                addButton.disabled = false;
                addButton.style.opacity = '1';
                addButton.style.cursor = 'pointer';
            });
            input.addEventListener('input', function() {
                addFormTouched = true;
                addButton.disabled = false;
                addButton.style.opacity = '1';
                addButton.style.cursor = 'pointer';
            });
        });
    }
});</script>
@endsection

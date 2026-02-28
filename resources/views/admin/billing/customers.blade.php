@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.warehouse')

@section('title', 'Customers')

@section('page-title', 'Customers')
@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css"><link rel="stylesheet" href="/build/assets/employees-form-BzD5O2VJ.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
            <button type="button" class="add-invoice-btn" onclick="openAddCustomerModal()">
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
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Address <span class="required">*</span></label>
                        <textarea name="address" id="add_address_field" rows="2" required placeholder="Type address or click the map to pin location"></textarea>
                        <button type="button" onclick="searchAddressOnMap('add')" style="display:block;margin-top:.5rem;padding:.65rem 1.1rem;background:var(--secondary-color);color:#fff;border:none;border-radius:8px;font-family:Poppins,sans-serif;font-size:.875rem;font-weight:600;cursor:pointer;transition:all .3s;width:100%;letter-spacing:.2px;" onmouseover="this.style.background='#2980b9';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(52,152,219,0.35)'" onmouseout="this.style.background='var(--secondary-color)';this.style.transform='';this.style.boxShadow=''">
                            Search &amp; Pin on Map
                        </button>
                        <div id="add-address-map" style="height:200px;border-radius:8px;margin-top:.5rem;border:1px solid var(--border-color);overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.07);"></div>
                        <p style="font-size:.78rem;color:var(--text-light);margin:.4rem 0 0;display:flex;align-items:flex-start;gap:.35rem;line-height:1.4;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Click on the map to drop a delivery pin, or type the address above then click "Search &amp; Pin on Map".
                        </p>
                        <input type="hidden" name="latitude" id="add_latitude">
                        <input type="hidden" name="longitude" id="add_longitude">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Notes</label>
                        <textarea name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Save Customer</button>
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
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Address <span class="required">*</span></label>
                        <textarea name="address" id="edit_address" rows="2" required placeholder="Type address or click the map to pin location"></textarea>
                        <button type="button" onclick="searchAddressOnMap('edit')" style="display:block;margin-top:.5rem;padding:.65rem 1.1rem;background:var(--secondary-color);color:#fff;border:none;border-radius:8px;font-family:Poppins,sans-serif;font-size:.875rem;font-weight:600;cursor:pointer;transition:all .3s;width:100%;letter-spacing:.2px;" onmouseover="this.style.background='#2980b9';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(52,152,219,0.35)'" onmouseout="this.style.background='var(--secondary-color)';this.style.transform='';this.style.boxShadow=''">
                            Search &amp; Pin on Map
                        </button>
                        <div id="edit-address-map" style="height:200px;border-radius:8px;margin-top:.5rem;border:1px solid var(--border-color);overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.07);"></div>
                        <p style="font-size:.78rem;color:var(--text-light);margin:.4rem 0 0;display:flex;align-items:flex-start;gap:.35rem;line-height:1.4;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Click on the map to drop a delivery pin, or type the address above then click "Search &amp; Pin on Map".
                        </p>
                        <input type="hidden" name="latitude" id="edit_latitude">
                        <input type="hidden" name="longitude" id="edit_longitude">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Notes</label>
                        <textarea name="notes" id="edit_notes" rows="2"></textarea>
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
    document.getElementById('edit_latitude').value  = customer.latitude  || '';
    document.getElementById('edit_longitude').value = customer.longitude || '';

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
    setTimeout(() => initPickerMap('edit'), 150);
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
});
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ─── Map Pickers ─────────────────────────────────────────────────────────────
const GEO_KEY       = '328a40dae9644da6a37cd0a608800fa2';
const PICKER_CENTER = [14.1077, 121.1411]; // Santo Tomas, Batangas

let addPickerMap = null, addPickerMarker = null;
let editPickerMap = null, editPickerMarker = null;

function openAddCustomerModal() {
    document.getElementById('customerModal').classList.add('active');
    setTimeout(() => initPickerMap('add'), 150);
}

function initPickerMap(prefix) {
    const isAdd = prefix === 'add';
    const mapEl = document.getElementById(prefix + '-address-map');

    if (isAdd && addPickerMap) {
        setTimeout(() => addPickerMap.invalidateSize(), 50);
        return;
    }
    if (!isAdd && editPickerMap) {
        const lat = parseFloat(document.getElementById('edit_latitude').value);
        const lng = parseFloat(document.getElementById('edit_longitude').value);
        if (lat && lng) {
            if (editPickerMarker) editPickerMap.removeLayer(editPickerMarker);
            editPickerMarker = L.marker([lat, lng]).addTo(editPickerMap);
            editPickerMap.setView([lat, lng], 15);
        } else {
            editPickerMap.setView(PICKER_CENTER, 13);
        }
        setTimeout(() => editPickerMap.invalidateSize(), 50);
        return;
    }

    const lat = !isAdd ? parseFloat(document.getElementById('edit_latitude').value) : null;
    const lng = !isAdd ? parseFloat(document.getElementById('edit_longitude').value) : null;
    const center = (lat && lng) ? [lat, lng] : PICKER_CENTER;
    const zoom   = (lat && lng) ? 15 : 13;

    const map = L.map(mapEl, { attributionControl: false }).setView(center, zoom);
    L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEO_KEY}`, { maxZoom: 20 }).addTo(map);

    if (isAdd) {
        addPickerMap = map;
        map.on('click', e => setPickerPin('add', e.latlng.lat, e.latlng.lng, true));
    } else {
        editPickerMap = map;
        if (lat && lng) {
            editPickerMarker = L.marker([lat, lng]).addTo(map);
        }
        map.on('click', e => setPickerPin('edit', e.latlng.lat, e.latlng.lng, true));
    }
    setTimeout(() => map.invalidateSize(), 100);
}

function setPickerPin(prefix, lat, lng, reverseGeo) {
    const isAdd = prefix === 'add';
    const map   = isAdd ? addPickerMap : editPickerMap;

    if (isAdd) {
        if (addPickerMarker) addPickerMap.removeLayer(addPickerMarker);
        addPickerMarker = L.marker([lat, lng]).addTo(addPickerMap);
        document.getElementById('add_latitude').value  = lat;
        document.getElementById('add_longitude').value = lng;
    } else {
        if (editPickerMarker) editPickerMap.removeLayer(editPickerMarker);
        editPickerMarker = L.marker([lat, lng]).addTo(editPickerMap);
        document.getElementById('edit_latitude').value  = lat;
        document.getElementById('edit_longitude').value = lng;
        checkCustomerFormChanges();
    }
    map.panTo([lat, lng]);

    if (reverseGeo) {
        fetch(`https://api.geoapify.com/v1/geocode/reverse?lat=${lat}&lon=${lng}&apiKey=${GEO_KEY}`)
            .then(r => r.json())
            .then(data => {
                if (data.features?.length > 0) {
                    const p = data.features[0].properties;
                    // Build address from street components only — no POI/landmark names
                    const parts = [];
                    if (p.housenumber && p.street)      parts.push(p.housenumber + ' ' + p.street);
                    else if (p.street)                  parts.push(p.street);
                    if (p.suburb && p.suburb !== p.city) parts.push(p.suburb);
                    if (p.city || p.town || p.village)  parts.push(p.city || p.town || p.village);
                    if (p.state)                        parts.push(p.state);
                    const addr = parts.length > 0 ? parts.join(', ') : p.formatted;
                    const fieldId = isAdd ? 'add_address_field' : 'edit_address';
                    const field   = document.getElementById(fieldId);
                    if (field) {
                        field.value = addr;
                        if (!isAdd) checkCustomerFormChanges();
                    }
                }
            }).catch(() => {});
    }
}

function searchAddressOnMap(prefix) {
    const fieldId = prefix === 'add' ? 'add_address_field' : 'edit_address';
    const query   = document.getElementById(fieldId)?.value?.trim();
    if (!query) { alert('Please enter an address first.'); return; }

    fetch(`https://api.geoapify.com/v1/geocode/search?text=${encodeURIComponent(query)}&apiKey=${GEO_KEY}&filter=countrycode:ph&bias=proximity:121.1411,14.1077`)
        .then(r => r.json())
        .then(data => {
            if (data.features?.length > 0) {
                const [lon, lat] = data.features[0].geometry.coordinates;
                setPickerPin(prefix, lat, lon, false);
                const m = prefix === 'add' ? addPickerMap : editPickerMap;
                m.setView([lat, lon], 16);
            } else {
                alert('Address not found on map. Try clicking on the map to pin manually.');
            }
        }).catch(() => alert('Search failed. Please try clicking directly on the map.'));
}
</script>
@endsection

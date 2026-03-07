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
                                <button type="button" class="btn-action view" onclick="viewCustomer({{ $customer->id }})" title="View">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    View
                                </button>
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
                                <p>{{ request('search') || request('status') ? 'Try adjusting your filters' : 'No registered customers yet' }}</p>
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

<!-- Customer Info Modal -->
<div class="modal-overlay" id="customerInfoModal">
    <div class="modal-content" style="max-width:520px;position:relative;">
        <button type="button" onclick="document.getElementById('customerInfoModal').classList.remove('active')" style="position:absolute;top:1rem;right:1rem;background:none;border:none;cursor:pointer;color:var(--text-light);padding:0;line-height:1;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        <div class="modal-header">
            <h3 id="modal_business_name" style="text-align:left;"></h3>
        </div>
        <div class="modal-body">
            <div style="display:grid;grid-template-columns:140px 1fr;gap:0;font-size:.9rem;">
                <div style="padding:.6rem .75rem .6rem 0;color:var(--text-light);font-weight:600;border-bottom:1px solid var(--border-color);text-align:left;">Contact Person</div>
                <div style="padding:.6rem 0;border-bottom:1px solid var(--border-color);text-align:left;" id="modal_contact_person"></div>
                <div style="padding:.6rem .75rem .6rem 0;color:var(--text-light);font-weight:600;border-bottom:1px solid var(--border-color);text-align:left;">Email</div>
                <div style="padding:.6rem 0;border-bottom:1px solid var(--border-color);text-align:left;" id="modal_email"></div>
                <div style="padding:.6rem .75rem .6rem 0;color:var(--text-light);font-weight:600;border-bottom:1px solid var(--border-color);text-align:left;">Phone</div>
                <div style="padding:.6rem 0;border-bottom:1px solid var(--border-color);text-align:left;" id="modal_phone"></div>
                <div style="padding:.6rem .75rem .6rem 0;color:var(--text-light);font-weight:600;border-bottom:1px solid var(--border-color);text-align:left;">Customer Type</div>
                <div style="padding:.6rem 0;border-bottom:1px solid var(--border-color);text-align:left;" id="modal_customer_type"></div>
                <div style="padding:.6rem .75rem .6rem 0;color:var(--text-light);font-weight:600;border-bottom:1px solid var(--border-color);text-align:left;">Address</div>
                <div style="padding:.6rem 0;border-bottom:1px solid var(--border-color);text-align:left;" id="modal_address"></div>
                <div style="padding:.6rem .75rem .6rem 0;color:var(--text-light);font-weight:600;text-align:left;" id="modal_notes_label">Notes</div>
                <div style="padding:.6rem 0;text-align:left;" id="modal_notes"></div>
            </div>
        </div>
    </div>
</div>

<script>
const customerData = @json($customers->items());

function viewCustomer(id) {
    const c = customerData.find(x => x.id === id);
    if (!c) return;
    document.getElementById('modal_business_name').textContent = c.business_name;
    document.getElementById('modal_contact_person').textContent = c.contact_person;
    document.getElementById('modal_email').textContent = c.email || '—';
    document.getElementById('modal_phone').textContent = c.phone;
    document.getElementById('modal_customer_type').textContent = c.customer_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    document.getElementById('modal_address').textContent = c.address;
    const notesLabel = document.getElementById('modal_notes_label');
    if (c.notes) {
        document.getElementById('modal_notes').textContent = c.notes;
        notesLabel.style.display = '';
        document.getElementById('modal_notes').style.display = '';
    } else {
        notesLabel.style.display = 'none';
        document.getElementById('modal_notes').style.display = 'none';
    }
    document.getElementById('customerInfoModal').classList.add('active');
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('customerInfoModal');
    if (e.target === modal) modal.classList.remove('active');
});

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
</script>
@endsection

@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.warehouse')

@section('title', 'Billing & Invoices')

@section('page-title', 'Billing & Invoices')
@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">
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
<div class="billing-grid-container">
    <!-- Status Statistics -->
    <div class="status-stats">
        <div class="status-cards">
            <div class="status-card status-total">
                <div class="status-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div class="status-content">
                    <span class="status-name">Total Invoices</span>
                    <div class="status-bottom">
                        <span class="status-count">{{ $stats['total'] }}</span>
                    </div>
                </div>
            </div>
            <div class="status-card status-pending">
                <div class="status-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="status-content">
                    <span class="status-name">Pending</span>
                    <div class="status-bottom">
                        <span class="status-count">{{ $stats['pending'] }}</span>
                        <span class="status-percentage">{{ $stats['total'] > 0 ? round(($stats['pending'] / $stats['total'] * 100), 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
            <div class="status-card status-paid">
                <div class="status-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="status-content">
                    <span class="status-name">Paid</span>
                    <div class="status-bottom">
                        <span class="status-count">{{ $stats['paid'] }}</span>
                        <span class="status-percentage">{{ $stats['total'] > 0 ? round(($stats['paid'] / $stats['total'] * 100), 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
            <div class="status-card status-overdue">
                <div class="status-icon">
                    <span style="font-size: 2rem; font-weight: 700; line-height: 1;">!</span>
                </div>
                <div class="status-content">
                    <span class="status-name">Overdue</span>
                    <div class="status-bottom">
                        <span class="status-count">{{ $stats['overdue'] }}</span>
                        <span class="status-percentage">{{ $stats['total'] > 0 ? round(($stats['overdue'] / $stats['total'] * 100), 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
            <div class="status-card status-revenue">
                <div class="status-icon">
                    <span style="font-size: 1.8rem; font-weight: 700;">₱</span>
                </div>
                <div class="status-content">
                    <span class="status-name">Total Revenue</span>
                    <div class="status-bottom">
                        <span class="status-amount">₱{{ number_format($stats['total_amount'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="billing-toolbar">
        <form method="GET" action="{{ route('admin.billing.index') }}" id="filterForm" class="toolbar-form">
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by invoice or customer..." 
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
                                <input type="radio" name="status" value="pending" 
                                       {{ request('status') == 'pending' ? 'checked' : '' }}>
                                <span>Pending</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="paid" 
                                       {{ request('status') == 'paid' ? 'checked' : '' }}>
                                <span>Paid</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="partially_paid" 
                                       {{ request('status') == 'partially_paid' ? 'checked' : '' }}>
                                <span>Partially Paid</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="overdue" 
                                       {{ request('status') == 'overdue' ? 'checked' : '' }}>
                                <span>Overdue</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="cancelled" 
                                       {{ request('status') == 'cancelled' ? 'checked' : '' }}>
                                <span>Cancelled</span>
                            </label>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="filter-apply">Apply</button>
                            <a href="{{ route('admin.billing.index') }}" class="filter-reset">Reset</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="toolbar-actions">
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.billing.index') }}" class="clear-filters">Clear All</a>
                @endif
                <a href="{{ route('admin.billing.customers') }}" class="manage-customers-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Customers
                </a>
                <a href="{{ route('admin.billing.create') }}" class="add-invoice-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    New Invoice
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices List -->
    <div class="billing-list">
        <table class="billing-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th class="sortable" data-column="customer">
                        Customer
                        <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M19 12l-7 7-7-7"></path>
                        </svg>
                    </th>
                    <th>Invoice Date</th>
                    <th>Due Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>
                            <strong>{{ $invoice->invoice_number }}</strong>
                        </td>
                        <td>{{ $invoice->customer->business_name }}</td>
                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                        <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                        <td>₱{{ number_format($invoice->total_amount, 2) }}</td>
                        <td>
                            <span class="status-badge {{ $invoice->status }}">
                                {{ ucwords(str_replace('_', ' ', $invoice->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.billing.show', $invoice->id) }}" class="btn-action view" title="View">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('admin.billing.edit', $invoice->id) }}" class="btn-action edit" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('admin.billing.destroy', $invoice->id) }}" method="POST" style="display: inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete" onclick="showDeleteModal('{{ $invoice->id }}', '{{ $invoice->invoice_number }}')" title="Delete">
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
                        <td colspan="7">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <h3>No invoices found</h3>
                                <p>{{ request('search') || request('status') ? 'Try adjusting your filters' : 'Create your first invoice to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination">
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete invoice <strong id="invoiceToDelete"></strong>? <span style="color: #dc3545; font-weight: 600;">This action cannot be undone.</span></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="hideDeleteModal()">Cancel</button>
            <button type="button" class="btn-modal btn-confirm" onclick="confirmDelete()">Delete</button>
        </div>
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
    }

    // Table horizontal scroll indicator
    const billingList = document.querySelector('.billing-list');
    if (billingList) {
        function updateScrollIndicator() {
            const isScrolledToEnd = billingList.scrollLeft + billingList.clientWidth >= billingList.scrollWidth - 5;
            if (isScrolledToEnd) {
                billingList.classList.add('scrolled-end');
            } else {
                billingList.classList.remove('scrolled-end');
            }
            
            // Mark as scrolled to hide hint
            if (billingList.scrollLeft > 10) {
                billingList.classList.add('scrolled');
            }
        }
        
        billingList.addEventListener('scroll', updateScrollIndicator);
        updateScrollIndicator(); // Initial check
        
        // Add touch feedback for mobile
        let isScrolling;
        billingList.addEventListener('scroll', function() {
            billingList.style.cursor = 'grabbing';
            clearTimeout(isScrolling);
            isScrolling = setTimeout(function() {
                billingList.style.cursor = 'grab';
            }, 150);
        });
    }

    // Table sorting
    const sortableHeaders = document.querySelectorAll('.sortable');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-column');
            const currentUrl = new URL(window.location.href);
            const currentSort = currentUrl.searchParams.get('sort_column');
            const currentDirection = currentUrl.searchParams.get('sort_direction') || 'asc';
            
            let newDirection = 'asc';
            if (currentSort === column && currentDirection === 'asc') {
                newDirection = 'desc';
            }
            
            // Build new URL with all existing filters
            const params = new URLSearchParams();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && searchInput.value) {
                params.set('search', searchInput.value);
            }
            
            // Get selected statuses from checkboxes
            const statusCheckboxes = document.querySelectorAll('input[name="status[]"]:checked');
            statusCheckboxes.forEach(checkbox => {
                params.append('status[]', checkbox.value);
            });
            
            params.set('sort_column', column);
            params.set('sort_direction', newDirection);
            
            window.location.href = '{{ route("admin.billing.index") }}?' + params.toString();
        });
    });
    
    // Restore sort state
    const urlParams = new URLSearchParams(window.location.search);
    const sortColumn = urlParams.get('sort_column');
    const sortDirection = urlParams.get('sort_direction');
    
    if (sortColumn && sortDirection) {
        const sortedHeader = document.querySelector(`.sortable[data-column="${sortColumn}"]`);
        if (sortedHeader) {
            sortedHeader.classList.add(sortDirection);
        }
    }
});

// Delete modal functions
let deleteFormToSubmit = null;

function showDeleteModal(invoiceId, invoiceNumber) {
    document.getElementById('invoiceToDelete').textContent = invoiceNumber;
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
});
</script>
@endsection

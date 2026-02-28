@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.warehouse')

@section('title', 'Inventory Management')

@section('page-title', 'Inventory Management')
@php
    $r = auth()->user()->role === 'admin' ? 'admin.inventory' : 'inventory';
@endphp
@section('styles')
<link rel="stylesheet" href="/build/assets/inventory-Wqoz_iPC.css"><link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css"><script src="/build/assets/inventory-eb_1lA51.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
@section('content')
@if(session('success'))
    <div class="alert alert-success" id="successAlert" style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('successAlert').remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;line-height:1;">&times;</button>
    </div>
@endif
<div class="inventory-grid-container">

    {{-- Stat Tiles --}}
    <div class="status-stats">
        <div class="status-cards">
            <div class="status-card status-total">
                <div class="status-icon"><i class="fas fa-warehouse" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Total Items</span>
                    <div class="status-bottom"><span class="status-count">{{ $totalItems }}</span></div>
                </div>
            </div>
            <div class="status-card status-paid">
                <div class="status-icon"><i class="fas fa-check-circle" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">In Stock</span>
                    <div class="status-bottom"><span class="status-count">{{ $inStockCount }}</span></div>
                </div>
            </div>
            <div class="status-card status-pending">
                <div class="status-icon"><i class="fas fa-exclamation-triangle" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Low Stock</span>
                    <div class="status-bottom"><span class="status-count">{{ $lowStockCount }}</span></div>
                </div>
            </div>
            <div class="status-card status-partial">
                <div class="status-icon"><i class="fas fa-clock" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Expiring Soon</span>
                    <div class="status-bottom"><span class="status-count">{{ $expiringSoonCount }}</span></div>
                </div>
            </div>
            <div class="status-card status-overdue">
                <div class="status-icon"><i class="fas fa-ban" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Expired</span>
                    <div class="status-bottom"><span class="status-count">{{ $expiredCount }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="inventory-toolbar">
        <form method="GET" action="{{ route($r . '.index') }}" id="filterForm" class="toolbar-form">
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by product name..." 
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
                            <div class="section-title">Category</div>
                            <label class="filter-option">
                                <input type="radio" name="category" value="" 
                                       {{ !request('category') ? 'checked' : '' }}>
                                <span>All Categories</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="filter-option">
                                    <input type="radio" name="category" value="{{ $cat }}" 
                                           {{ request('category') == $cat ? 'checked' : '' }}>
                                    <span>{{ ucfirst($cat) }}</span>
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
                            <a href="{{ route($r . '.index') }}" class="filter-reset">Reset</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="toolbar-actions">
                @if(request('search') || request('category') || request('status'))
                    <a href="{{ route($r . '.index') }}" class="clear-filters">Clear All</a>
                @endif
                <a href="{{ route($r . '.create') }}" class="add-inventory-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Inventory
                </a>
            </div>
        </form>
    </div>

    <!-- Inventory List -->
    @if($inventory->count() > 0)
        <div class="inventory-list">
            <table class="inventory-table" data-route="{{ route($r . '.index') }}">
                <thead>
                    <tr>
                        <th class="sortable" data-column="id">
                            ID
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th class="sortable" data-column="product_name">
                            Product Name
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th class="sortable" data-column="category">
                            Category
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th class="sortable" data-column="quantity">
                            Quantity
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th class="sortable" data-column="status">
                            Status
                            <svg class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M19 12l-7 7-7-7"></path>
                            </svg>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                    <tr class="inventory-row">
                        <td class="inventory-id">{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="inventory-name">{{ $item->product_name }}</td>
                        <td>{{ ucfirst($item->category) }}</td>
                        <td>{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                        <td>
                            <span class="status-badge status-{{ str_replace('_', '-', $item->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route($r . '.edit', $item) }}" class="btn-action edit" title="Edit Item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route($r . '.destroy', $item) }}" style="display: inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action delete" onclick="showDeleteModal('{{ $item->id }}', '{{ $item->product_name }}')">
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
            <div class="empty-state-icon">📦</div>
            <h2>No Inventory Items Yet</h2>
            <p>Get started by adding your first inventory item to the system.</p>
            <a href="{{ route($r . '.create') }}" class="empty-state-btn">Add First Item</a>
        </div>
    @endif

    <!-- Pagination -->
    @if($inventory->hasPages())
        {{ $inventory->appends(request()->query())->render('pagination.bootstrap-5') }}
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete <strong id="itemToDelete"></strong>? <span style="color: #dc3545; font-weight: 600;">This action cannot be undone.</span></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="hideDeleteModal()">Cancel</button>
            <button type="button" class="btn-modal btn-confirm" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/build/assets/inventory-Wqoz_iPC.css"><script src="/build/assets/inventory-eb_1lA51.js" defer></script>
<script>
    let deleteFormToSubmit = null;

    function showDeleteModal(itemId, itemName) {
        document.getElementById('itemToDelete').textContent = itemName;
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

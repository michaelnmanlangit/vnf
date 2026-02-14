@extends('layouts.warehouse')

@section('title', 'Temperature Monitoring')

@section('page-title', 'Temperature Monitoring')

@section('styles')
@vite(['resources/css/inventory.css', 'resources/js/inventory.js'])
@endsection

@section('content')
<div class="inventory-grid-container">
    <!-- Search & Filter Toolbar -->
    <div class="inventory-toolbar">
        <form method="GET" action="{{ route('warehouse.temperature.index') }}" id="filterForm" class="toolbar-form">
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by product name..." 
                       value="{{ request('search') }}">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>

            <!-- Actions -->
            <div class="toolbar-actions">
                @if(request('search'))
                    <a href="{{ route('warehouse.temperature.index') }}" class="clear-filters">Clear All</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Temperature List -->
    @if($inventory->count() > 0)
        <div class="inventory-list">
            <table class="inventory-table" data-route="{{ route('warehouse.temperature.index') }}">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Location</th>
                        <th>Storage Temp</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                    <tr class="inventory-row">
                        <td class="inventory-id">{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="inventory-name">{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->storage_temperature ?? 'Room Temp' }}</td>
                        <td>{{ $item->updated_at->format('M d, Y') }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('warehouse.temperature.show', $item) }}" class="btn-action view" title="View Details">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">🌡️</div>
            <h2>No Items</h2>
            <p>No inventory items available for temperature monitoring.</p>
        </div>
    @endif

    <!-- Pagination -->
    @if($inventory->hasPages())
        {{ $inventory->appends(request()->query())->render('pagination.bootstrap-5') }}
    @endif
</div>

@vite(['resources/css/inventory.css', 'resources/js/inventory.js'])
<script>
    // Filter toggle functionality
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle) {
        filterToggle.addEventListener('click', function() {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });
    }

    // Close filter dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const filterGroup = document.querySelector('.filter-group');
        if (filterGroup && !filterGroup.contains(e.target)) {
            const dropdown = document.getElementById('filterDropdown');
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        }
    });
</script>
@endsection

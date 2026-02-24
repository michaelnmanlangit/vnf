@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.warehouse')

@section('title', 'Storage Unit Details')

@section('page-title', $unit->name)

@section('styles')
<style>
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        color: #374151;
        text-decoration: none;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        font-weight: 600;
        transition: all 0.2s;
        margin-bottom: 2rem;
    }

    .back-button:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .status-badge-large {
        padding: 0.75rem 1.5rem;
        border-radius: 9999px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge-large.normal {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge-large.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge-large.critical {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-badge-large.no_data {
        background: #e5e7eb;
        color: #374151;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .detail-card {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .temp-display-large {
        text-align: center;
        padding: 2rem;
        border-radius: 1rem;
        color: white;
        margin-bottom: 1.5rem;
    }

    .temp-display-large.normal {
        background: #10b981;
    }

    .temp-display-large.warning {
        background: #f59e0b;
    }

    .temp-display-large.critical {
        background: #ef4444;
    }

    .temp-display-large.no_data {
        background: #6b7280;
    }

    .temp-value-large {
        font-size: 4rem;
        font-weight: 700;
        line-height: 1;
    }

    .temp-unit-large {
        font-size: 2rem;
    }

    .temp-label-large {
        font-size: 1rem;
        opacity: 0.9;
        margin-top: 0.5rem;
    }

    .temp-range-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .range-item {
        text-align: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.5rem;
    }

    .range-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .range-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
    }

    .products-table-container {
        max-height: 500px;
        overflow-y: auto;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
    }

    .products-table thead {
        background: #2c3e50;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .products-table thead th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .products-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .products-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .products-table tbody tr:last-child {
        border-bottom: none;
    }

    .products-table tbody td {
        padding: 1rem 1.5rem;
        color: #111827;
        font-size: 0.95rem;
    }

    .product-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .category-badge {
        display: inline-block;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .category-badge.meat {
        background: #fee2e2;
        color: #991b1b;
    }

    .category-badge.seafood {
        background: #dbeafe;
        color: #1e40af;
    }

    .category-badge.vegetables {
        background: #d1fae5;
        color: #065f46;
    }

    .category-badge.ice {
        background: #e0f2fe;
        color: #075985;
    }

    .category-badge.dairy {
        background: #fef3c7;
        color: #92400e;
    }

    .category-badge.frozen {
        background: #e0e7ff;
        color: #3730a3;
    }

    .category-badge.fruits {
        background: #fef9c3;
        color: #854d0e;
    }

    .product-qty {
        color: #6b7280;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .detail-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .detail-btn-primary {
        background: #3498db;
        color: white;
    }

    .detail-btn-primary:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    .detail-btn-secondary {
        background: white;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .detail-btn-secondary:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .empty-products {
        text-align: center;
        padding: 3rem;
        color: #6b7280;
    }

    /* Modal Button Styles */
    .modal-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .modal-btn-cancel {
        background: white;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .modal-btn-cancel:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .modal-btn-submit {
        background: #3498db;
        color: white;
    }

    .modal-btn-submit:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    /* Alert Messages */
    .alert {
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* History Modal Styles */
    .history-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .history-modal.active {
        display: flex;
    }

    .history-modal-content {
        background: white;
        border-radius: 1rem;
        max-width: 900px;
        width: 95%;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        display: flex;
        flex-direction: column;
    }

    .history-modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .history-modal-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .history-close-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #6b7280;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .history-close-btn:hover {
        background: #f3f4f6;
        color: #374151;
    }

    .history-modal-body {
        padding: 1.5rem;
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .history-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: end;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
        min-width: 200px;
    }

    .filter-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
    }

    .filter-input {
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .filter-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-btn {
        padding: 0.75rem 1.5rem;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 600;
        height: fit-content;
    }

    .filter-btn:hover {
        background: #2980b9;
    }

    .history-table-container {
        flex: 1;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: white;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table thead {
        background: #f8fafc;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .history-table thead th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .history-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.2s;
    }

    .history-table tbody tr:hover {
        background: #f9fafb;
    }

    .history-table tbody tr:last-child {
        border-bottom: none;
    }

    .history-table tbody td {
        padding: 1rem 1.5rem;
        font-size: 0.875rem;
        color: #111827;
        vertical-align: middle;
    }

    .temp-value {
        font-weight: 600;
        font-size: 1rem;
    }

    .temp-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .temp-status.normal {
        background: #d1fae5;
        color: #065f46;
    }

    .temp-status.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .temp-status.critical {
        background: #fee2e2;
        color: #991b1b;
    }

    .temp-status.no_data {
        background: #f3f4f6;
        color: #374151;
    }

    .history-empty {
        text-align: center;
        padding: 3rem;
        color: #6b7280;
    }

    .history-empty svg {
        width: 3rem;
        height: 3rem;
        margin: 0 auto 1rem;
        color: #d1d5db;
    }

    /* Responsive Design */
    
    /* Tablets and below */
    @media (max-width: 1024px) {
        .detail-grid {
            gap: 1.5rem;
        }

        .detail-card {
            padding: 1.5rem;
        }

        .temp-value-large {
            font-size: 3rem;
        }

        .temp-unit-large {
            font-size: 1.5rem;
        }

        .products-table-container {
            max-height: 400px;
        }

        .products-table thead th,
        .products-table tbody td {
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
        }
    }

    /* Mobile phones */
    @media (max-width: 768px) {
        .back-button {
            width: 100%;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .detail-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .detail-card {
            padding: 1.25rem;
        }

        .card-title {
            font-size: 1.125rem;
        }

        .temp-display-large {
            padding: 1.5rem;
        }

        .temp-value-large {
            font-size: 2.5rem;
        }

        .temp-unit-large {
            font-size: 1.25rem;
        }

        .temp-label-large {
            font-size: 0.9rem;
        }

        .temp-range-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .range-item {
            padding: 0.875rem;
        }

        .range-value {
            font-size: 1.25rem;
        }

        /* Make table responsive with horizontal scrolling */
        .products-table-container {
            max-height: none;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .products-table {
            min-width: 500px;
        }

        .products-table thead th,
        .products-table tbody td {
            padding: 0.75rem;
            font-size: 0.85rem;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-btn {
            width: 100%;
            justify-content: center;
        }

        .status-badge-large {
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
        }
    }

    /* Small mobile phones */
    @media (max-width: 480px) {
        .back-button {
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
        }

        .detail-card {
            padding: 1rem;
        }

        .card-title {
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .temp-display-large {
            padding: 1.25rem;
        }

        .temp-value-large {
            font-size: 2rem;
        }

        .temp-unit-large {
            font-size: 1rem;
        }

        .temp-label-large {
            font-size: 0.8rem;
        }

        .range-item {
            padding: 0.75rem;
        }

        .range-label {
            font-size: 0.8rem;
        }

        .range-value {
            font-size: 1.125rem;
        }

        /* Stack table for very small screens */
        .products-table {
            min-width: 450px;
        }

        .products-table thead th,
        .products-table tbody td {
            padding: 0.625rem 0.5rem;
            font-size: 0.8rem;
        }

        .category-badge {
            padding: 0.3rem 0.75rem;
            font-size: 0.7rem;
        }

        .detail-btn {
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
        }

        .status-badge-large {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .empty-products {
            padding: 2rem 1rem;
            font-size: 0.9rem;
        }
    }

    /* Modal responsive design */
    @media (max-width: 640px) {
        #recordModal > div {
            max-width: 95% !important;
            width: 95% !important;
            padding: 1.5rem !important;
        }

        #recordModal h2 {
            font-size: 1.125rem !important;
            margin-bottom: 1rem !important;
        }

        #recordModal input,
        #recordModal textarea {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }

        #recordModal .modal-btn {
            width: 100%;
            padding: 0.875rem;
        }

        #recordModal > div > form > div:last-child {
            flex-direction: column;
        }

        /* History Modal Responsive */
        .history-modal-content {
            max-width: 95%;
            width: 95%;
            max-height: 95vh;
        }

        .history-modal-header {
            padding: 1rem;
        }

        .history-modal-header h2 {
            font-size: 1.125rem;
        }

        .history-modal-body {
            padding: 1rem;
        }

        .history-filters {
            flex-direction: column;
            gap: 0.75rem;
        }

        .filter-group {
            min-width: auto;
        }

        .history-table thead th,
        .history-table tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }

        .temp-value {
            font-size: 0.9rem;
        }

        .temp-status {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }
    }

    @media (max-width: 480px) {
        .history-modal-content {
            max-width: 98%;
            width: 98%;
        }

        .history-table-container {
            overflow-x: auto;
        }

        .history-table {
            min-width: 600px;
        }
    }
</style>
@endsection

@section('content')
<a href="{{ route(auth()->user()->role === 'admin' ? 'admin.temperature.index' : 'warehouse.temperature.index') }}" class="back-button">
    ← Back to All Units
</a>

<div class="detail-grid">
    <!-- Temperature Information -->
    <div class="detail-card">
        <h2 class="card-title">Temperature Monitoring</h2>
        
        <div class="temp-display-large {{ $status }}">
            @if($latestTemp)
            <div class="temp-value-large">
                {{ number_format($latestTemp->temperature, 1) }}<span class="temp-unit-large">°C</span>
            </div>
            @else
            <div class="temp-value-large">
                --<span class="temp-unit-large">°C</span>
            </div>
            @endif
            <div class="temp-label-large">Current Temperature</div>
        </div>

        <div class="temp-range-grid">
            <div class="range-item">
                <div class="range-label">Minimum</div>
                <div class="range-value">{{ number_format($unit->temperature_min, 1) }}°C</div>
            </div>
            <div class="range-item">
                <div class="range-label">Maximum</div>
                <div class="range-value">{{ number_format($unit->temperature_max, 1) }}°C</div>
            </div>
            @if($latestTemp && $latestTemp->humidity)
            <div class="range-item">
                <div class="range-label">Humidity</div>
                <div class="range-value">{{ number_format($latestTemp->humidity, 0) }}%</div>
            </div>
            @else
            <div class="range-item">
                <div class="range-label">Humidity</div>
                <div class="range-value">--</div>
            </div>
            @endif
        </div>

        <div style="text-align: center; color: #6b7280; font-size: 0.875rem;">
            Last updated: {{ $latestTemp ? $latestTemp->recorded_at->diffForHumans() : 'Never' }}
        </div>

        <div class="action-buttons">
            <button class="detail-btn detail-btn-primary" onclick="showRecordModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Record Temperature
            </button>
            <button class="detail-btn detail-btn-secondary" onclick="showHistoryModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                View History
            </button>
        </div>
    </div>

    <!-- Products Information -->
    <div class="detail-card">
        <h2 class="card-title">Stored Products ({{ $totalProducts }})</h2>
        
        @if($products->isNotEmpty())
        <div class="products-table-container">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $category)
                        @foreach($category['items'] as $item)
                        <tr>
                            <td class="product-name">{{ $item['name'] }}</td>
                            <td>
                                <span class="category-badge {{ strtolower($category['category']) }}">
                                    {{ $category['category'] }}
                                </span>
                            </td>
                            <td class="product-qty">{{ $item['quantity'] }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-products">
            <p>No products currently stored in this unit</p>
        </div>
        @endif
    </div>
</div>

<!-- Record Temperature Modal -->
<div class="modal" id="recordModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: white; border-radius: 1rem; max-width: 500px; width: 90%; padding: 2rem;">
        <h2 style="margin-bottom: 1.5rem;">Record Temperature - {{ $unit->name }}</h2>
        <form method="POST" action="{{ route(auth()->user()->role === 'admin' ? 'admin.temperature.record' : 'warehouse.temperature.record') }}">
            @csrf
            <input type="hidden" name="storage_unit_id" value="{{ $unit->id }}">
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Temperature (°C)</label>
                <input type="number" step="0.1" name="temperature" required 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Humidity (%) - Optional</label>
                <input type="number" step="0.1" name="humidity" 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Notes - Optional</label>
                <textarea name="notes" rows="3" 
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;"></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeRecordModal()" class="modal-btn modal-btn-cancel">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-submit">Record</button>
            </div>
        </form>
    </div>
</div>

<!-- Temperature History Modal -->
<div class="history-modal" id="historyModal">
    <div class="history-modal-content">
        <div class="history-modal-header">
            <h2>Temperature History - {{ $unit->name }}</h2>
            <button type="button" class="history-close-btn" onclick="closeHistoryModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        
        <div class="history-modal-body">
            <div class="history-filters">
                <div class="filter-group">
                    <label class="filter-label">From Date</label>
                    <input type="date" class="filter-input" id="fromDate" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">To Date</label>
                    <input type="date" class="filter-input" id="toDate" value="{{ now()->format('Y-m-d') }}">
                </div>
                <button type="button" class="filter-btn" onclick="filterHistory()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    Filter
                </button>
            </div>

            <div class="history-table-container" id="historyTableContainer">
                <div class="history-empty" id="historyLoading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v4"></path>
                        <path d="m16.84 6.81 2.81-2.81"></path>
                        <path d="M22 12h-4"></path>
                        <path d="m16.84 17.19 2.81 2.81"></path>
                        <path d="M12 22v-4"></path>
                        <path d="m7.16 17.19-2.81 2.81"></path>
                        <path d="M2 12h4"></path>
                        <path d="m7.16 6.81-2.81-2.81"></path>
                    </svg>
                    <p>Loading temperature history...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showRecordModal() {
        document.getElementById('recordModal').style.display = 'flex';
    }

    function closeRecordModal() {
        document.getElementById('recordModal').style.display = 'none';
    }

    function showHistoryModal() {
        document.getElementById('historyModal').classList.add('active');
        loadHistoryData();
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').classList.remove('active');
    }

    function filterHistory() {
        loadHistoryData();
    }

    function loadHistoryData() {
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const container = document.getElementById('historyTableContainer');
        
        // Show loading state
        container.innerHTML = `
            <div class="history-empty" id="historyLoading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
                    <path d="M12 2v4"></path>
                    <path d="m16.84 6.81 2.81-2.81"></path>
                    <path d="M22 12h-4"></path>
                    <path d="m16.84 17.19 2.81 2.81"></path>
                    <path d="M12 22v-4"></path>
                    <path d="m7.16 17.19-2.81 2.81"></path>
                    <path d="M2 12h4"></path>
                    <path d="m7.16 6.81-2.81-2.81"></path>
                </svg>
                <p>Loading temperature history...</p>
            </div>
        `;

        // Build the correct URL based on user role
        const unitId = {{ $unit->id }};
        const userRole = '{{ auth()->user()->role }}';
        let baseUrl = userRole === 'admin' ? '/admin/temperature/history/' : '/warehouse/temperature/history/';
        let url = baseUrl + unitId;
        
        const params = new URLSearchParams();
        if (fromDate) params.append('start_date', fromDate);
        if (toDate) params.append('end_date', toDate);
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        console.log('Fetching from URL:', url); // Debug log
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status); // Debug log
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data); // Debug log
                // Transform the database data for the table
                const transformedData = data.map(record => {
                    const recordedDate = new Date(record.recorded_at);
                    return {
                        date: recordedDate.toISOString().split('T')[0],
                        time: recordedDate.toTimeString().split(' ')[0],
                        temperature: record.temperature,
                        humidity: record.humidity || 'N/A',
                        status: record.status,
                        recorded_by: record.recorded_by ? record.recorded_by.name : 'Unknown'
                    };
                });
                renderHistoryTable(transformedData);
            })
            .catch(error => {
                console.error('Error fetching temperature history:', error);
                container.innerHTML = `
                    <div class="history-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p>Error loading temperature history. Please try again.</p>
                    </div>
                `;
            });
    }



    function renderHistoryTable(data) {
        const container = document.getElementById('historyTableContainer');
        
        if (data.length === 0) {
            container.innerHTML = `
                <div class="history-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    <p>No temperature records found for the selected date range.</p>
                </div>
            `;
            return;
        }

        const tableHTML = `
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Temperature</th>
                        <th>Humidity</th>
                        <th>Status</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.map(record => `
                        <tr>
                            <td>${record.date}</td>
                            <td>${record.time}</td>
                            <td><span class="temp-value">${record.temperature}°C</span></td>
                            <td>${record.humidity === 'N/A' ? '<span style="color: #888;">N/A</span>' : record.humidity + '%'}</td>
                            <td><span class="temp-status ${record.status}">${record.status.charAt(0).toUpperCase() + record.status.slice(1)}</span></td>
                            <td>${record.recorded_by}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        container.innerHTML = tableHTML;
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        const historyModal = document.getElementById('historyModal');
        if (e.target === historyModal) {
            closeHistoryModal();
        }
    });

    // Add CSS animation for loading spinner
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection

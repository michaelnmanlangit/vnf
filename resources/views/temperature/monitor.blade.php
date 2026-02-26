@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.warehouse')

@section('title', 'Temperature Monitoring')

@section('styles')
<style>
    :root {
        --color-normal: #10b981;
        --color-warning: #f59e0b;
        --color-critical: #ef4444;
        --color-no-data: #6b7280;
    }

    /* Toolbar Styles */
    .temp-toolbar {
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
    }

    .toolbar-form {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex: 0 1 auto;
    }

    .toolbar-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex: 0 1 auto;
    }

    .temp-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
    }

    .temp-record-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .temp-record-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .temp-simulate-btn {
        background: white;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .temp-simulate-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .storage-units-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 3rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .storage-unit-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        text-decoration: none;
        transition: transform 0.3s ease, filter 0.3s ease;
        position: relative;
    }

    .storage-unit-item:hover {
        transform: translateY(-10px) scale(1.05);
        filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.2));
    }

    .unit-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        padding: 2rem 1.5rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        border: 1px solid #f1f5f9;
        transition: box-shadow 0.3s ease;
    }

    .storage-unit-item:hover .unit-card {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.13);
    }

    .storage-unit-item.status-critical .unit-card {
        border-color: rgba(239, 68, 68, 0.25);
    }

    .storage-unit-item.status-warning .unit-card {
        border-color: rgba(245, 158, 11, 0.25);
    }

    .storage-unit-item.status-normal .unit-card {
        border-color: rgba(16, 185, 129, 0.2);
    }

    .unit-svg-wrapper {
        position: relative;
        width: 200px;
        height: 200px;
    }

    .unit-svg-wrapper svg {
        width: 100%;
        height: 100%;
        transition: all 0.3s ease;
    }

    /* Status-based coloring for SVG */
    .storage-unit-item.status-normal .unit-svg-wrapper svg .colorable {
        fill: var(--color-normal);
    }

    .storage-unit-item.status-warning .unit-svg-wrapper svg .colorable {
        fill: var(--color-warning);
    }

    .storage-unit-item.status-critical .unit-svg-wrapper svg .colorable {
        fill: var(--color-critical);
    }

    .storage-unit-item.status-no_data .unit-svg-wrapper svg .colorable {
        fill: var(--color-no-data);
    }

    /* Pulse animation for critical status */
    .storage-unit-item.status-critical .unit-svg-wrapper svg {
        animation: pulse-glow 2s infinite;
    }

    @keyframes pulse-glow {
        0%, 100% {
            filter: drop-shadow(0 0 10px rgba(239, 68, 68, 0.6));
        }
        50% {
            filter: drop-shadow(0 0 20px rgba(239, 68, 68, 1));
        }
    }

    .unit-info {
        margin-top: 1.5rem;
        text-align: center;
        width: 100%;
        max-width: 250px;
    }

    .unit-name {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .unit-code {
        font-size: 0.875rem;
        color: #6b7280;
        font-family: 'Courier New', monospace;
        margin-bottom: 1rem;
    }

    .unit-temp {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .unit-temp-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
    }

    .unit-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .unit-status-badge.normal {
        background: #d1fae5;
        color: #065f46;
    }

    .unit-status-badge.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .unit-status-badge.critical {
        background: #fee2e2;
        color: #991b1b;
    }

    .unit-status-badge.no_data {
        background: #e5e7eb;
        color: #374151;
    }

    .unit-products-count {
        margin-top: 0.75rem;
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Modal Styles - scoped to custom modals only */
    .custom-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .custom-modal.active {
        display: flex;
    }

    .custom-modal .modal-content {
        background: white;
        border-radius: 1rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .custom-modal .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .custom-modal .modal-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
    }

    .custom-modal .modal-body {
        padding: 1.5rem;
    }

    .custom-modal .form-group {
        margin-bottom: 1.5rem;
    }

    .custom-modal .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .custom-modal .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: border-color 0.2s;
    }

    .custom-modal .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .custom-modal .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .custom-modal .btn-modal {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }

    .custom-modal .btn-modal-cancel {
        background: white;
        color: #111827;
        border: 1px solid #e5e7eb;
    }

    .custom-modal .btn-modal-cancel:hover {
        background: #f3f4f6;

    .custom-modal .btn-modal-submit {
        background: #3b82f6;
        color: white;
    }

    .custom-modal .btn-modal-submit:hover {
        background: #2563eb;
    }

    /* Responsive Design */
    
    /* Tablets and below */
    @media (max-width: 1024px) {
        .storage-units-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2.5rem;
        }

        .temp-toolbar {
            width: 100%;
            margin-left: 0;
            justify-content: space-between;
        }
    }

    /* Mobile phones */
    @media (max-width: 768px) {
        .storage-units-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .temp-toolbar {
            flex-direction: column;
            align-items: stretch;
            padding: 1rem;
        }

        .toolbar-form,
        .toolbar-actions {
            width: 100%;
        }

        .toolbar-form {
            flex-direction: column;
            gap: 0.5rem;
        }

        .toolbar-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .temp-action-btn {
            width: 100%;
            justify-content: center;
            padding: 1rem;
        }

        .unit-svg-wrapper {
            width: 150px;
            height: 150px;
        }

        .unit-info {
            max-width: 100%;
        }

        .unit-name {
            font-size: 1rem;
        }

        .unit-temp {
            font-size: 1.75rem;
        }

        .custom-modal .modal-content {
            width: 95%;
            max-height: 95vh;
        }

        .custom-modal .modal-header,
        .custom-modal .modal-body,
        .custom-modal .modal-footer {
            padding: 1rem;
        }

        .custom-modal .modal-header h2 {
            font-size: 1.25rem;
        }
    }

    /* Small mobile phones */
    @media (max-width: 480px) {
        .storage-units-grid {
            gap: 1.5rem;
        }

        .unit-svg-wrapper {
            width: 120px;
            height: 120px;
        }

        .unit-name {
            font-size: 0.95rem;
        }

        .unit-code {
            font-size: 0.75rem;
        }

        .unit-temp {
            font-size: 1.5rem;
        }

        .unit-temp-label {
            font-size: 0.7rem;
        }

        .unit-status-badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.75rem;
        }

        .unit-products-count {
            font-size: 0.8rem;
        }

        .temp-action-btn {
            font-size: 0.9rem;
            padding: 0.875rem;
        }
    }
</style>
@endsection

@section('content')
<div class="storage-units-grid">
    @foreach($storageUnits as $unit)
    <a href="{{ route(auth()->user()->role === 'admin' ? 'admin.temperature.show' : 'warehouse.temperature.show', $unit['id']) }}" 
       class="storage-unit-item status-{{ $unit['status'] }}">
        <div class="unit-card">
        <div class="unit-svg-wrapper">
            <svg height="200px" width="200px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve">
                <path class="colorable" style="fill:#9BB4C0;" d="M499.725,163.607L260.792,1.473c-2.893-1.963-6.69-1.964-9.583,0L12.275,163.607 c-2.34,1.587-3.742,4.231-3.742,7.06v332.8c0,4.713,3.821,8.533,8.533,8.533h477.867c4.713,0,8.533-3.821,8.533-8.533v-332.8 C503.467,167.838,502.066,165.194,499.725,163.607z"></path>
                <path style="fill:#7A8C96;" d="M34.134,503.467v-332.8c0-2.829,1.402-5.473,3.742-7.061L268.8,6.907l-8.009-5.435 c-2.893-1.963-6.69-1.964-9.583,0L12.275,163.606c-2.34,1.588-3.742,4.233-3.742,7.061v332.8c0,4.713,3.821,8.533,8.533,8.533h25.6 C37.954,512,34.134,508.181,34.134,503.467z"></path>
                <path style="fill:#74757B;" d="M409.6,204.8H102.4c-4.713,0-8.533,3.821-8.533,8.533v290.133c0,4.713,3.821,8.533,8.533,8.533h307.2 c4.713,0,8.533-3.821,8.533-8.533V213.334C418.134,208.621,414.313,204.8,409.6,204.8z"></path>
                <path style="fill:#606268;" d="M102.4,204.8c-4.713,0-8.533,3.821-8.533,8.533v290.133c0,4.713,3.821,8.533,8.533,8.533h17.067 V204.8H102.4z"></path>
                <path style="fill:#C3C4C6;" d="M426.667,170.667H85.334c-4.713,0-8.533,3.821-8.533,8.533v34.133c0,4.713,3.821,8.533,8.533,8.533 h341.333c4.713,0,8.533-3.821,8.533-8.533V179.2C435.2,174.488,431.379,170.667,426.667,170.667z"></path>
                <path style="fill:#AFB0B4;" d="M426.667,204.8H85.334c-4.713,0-8.533-3.821-8.533-8.533v17.067c0,4.713,3.821,8.533,8.533,8.533 h341.333c4.713,0,8.533-3.821,8.533-8.533v-17.067C435.2,200.981,431.379,204.8,426.667,204.8z"></path>
                <g>
                    <path class="colorable" style="fill:#9BB4C0;" d="M290.134,76.8h-68.267c-4.713,0-8.533-3.821-8.533-8.533c0-4.713,3.821-8.533,8.533-8.533h68.267 c4.713,0,8.533,3.821,8.533,8.533C298.667,72.98,294.846,76.8,290.134,76.8z"></path>
                    <path class="colorable" style="fill:#9BB4C0;" d="M290.134,110.934h-68.267c-4.713,0-8.533-3.821-8.533-8.533s3.821-8.533,8.533-8.533h68.267 c4.713,0,8.533,3.821,8.533,8.533S294.846,110.934,290.134,110.934z"></path>
                    <path class="colorable" style="fill:#9BB4C0;" d="M290.134,145.067h-68.267c-4.713,0-8.533-3.821-8.533-8.533c0-4.713,3.821-8.533,8.533-8.533h68.267 c4.713,0,8.533,3.821,8.533,8.533C298.667,141.246,294.846,145.067,290.134,145.067z"></path>
                </g>
            </svg>
        </div>
        
        <div class="unit-info">
            <div class="unit-name">{{ $unit['name'] }}</div>
            <div class="unit-code">{{ $unit['code'] }}</div>
            
            <div class="unit-temp">
                @if($unit['current_temperature'] !== null)
                    {{ number_format($unit['current_temperature'], 1) }}°C
                @else
                    --°C
                @endif
            </div>
            <div class="unit-temp-label">Current Temperature</div>
            
            <span class="unit-status-badge {{ $unit['status'] }}">
                @if($unit['status'] === 'normal')
                    Normal
                @elseif($unit['status'] === 'warning')
                    Warning
                @elseif($unit['status'] === 'critical')
                    Critical
                @else
                    No Data
                @endif
            </span>
            
            <div class="unit-products-count">
                {{ $unit['total_products'] }} products stored
            </div>
        </div>
        </div>
    </a>
    @endforeach
</div>
@endsection

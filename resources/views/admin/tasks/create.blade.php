@extends('layouts.admin')

@section('title', 'Assign Relocation Work')

@section('page-title', 'Assign Relocation Work')

@section('styles')
@vite(['resources/css/billing.css'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* ---- Searchable Worker Picker ---- */
    .worker-picker { position: relative; }
    .worker-search-wrap {
        display: flex;
        align-items: center;
        gap: .5rem;
        border: 1px solid #d1d5db;
        border-radius: .5rem;
        padding: .5rem .75rem;
        background: #fff;
        cursor: text;
        transition: border-color .15s;
    }
    .worker-search-wrap:focus-within { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
    .worker-search-wrap i { color: #9ca3af; flex-shrink: 0; }
    .worker-search-input {
        border: none;
        outline: none;
        flex: 1;
        font-size: .95rem;
        background: transparent;
        color: #1e293b;
        min-width: 0;
    }
    .worker-clear-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #9ca3af;
        padding: 0;
        line-height: 1;
        display: none;
    }
    .worker-clear-btn.visible { display: block; }

    .worker-selected-chip {
        display: none;
        align-items: center;
        gap: .5rem;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        border-radius: .375rem;
        padding: .35rem .75rem;
        margin-top: .4rem;
        font-size: .9rem;
        color: #3730a3;
    }
    .worker-selected-chip.visible { display: flex; }
    .worker-selected-chip .chip-name { font-weight: 600; }
    .worker-selected-chip .chip-meta { color: #6366f1; font-size: .8rem; }

    .dept-filters {
        display: flex;
        gap: .4rem;
        flex-wrap: wrap;
        margin: .5rem 0;
    }
    .dept-btn {
        padding: .25rem .75rem;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        color: #374151;
        font-size: .8rem;
        cursor: pointer;
        transition: all .15s;
    }
    .dept-btn:hover { background: #e0e7ff; border-color: #818cf8; color: #3730a3; }
    .dept-btn.active { background: #6366f1; border-color: #6366f1; color: #fff; }

    .worker-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0; right: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .5rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.1);
        max-height: 280px;
        overflow-y: auto;
        z-index: 999;
        display: none;
    }
    .worker-dropdown.open { display: block; }
    .worker-option {
        padding: .65rem 1rem;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: .1rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background .1s;
    }
    .worker-option:last-child { border-bottom: none; }
    .worker-option:hover, .worker-option.focused { background: #eef2ff; }
    .worker-option .opt-name { font-weight: 600; font-size: .9rem; color: #1e293b; }
    .worker-option .opt-meta { font-size: .78rem; color: #6b7280; }
    .worker-option .opt-dept {
        display: inline-block;
        font-size: .72rem;
        padding: .1rem .45rem;
        border-radius: 999px;
        font-weight: 600;
        margin-left: .3rem;
    }
    .dept-badge-production  { background: #dcfce7; color: #166534; }
    .dept-badge-administration { background: #fef9c3; color: #713f12; }
    .worker-no-results {
        padding: 1rem;
        text-align: center;
        color: #9ca3af;
        font-size: .9rem;
        display: none;
    }

    /* ---- Location & Arrow ---- */
    .location-card {
        background: #f0f4ff;
        border: 1px solid #c7d2fe;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        margin-top: 0.5rem;
        display: none;
    }
    .location-card.visible { display: block; }
    .location-card .loc-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .25rem;
    }
    .location-card .loc-value { font-size: 1rem; font-weight: 600; color: #1e293b; }
    .location-card .no-location { font-size: 0.9rem; color: #ef4444; font-style: italic; }
    .arrow-icon { text-align: center; font-size: 1.5rem; color: #6366f1; margin: 1rem 0; display: none; }
</style>
@endsection

@section('content')
<div class="billing-container">
    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tasks.store') }}" method="POST" id="relocationForm">
        @csrf

        <div class="form-grid">
            <div class="form-section">
                <h3>Relocation Details</h3>

                {{-- Worker Search Picker --}}
                <div class="form-group">
                    <label>Worker <span class="required">*</span></label>

                    {{-- Hidden real input for form submission --}}
                    <input type="hidden" name="employee_id" id="employee_id" value="{{ old('employee_id') }}">

                    {{-- Department filter chips --}}
                    <div class="dept-filters">
                        <button type="button" class="dept-btn active" data-dept="">All</button>
                        <button type="button" class="dept-btn" data-dept="production">Production</button>
                        <button type="button" class="dept-btn" data-dept="administration">Administration</button>
                    </div>

                    {{-- Search box --}}
                    <div class="worker-picker" id="workerPicker">
                        <div class="worker-search-wrap" id="workerSearchWrap">
                            <i class="fas fa-search"></i>
                            <input type="text" class="worker-search-input" id="workerSearchInput"
                                   placeholder="Search by name or position…" autocomplete="off">
                            <button type="button" class="worker-clear-btn" id="workerClearBtn" title="Clear selection">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Selected chip --}}
                        <div class="worker-selected-chip" id="workerSelectedChip">
                            <i class="fas fa-user-check"></i>
                            <span class="chip-name" id="chipName"></span>
                            <span class="chip-meta" id="chipMeta"></span>
                        </div>

                        {{-- Dropdown list --}}
                        <div class="worker-dropdown" id="workerDropdown">
                            <div class="worker-no-results" id="workerNoResults">No workers found.</div>
                            @foreach($employees as $emp)
                                <div class="worker-option"
                                     data-id="{{ $emp->id }}"
                                     data-name="{{ $emp->full_name }}"
                                     data-dept="{{ $emp->department }}"
                                     data-position="{{ $emp->position }}">
                                    <span class="opt-name">
                                        {{ $emp->full_name }}
                                        <span class="opt-dept dept-badge-{{ $emp->department }}">{{ ucfirst($emp->department) }}</span>
                                    </span>
                                    <span class="opt-meta">{{ $emp->position }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @error('employee_id')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                    <small class="helper-text">Active production and administration workers only.</small>
                </div>

                {{-- Current Location Card (AJAX) --}}
                <div id="currentLocationCard" class="location-card">
                    <div class="loc-label"><i class="fas fa-map-marker-alt"></i> Current Location</div>
                    <div id="currentLocationValue" class="loc-value"></div>
                </div>

                {{-- Arrow --}}
                <div class="arrow-icon" id="arrowIcon">
                    <i class="fas fa-arrow-down"></i>
                </div>

                {{-- Relocate To --}}
                <div class="form-group" id="relocateToGroup" style="display:none;">
                    <label for="relocate_to_storage_unit_id">Relocate To <span class="required">*</span></label>
                    <select name="relocate_to_storage_unit_id" id="relocate_to_storage_unit_id" class="form-control">
                        <option value="">-- Select Destination --</option>
                        @foreach($storageUnits as $unit)
                            <option value="{{ $unit->id }}"
                                {{ old('relocate_to_storage_unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('relocate_to_storage_unit_id')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Assign Relocation</button>
            <a href="{{ route('admin.tasks.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    // ── DOM refs ──────────────────────────────────────────────
    const hiddenInput      = document.getElementById('employee_id');
    const searchInput      = document.getElementById('workerSearchInput');
    const clearBtn         = document.getElementById('workerClearBtn');
    const dropdown         = document.getElementById('workerDropdown');
    const noResults        = document.getElementById('workerNoResults');
    const selectedChip     = document.getElementById('workerSelectedChip');
    const chipName         = document.getElementById('chipName');
    const chipMeta         = document.getElementById('chipMeta');
    const deptBtns         = document.querySelectorAll('.dept-btn');
    const allOptions       = Array.from(document.querySelectorAll('.worker-option'));
    const locationCard     = document.getElementById('currentLocationCard');
    const locationValue    = document.getElementById('currentLocationValue');
    const arrowIcon        = document.getElementById('arrowIcon');
    const relocateToGroup  = document.getElementById('relocateToGroup');
    const relocateToSelect = document.getElementById('relocate_to_storage_unit_id');

    let activeDept = '';
    let focusedIndex = -1;

    // ── Filter helpers ────────────────────────────────────────
    function getVisible() {
        return allOptions.filter(o => o.style.display !== 'none');
    }

    function applyFilter() {
        const q = searchInput.value.trim().toLowerCase();
        let shown = 0;
        allOptions.forEach(function(opt) {
            const name     = opt.dataset.name.toLowerCase();
            const dept     = opt.dataset.dept.toLowerCase();
            const position = opt.dataset.position.toLowerCase();
            const matchDept   = activeDept === '' || dept === activeDept;
            const matchSearch = q === '' || name.includes(q) || position.includes(q);
            const vis = matchDept && matchSearch;
            opt.style.display = vis ? 'flex' : 'none';
            if (vis) shown++;
        });
        noResults.style.display = shown === 0 ? 'block' : 'none';
        focusedIndex = -1;
    }

    function openDropdown() {
        dropdown.classList.add('open');
        applyFilter();
    }

    function closeDropdown() {
        dropdown.classList.remove('open');
    }

    // ── Selection ────────────────────────────────────────────
    function selectEmployee(opt) {
        const id       = opt.dataset.id;
        const name     = opt.dataset.name;
        const dept     = opt.dataset.dept;
        const position = opt.dataset.position;

        hiddenInput.value    = id;
        searchInput.value    = '';
        chipName.textContent = name;
        chipMeta.textContent = ucfirst(dept) + ' – ' + position;
        selectedChip.classList.add('visible');
        clearBtn.classList.add('visible');
        closeDropdown();
        loadEmployeeInfo(id);
    }

    function clearSelection() {
        hiddenInput.value = '';
        searchInput.value = '';
        selectedChip.classList.remove('visible');
        clearBtn.classList.remove('visible');
        locationCard.classList.remove('visible');
        arrowIcon.style.display = 'none';
        relocateToGroup.style.display = 'none';
        relocateToSelect.required = false;
        applyFilter();
    }

    function ucfirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

    // ── Location AJAX ─────────────────────────────────────────
    function loadEmployeeInfo(id) {
        if (!id) return;
        fetch('/admin/tasks/employee/' + id + '/info', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(function(data) {
            locationValue.innerHTML = data.current_unit
                ? '<strong>' + data.current_unit.name + '</strong> (' + data.current_unit.code + ')'
                : '<span class="no-location">No storage unit assigned yet</span>';
            locationCard.classList.add('visible');
            arrowIcon.style.display = 'block';
            relocateToGroup.style.display = 'block';
            relocateToSelect.required = true;
            Array.from(relocateToSelect.options).forEach(function(o) {
                o.disabled = data.current_unit ? (o.value == data.current_unit.id) : false;
                if (o.disabled && o.selected) o.selected = false;
            });
        });
    }

    // ── Events ───────────────────────────────────────────────
    searchInput.addEventListener('focus', openDropdown);
    searchInput.addEventListener('input', function() {
        clearBtn.classList.toggle('visible', searchInput.value.length > 0 || hiddenInput.value !== '');
        applyFilter();
        if (!dropdown.classList.contains('open')) openDropdown();
    });

    clearBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        clearSelection();
        searchInput.focus();
    });

    allOptions.forEach(function(opt) {
        opt.addEventListener('mousedown', function(e) {
            e.preventDefault(); // prevent blur before click
        });
        opt.addEventListener('click', function() {
            selectEmployee(opt);
        });
    });

    // keyboard nav
    searchInput.addEventListener('keydown', function(e) {
        const vis = getVisible();
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusedIndex = Math.min(focusedIndex + 1, vis.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusedIndex = Math.max(focusedIndex - 1, 0);
        } else if (e.key === 'Enter' && focusedIndex >= 0) {
            e.preventDefault();
            selectEmployee(vis[focusedIndex]);
            return;
        } else if (e.key === 'Escape') {
            closeDropdown();
            return;
        }
        vis.forEach(function(o, i) {
            o.classList.toggle('focused', i === focusedIndex);
            if (i === focusedIndex) o.scrollIntoView({ block: 'nearest' });
        });
    });

    document.addEventListener('click', function(e) {
        if (!document.getElementById('workerPicker').contains(e.target)) closeDropdown();
    });

    // department filter buttons
    deptBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            deptBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeDept = btn.dataset.dept;
            applyFilter();
            if (!dropdown.classList.contains('open')) openDropdown();
        });
    });

    // ── Restore old() value after validation error ────────────
    var oldId = hiddenInput.value;
    if (oldId) {
        var match = allOptions.find(o => o.dataset.id == oldId);
        if (match) selectEmployee(match);
    }
})();
</script>
@endsection

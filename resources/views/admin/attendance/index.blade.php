@extends('layouts.admin')

@section('title', 'Attendance Management')
@section('page-title', 'Attendance')

@section('styles')
<style>
    .attendance-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.07);
        text-align: center;
    }
    .stat-card .stat-num {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 4px;
    }
    .stat-card .stat-label {
        font-size: .78rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .stat-card.total   .stat-num { color: #3b82f6; }
    .stat-card.present .stat-num { color: #22c55e; }
    .stat-card.absent  .stat-num { color: #ef4444; }
    .stat-card.late    .stat-num { color: #f59e0b; }
    .stat-card.unmarked .stat-num { color: #94a3b8; }

    .filter-bar {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }
    .filter-bar input[type="date"] {
        padding: .5rem .75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: .9rem;
        font-family: inherit;
        color: #1a202c;
        outline: none;
        transition: border-color .2s;
    }
    .filter-bar input[type="date"]:focus { border-color: #4169E1; }
    .filter-bar .btn-filter {
        padding: .5rem 1.2rem;
        background: #4169E1;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .88rem;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: opacity .2s;
    }
    .filter-bar .btn-filter:hover { opacity: .88; }
    .filter-bar .portal-link {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem 1rem;
        border: 1.5px solid #4169E1;
        border-radius: 8px;
        color: #4169E1;
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        transition: background .2s, color .2s;
    }
    .filter-bar .portal-link:hover { background: #4169E1; color: #fff; }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.07);
    }
    .attendance-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .attendance-table th {
        padding: .75rem 1rem;
        text-align: left;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #475569;
    }
    .attendance-table td {
        padding: .75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        color: #374151;
        vertical-align: middle;
    }
    .attendance-table tbody tr:last-child td { border-bottom: none; }
    .attendance-table tbody tr:hover { background: #f8fafc; }

    .emp-info { display: flex; align-items: center; gap: .75rem; }
    .emp-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        object-fit: cover; flex-shrink: 0; border: 2px solid #e2e8f0;
    }
    .emp-avatar-placeholder {
        width: 34px; height: 34px; border-radius: 50%;
        background: #dbeafe; display: flex; align-items: center;
        justify-content: center; font-size: .8rem; font-weight: 700;
        color: #4169E1; flex-shrink: 0;
    }
    .emp-name { font-weight: 600; color: #111827; }
    .emp-pos  { font-size: .75rem; color: #64748b; }

    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 600;
    }
    .badge-present   { background: #dcfce7; color: #166534; }
    .badge-absent    { background: #fee2e2; color: #991b1b; }
    .badge-late      { background: #fef3c7; color: #92400e; }
    .badge-half_day  { background: #e0e7ff; color: #3730a3; }
    .badge-on_leave  { background: #f3e8ff; color: #6b21a8; }
    .badge-unmarked  { background: #f1f5f9; color: #64748b; }

    .btn-mark {
        padding: 4px 12px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: .78rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        font-family: inherit;
        transition: background .15s;
    }
    .btn-mark:hover { background: #e2e8f0; }

    /* Modal */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 1000;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden;
        transition: opacity .2s, visibility .2s;
    }
    .modal-overlay.open { opacity: 1; visibility: visible; }
    .modal {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        transform: translateY(12px);
        transition: transform .2s;
    }
    .modal-overlay.open .modal { transform: translateY(0); }
    .modal h3 { font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 1.25rem; }
    .modal-grid { display: grid; gap: .9rem; }
    .modal-grid label { font-size: .8rem; font-weight: 600; color: #374151; display: block; margin-bottom: 4px; }
    .modal-grid select, .modal-grid input {
        width: 100%;
        padding: .5rem .75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: .9rem;
        font-family: inherit;
        color: #1a202c;
        outline: none;
        transition: border-color .2s;
    }
    .modal-grid select:focus, .modal-grid input:focus { border-color: #4169E1; }
    .modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
    .modal-actions { display: flex; gap: .75rem; margin-top: 1.25rem; }
    .btn-save {
        flex: 1; padding: .6rem; background: #4169E1; color: #fff;
        border: none; border-radius: 8px; font-size: .9rem; font-weight: 600;
        font-family: inherit; cursor: pointer; transition: opacity .2s;
    }
    .btn-save:hover { opacity: .88; }
    .btn-cancel {
        padding: .6rem 1.2rem; background: #f1f5f9; color: #475569;
        border: 1px solid #cbd5e1; border-radius: 8px; font-size: .9rem;
        font-weight: 600; font-family: inherit; cursor: pointer;
    }
    .btn-cancel:hover { background: #e2e8f0; }
</style>
@endsection

@section('content')
<div class="content-wrapper">

    {{-- Flash message --}}
    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.25rem;font-size:.88rem;font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Date filter --}}
    <form method="GET" action="{{ route('admin.attendance.index') }}" class="filter-bar">
        <label style="font-size:.85rem;font-weight:600;color:#374151;margin:0;">Date</label>
        <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}">
        <button type="submit" class="btn-filter">View</button>
        <a href="{{ route('attendance') }}" target="_blank" class="portal-link">
            <i class="fas fa-external-link-alt"></i> Employee Clock Portal
        </a>
    </form>

    {{-- Stats --}}
    <div class="attendance-stats">
        <div class="stat-card total">
            <div class="stat-num">{{ $totalEmployees }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card present">
            <div class="stat-num">{{ $presentCount }}</div>
            <div class="stat-label">Present</div>
        </div>
        <div class="stat-card absent">
            <div class="stat-num">{{ $absentCount }}</div>
            <div class="stat-label">Absent</div>
        </div>
        <div class="stat-card late">
            <div class="stat-num">{{ $lateCount }}</div>
            <div class="stat-label">Late</div>
        </div>
        <div class="stat-card unmarked">
            <div class="stat-num">{{ $notMarkedCount }}</div>
            <div class="stat-label">Unmarked</div>
        </div>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Status</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours Worked</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    @php $att = $employee->attendance->first(); @endphp
                    <tr>
                        <td>
                            <div class="emp-info">
                                @if($employee->image)
                                    <img src="{{ $employee->image }}" alt="" class="emp-avatar">
                                @else
                                    <div class="emp-avatar-placeholder">
                                        {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="emp-name">{{ $employee->full_name }}</div>
                                    <div class="emp-pos">{{ $employee->position }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($att)
                                <span class="status-badge badge-{{ $att->status }}">{{ ucfirst(str_replace('_', ' ', $att->status)) }}</span>
                            @else
                                <span class="status-badge badge-unmarked">Not Marked</span>
                            @endif
                        </td>
                        <td>{{ $att && $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('h:i A') : '—' }}</td>
                        <td>{{ $att && $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('h:i A') : '—' }}</td>
                        <td>{{ $att && $att->hours_worked ? number_format($att->hours_worked, 2) . ' hrs' : '—' }}</td>
                        <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $att->notes ?? '' }}">
                            {{ $att->notes ?? '—' }}
                        </td>
                        <td>
                            <button type="button" class="btn-mark"
                                data-id="{{ $employee->id }}"
                                data-name="{{ $employee->full_name }}"
                                data-status="{{ $att->status ?? '' }}"
                                data-time-in="{{ $att && $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '' }}"
                                data-time-out="{{ $att && $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '' }}"
                                data-notes="{{ $att->notes ?? '' }}"
                                onclick="openModal(this)">
                                {{ $att ? 'Edit' : 'Mark' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
                @if($employees->isEmpty())
                    <tr><td colspan="7" style="text-align:center;padding:2rem;color:#94a3b8;">No employees found.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Mark/Edit Attendance Modal --}}
<div class="modal-overlay" id="attendanceModal">
    <div class="modal">
        <h3 id="modalTitle">Mark Attendance</h3>
        <form method="POST" action="{{ route('admin.attendance.mark') }}">
            @csrf
            <input type="hidden" name="employee_id" id="modalEmployeeId">
            <input type="hidden" name="date" value="{{ $date }}">
            <div class="modal-grid">
                <div>
                    <label>Status *</label>
                    <select name="status" id="modalStatus" required>
                        <option value="">Select status</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="half_day">Half Day</option>
                        <option value="on_leave">On Leave</option>
                    </select>
                </div>
                <div class="modal-row">
                    <div>
                        <label>Time In</label>
                        <input type="time" name="time_in" id="modalTimeIn">
                    </div>
                    <div>
                        <label>Time Out</label>
                        <input type="time" name="time_out" id="modalTimeOut">
                    </div>
                </div>
                <div>
                    <label>Notes</label>
                    <input type="text" name="notes" id="modalNotes" maxlength="500" placeholder="Optional">
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(btn) {
    document.getElementById('modalTitle').textContent = 'Mark Attendance — ' + btn.dataset.name;
    document.getElementById('modalEmployeeId').value = btn.dataset.id;
    document.getElementById('modalStatus').value     = btn.dataset.status || '';
    document.getElementById('modalTimeIn').value     = btn.dataset.timeIn  || '';
    document.getElementById('modalTimeOut').value    = btn.dataset.timeOut || '';
    document.getElementById('modalNotes').value      = btn.dataset.notes   || '';
    document.getElementById('attendanceModal').classList.add('open');
}
function closeModal() {
    document.getElementById('attendanceModal').classList.remove('open');
}
document.getElementById('attendanceModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection

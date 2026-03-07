@extends('layouts.admin')



@section('title', 'Payroll Detail &mdash; ' . $employee->full_name)

@section('page-title', 'Payroll Detail')



@section('styles')

<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

.status-stats .status-cards{grid-template-columns:repeat(4,1fr);}

@@media(max-width:900px){.status-stats .status-cards{grid-template-columns:repeat(2,1fr);}}

</style>

@endsection



@section('content')

<div class="billing-grid-container">



    {{-- Back button --}}

    <div class="receipt-page-header" style="margin-bottom:1.25rem;">

        <a href="{{ route('admin.payroll.index', ['from'=>$from,'to'=>$to]) }}" class="manage-customers-btn">

            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                <line x1="19" y1="12" x2="5" y2="12"></line>

                <polyline points="12 19 5 12 12 5"></polyline>

            </svg>

            Back to Payroll

        </a>

        <div style="font-size:.85rem;color:#7f8c8d;">

            {{ $employee->position ?? 'Employee' }} &bull;

            {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}

        </div>

    </div>



    {{-- Summary Tiles --}}

    @php

        $hourlyRate  = $employee->salary > 0 ? round(($employee->salary / 26) / 8, 2) : 0;

        $daysPresent = $records->whereIn('status', ['present','late','half_day'])->count();

        $gross       = round($totalHours * $hourlyRate, 2);

    @endphp

    <div class="status-stats" style="margin-bottom:1.25rem;">

        <div class="status-cards">

            <div class="status-card status-total">

                <div class="status-icon"><i class="fas fa-user-tie" style="font-size:1.3rem;"></i></div>

                <div class="status-content">

                    <span class="status-name">Employee</span>

                    <div class="status-bottom"><span class="status-count" style="font-size:1rem;">{{ $employee->full_name }}</span></div>

                </div>

            </div>

            <div class="status-card status-paid">

                <div class="status-icon"><i class="fas fa-calendar-check" style="font-size:1.3rem;"></i></div>

                <div class="status-content">

                    <span class="status-name">Days Present</span>

                    <div class="status-bottom"><span class="status-count">{{ $daysPresent }}</span></div>

                </div>

            </div>

            <div class="status-card status-pending">

                <div class="status-icon"><i class="fas fa-clock" style="font-size:1.3rem;"></i></div>

                <div class="status-content">

                    <span class="status-name">Total Hours</span>

                    <div class="status-bottom"><span class="status-count">{{ number_format($totalHours,2) }}</span></div>

                </div>

            </div>

            <div class="status-card status-revenue">

                <div class="status-icon"><span style="font-size:1.5rem;font-weight:700;">&#8369;</span></div>

                <div class="status-content">

                    <span class="status-name">Gross Pay</span>

                    <div class="status-bottom"><span class="status-amount">&#8369;{{ number_format($gross,2) }}</span></div>

                </div>

            </div>

        </div>

    </div>



    {{-- Attendance Log Table --}}

    <div class="billing-list">

        <table class="billing-table">

            <thead>

                <tr>

                    <th>Date</th>

                    <th>Status</th>

                    <th style="text-align:center;">Time In</th>

                    <th style="text-align:center;">Time Out</th>

                    <th style="text-align:center;">Hours Worked</th>

                    <th>Notes</th>

                </tr>

            </thead>

            <tbody>

                @forelse($records as $rec)

                @php

                    $badge = match($rec->status) {

                        'present'  => 'paid',

                        'absent'   => 'overdue',

                        'late'     => 'pending',

                        'half_day' => 'partially_paid',

                        default    => '',

                    };

                    $label = match($rec->status) {

                        'present'  => 'Present',

                        'absent'   => 'Absent',

                        'late'     => 'Late',

                        'half_day' => 'Half Day',

                        default    => ucfirst($rec->status),

                    };

                @endphp

                <tr>

                    <td><strong>{{ \Carbon\Carbon::parse($rec->date)->format('D, M d Y') }}</strong></td>

                    <td><span class="status-badge {{ $badge }}">{{ $label }}</span></td>

                    <td style="text-align:center;">{!! $rec->time_in  ? \Carbon\Carbon::parse($rec->time_in)->format('h:i A')  : '&mdash;' !!}</td>

                    <td style="text-align:center;">{!! $rec->time_out ? \Carbon\Carbon::parse($rec->time_out)->format('h:i A') : '&mdash;' !!}</td>

                    <td style="text-align:center;font-weight:600;color:#2980b9;">
                        @if($rec->time_in && $rec->time_out)
                            @php
                                $hrs = abs(round(\Carbon\Carbon::today()->setTimeFromTimeString($rec->time_in)->diffInMinutes(\Carbon\Carbon::today()->setTimeFromTimeString($rec->time_out)) / 60, 2));
                            @endphp
                            {{ number_format($hrs, 2) }}
                        @elseif($rec->hours_worked)
                            {{ number_format(abs($rec->hours_worked), 2) }}
                        @else
                            {!! '&mdash;' !!}
                        @endif
                    </td>

                    <td style="font-size:.82rem;color:#95a5a6;">{!! $rec->notes ?? '&mdash;' !!}</td>

                </tr>

                @empty

                <tr>

                    <td colspan="6">

                        <div class="empty-state">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>

                            <h3>No records found</h3>

                            <p>No attendance records for this employee in the selected period.</p>

                        </div>

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>



</div>

@endsection


@extends('layouts.admin')



@section('title', 'Payroll')

@section('page-title', 'Payroll')



@section('styles')

<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

.status-stats .status-cards{grid-template-columns:repeat(4,1fr);}

@@media(max-width:1100px){.status-stats .status-cards{grid-template-columns:repeat(2,1fr);}}

@@media(max-width:600px){.status-stats .status-cards{grid-template-columns:repeat(2,1fr);}}

.report-filter{display:flex;flex-wrap:nowrap;align-items:center;gap:.6rem;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);padding:.6rem .8rem;margin-bottom:2rem;width:fit-content;margin-left:auto;}
.report-filter label{font-size:.73rem;font-weight:600;color:#7f8c8d;text-transform:uppercase;letter-spacing:.04em;margin:0;white-space:nowrap;}
.report-filter input[type=date]{padding:.45rem .75rem;border:1px solid #dde1e7;border-radius:8px;font-size:.875rem;font-family:'Poppins',sans-serif;color:#2c3e50;outline:none;transition:border-color .2s;}
.report-filter input[type=date]:focus{border-color:#3498db;}
.btn-rpt{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;border:none;border-radius:8px;font-size:.875rem;font-family:'Poppins',sans-serif;font-weight:500;cursor:pointer;transition:background .2s;}
.btn-rpt-apply{background:#3498db;color:#fff;}.btn-rpt-apply:hover{background:#2980b9;}

</style>

@endsection



@section('content')

<div class="billing-grid-container">



    {{-- Stat Tiles --}}

    <div class="status-stats">

        <div class="status-cards">

            <div class="status-card status-total">

                <div class="status-icon"><i class="fas fa-users" style="font-size:1.4rem;"></i></div>

                <div class="status-content">

                    <span class="status-name">Employees</span>

                    <div class="status-bottom"><span class="status-count">{{ $payroll->count() }}</span></div>

                </div>

            </div>

            <div class="status-card status-paid">

                <div class="status-icon"><i class="fas fa-clock" style="font-size:1.4rem;"></i></div>

                <div class="status-content">

                    <span class="status-name">Total Hours</span>

                    <div class="status-bottom"><span class="status-count">{{ number_format($payroll->sum(fn($r)=>str_replace(',','',$r['total_hours'])),1) }}</span></div>

                </div>

            </div>

            <div class="status-card status-revenue">

                <div class="status-icon"><span style="font-size:1.5rem;font-weight:700;">&#8369;</span></div>

                <div class="status-content">

                    <span class="status-name">Gross Pay</span>

                    <div class="status-bottom"><span class="status-amount">&#8369;{{ number_format($payroll->sum(fn($r)=>str_replace(',','',$r['gross_pay'])),2) }}</span></div>

                </div>

            </div>

            <div class="status-card status-pending">

                <div class="status-icon"><span style="font-size:1.5rem;font-weight:700;">&#8369;</span></div>

                <div class="status-content">

                    <span class="status-name">Net Pay</span>

                    <div class="status-bottom"><span class="status-amount">&#8369;{{ number_format($payroll->sum(fn($r)=>str_replace(',','',$r['net_pay'])),2) }}</span></div>

                </div>

            </div>

        </div>

    </div>



    {{-- Toolbar --}}

    <form method="GET" action="{{ route('admin.payroll.index') }}" class="report-filter">

        <label>Period Start</label>

        <input type="date" name="from" value="{{ $from }}">

        <label>Period End</label>

        <input type="date" name="to" value="{{ $to }}">

        <button type="submit" class="btn-rpt btn-rpt-apply">

            <i class="fas fa-calculator"></i>Generate

        </button>

    </form>



    {{-- Table --}}

    <div class="billing-list">

        <table class="billing-table">

            <thead>

                <tr>

                    <th>Employee</th>

                    <th style="text-align:center;">Days Present</th>

                    <th style="text-align:center;">Late Days</th>

                    <th style="text-align:center;">Total Hours</th>

                    <th style="text-align:right;">Daily Rate</th>

                    <th style="text-align:right;">Gross Pay</th>

                    <th style="text-align:right;">Late Deduct</th>

                    <th style="text-align:right;">Net Pay</th>

                    <th style="text-align:center;">Details</th>

                </tr>

            </thead>

            <tbody>

                @forelse($payroll as $row)

                <tr>

                    <td>

                        <strong>{{ $row['employee']->full_name }}</strong><br>

                        <span style="font-size:.78rem;color:#95a5a6;">{{ $row['employee']->position ?? 'Employee' }}</span>

                    </td>

                    <td style="text-align:center;">

                        <span class="status-badge paid">{{ $row['days_present'] }}</span>

                    </td>

                    <td style="text-align:center;">

                        @if($row['days_late'] > 0)

                            <span class="status-badge overdue">{{ $row['days_late'] }}</span>

                        @else

                            <span style="color:#bbb;">0</span>

                        @endif

                    </td>

                    <td style="text-align:center;font-weight:600;">{{ $row['total_hours'] }}</td>

                    <td style="text-align:right;">&#8369;{{ $row['daily_rate'] }}</td>

                    <td style="text-align:right;font-weight:600;">&#8369;{{ $row['gross_pay'] }}</td>

                    <td style="text-align:right;color:#e74c3c;">

                        @if($row['late_deduction'] != '0.00') &#8369;{{ $row['late_deduction'] }}

                        @else <span style="color:#bbb;">&mdash;</span>

                        @endif

                    </td>

                    <td style="text-align:right;font-weight:700;color:#2980b9;">&#8369;{{ $row['net_pay'] }}</td>

                    <td style="text-align:center;">

                        <div class="action-buttons">

                            <a href="{{ route('admin.payroll.detail', ['employee'=>$row['employee']->id,'from'=>$from,'to'=>$to]) }}" class="btn-action view">

                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>

                                View

                            </a>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="9">

                        <div class="empty-state">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>

                            <h3>No payroll data found</h3>

                            <p>No attendance records exist for the selected period.</p>

                        </div>

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>



</div>

@endsection


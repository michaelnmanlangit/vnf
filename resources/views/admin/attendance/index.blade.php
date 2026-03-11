@extends('layouts.admin')

@section('page-title', 'Attendance Portal')

@section('content')
<div class="attendance-wrap">
    <style>
        .attendance-wrap {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1rem;
            min-height: 80vh;
        }

        .att-card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 20px;
            padding: 44px 40px 36px;
            box-shadow: 0 8px 32px rgba(31,59,168,.12);
            text-align: center;
            border: 1px solid #e8eaf0;
        }

        .att-card h1 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 4px;
        }
        .att-card .subtitle {
            font-size: .85rem;
            color: #64748b;
            margin-bottom: 28px;
        }

        /* Alert boxes */
        .att-alert {
            border-radius: 12px;
            padding: 16px 18px;
            font-size: .88rem;
            margin-bottom: 22px;
            text-align: left;
        }
        .att-alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
        }

        /* Form */
        .att-form-group { text-align: left; margin-bottom: 20px; }
        .att-form-group label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .att-form-group input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            color: #1a202c;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .att-form-group input[type="text"]:focus {
            border-color: #4169E1;
            box-shadow: 0 0 0 3px rgba(65,105,225,.15);
        }
        .att-form-group input[type="text"]::placeholder { color: #94a3b8; }

        .btn-att-clock {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #4169E1, #1e3ba8);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
            letter-spacing: .01em;
        }
        .btn-att-clock:hover { opacity: .92; transform: translateY(-1px); }
        .btn-att-clock:active { transform: translateY(0); }

        /* ── Clock modal ── */
        .clock-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15,30,90,.55);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeInOverlay .3s ease;
        }
        @keyframes fadeInOverlay { from{opacity:0} to{opacity:1} }
        .clock-modal {
            background: #fff;
            border-radius: 20px;
            padding: 2.2rem 2rem 1.8rem;
            width: 100%;
            max-width: 360px;
            text-align: center;
            box-shadow: 0 24px 64px rgba(31,59,168,.35);
            animation: popIn .35s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        @keyframes popIn { from{transform:scale(.85);opacity:0} to{transform:scale(1);opacity:1} }
        .cm-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #eef1fc;
            box-shadow: 0 4px 18px rgba(65,105,225,.2);
            display: block;
            margin: 0 auto 1rem;
        }
        .cm-avatar-placeholder {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3ba8, #4169E1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 18px rgba(65,105,225,.2);
            font-size: 2rem;
            color: #fff;
        }
        .cm-badge {
            display: inline-block;
            padding: .28rem .9rem;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: .75rem;
        }
        .cm-badge-in   { background:#dcfce7; color:#15803d; }
        .cm-badge-out  { background:#dbeafe; color:#1d4ed8; }
        .cm-badge-done { background:#fef9c3; color:#92400e; }
        .cm-name { font-size: 1.15rem; font-weight: 700; color: #1a202c; margin-bottom: .4rem; }
        .cm-time { font-size: 1.6rem; font-weight: 800; color: #4169E1; line-height: 1; margin-bottom: .25rem; }
        .cm-date { font-size: .8rem; color: #94a3b8; margin-bottom: 1.1rem; }
        .cm-info-rows { width: 100%; display: flex; flex-direction: column; gap: .5rem; margin-bottom: 1rem; }
        .cm-hrs {
            font-size: .85rem;
            color: #374151;
            background: #f1f5f9;
            border-radius: 8px;
            padding: .45rem .9rem;
            display: flex;
            align-items: center;
            width: 100%;
            text-align: left;
            box-sizing: border-box;
        }
        .cm-hrs i { flex-shrink: 0; margin-right: .5rem; }
        .cm-hrs strong { margin-left: auto; color: #1a202c; }
        .cm-bar-wrap { height: 4px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-top: .5rem; }
        .cm-bar {
            height: 100%;
            background: linear-gradient(90deg, #1e3ba8, #4169E1);
            border-radius: 4px;
            animation: drainBar 5s linear forwards;
        }
        @keyframes drainBar { from{width:100%} to{width:0%} }
    </style>

    <div class="att-card">
        <h1>Attendance Portal</h1>
        <p class="subtitle">Enter an Employee ID to time in or time out.</p>

        {{-- Success modal --}}
        @if(session('action') && session('employee_name'))
            <div id="clockModal" class="clock-modal-overlay">
                <div class="clock-modal">
                    @if(session('employee_image'))
                        <img src="{{ session('employee_image') }}" alt="" class="cm-avatar">
                    @else
                        <div class="cm-avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                    @endif

                    <div class="cm-badge @if(session('action')==='in') cm-badge-in @elseif(session('action')==='out') cm-badge-out @else cm-badge-done @endif">
                        @if(session('action')==='in')
                            <i class="fa-solid fa-right-to-bracket"></i> Timed In
                        @elseif(session('action')==='out')
                            <i class="fa-solid fa-right-from-bracket"></i> Timed Out
                        @else
                            Already Timed Out
                        @endif
                    </div>

                    <div class="cm-name">{{ session('employee_name') }}</div>
                    <div class="cm-date">{{ session('date') }}</div>

                    @if(session('action') === 'in')
                        <div class="cm-info-rows">
                            <div class="cm-hrs">
                                <i class="fa-solid fa-right-to-bracket"></i>&nbsp;Time In: <strong>{{ session('time') }}</strong>
                            </div>
                        </div>
                    @elseif(session('action') === 'out' || session('action') === 'done')
                        <div class="cm-info-rows">
                            <div class="cm-hrs">
                                <i class="fa-solid fa-right-to-bracket"></i>&nbsp;Time In: <strong>{{ session('time_in') }}</strong>
                            </div>
                            <div class="cm-hrs">
                                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Time Out: <strong>{{ session('time') }}</strong>
                            </div>
                            @if(session('hours_worked'))
                                <div class="cm-hrs">
                                    <i class="fa-solid fa-clock"></i>&nbsp;Hours Worked: <strong>{{ session('hours_worked') }} hrs</strong>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="cm-bar-wrap"><div class="cm-bar"></div></div>
                </div>
            </div>
        @endif

        {{-- Error messages --}}
        @if($errors->any())
            <div class="att-alert att-alert-error">
                <i class="fa fa-circle-exclamation" style="margin-right:6px;"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.attendance.clock') }}">
            @csrf
            <div class="att-form-group">
                <label for="employee_id">Employee ID</label>
                <input
                    type="text"
                    id="employee_id"
                    name="employee_id"
                    placeholder="e.g. 001"
                    value="{{ old('employee_id') }}"
                    autocomplete="off"
                    autofocus
                />
            </div>
            <button type="submit" class="btn-att-clock">
                Time In / Time Out
            </button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('clockModal');
    if (modal) {
        setTimeout(() => {
            modal.style.transition = 'opacity .4s ease';
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 400);
        }, 5000);
    }
</script>
@endsection

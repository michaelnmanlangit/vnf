<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employee Attendance Portal — V&F Ice Plant & Cold Storage Inc.</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        :root {
            --brand:      #4169E1;
            --brand-dark: #2f50c4;
            --brand-deep: #1e3ba8;
            --brand-light:#eef1fc;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--brand-deep) 0%, var(--brand) 60%, #6c8ff0 100%);
            overflow: hidden;
            position: relative;
        }

        /* Floating circles — identical to login page */
        .circle {
            position: fixed;
            border-radius: 50%;
            opacity: .18;
            pointer-events: none;
        }
        .c1 { width:320px; height:320px; background:#fff; top:-80px;  left:-80px;  animation: floatA 7s ease-in-out infinite; }
        .c2 { width:200px; height:200px; background:#fff; bottom:-60px; right:-60px; animation: floatB 9s ease-in-out infinite; }
        .c3 { width:130px; height:130px; background:#fff; top:55%;  left:70%;   animation: floatC 6s ease-in-out infinite; }
        .c4 { width: 80px; height: 80px; background:#fff; top:15%;  right:12%;  animation: floatD 8s ease-in-out infinite; }

        @keyframes floatA { 0%,100%{transform:translate(0,0)} 50%{transform:translate(18px,22px)} }
        @keyframes floatB { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-16px,-18px)} }
        @keyframes floatC { 0%,100%{transform:translate(0,0)} 50%{transform:translate(12px,-14px)} }
        @keyframes floatD { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-10px,12px)} }

        /* Card */
        .card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 20px;
            padding: 44px 40px 36px;
            box-shadow: 0 20px 60px rgba(31,59,168,.35);
            text-align: center;
        }

        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .logo-wrap img { height: 58px; object-fit: contain; }
        .logo-wrap span { font-size: .78rem; font-weight: 600; letter-spacing: .08em; color: var(--brand); text-transform: uppercase; }

        h1 { font-size: 1.35rem; font-weight: 700; color: #1a202c; margin-bottom: 4px; }
        .subtitle { font-size: .85rem; color: #64748b; margin-bottom: 28px; }

        /* Alert boxes */
        .alert {
            border-radius: 12px;
            padding: 16px 18px;
            font-size: .88rem;
            margin-bottom: 22px;
            text-align: left;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        .alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
        }
        .alert .badge {
            display: inline-block;
            background: var(--brand);
            color: #fff;
            border-radius: 20px;
            padding: 2px 11px;
            font-size: .78rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .alert-success .badge { background: #22c55e; }
        .alert .row { display: flex; justify-content: space-between; margin-top: 10px; gap: 8px; }
        .alert .row span { font-size: .82rem; font-weight: 500; color: #374151; }
        .alert .row strong { display: block; font-size: .93rem; font-weight: 700; color: #111827; }

        /* Form */
        .form-group { text-align: left; margin-bottom: 20px; }
        label { display: block; font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
        input[type="text"] {
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
        input[type="text"]:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(65,105,225,.15);
        }
        input[type="text"]::placeholder { color: #94a3b8; }

        .btn-clock {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--brand), var(--brand-deep));
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
        .btn-clock:hover { opacity: .92; transform: translateY(-1px); }
        .btn-clock:active { transform: translateY(0); }

        .back-link { margin-top: 20px; font-size: .82rem; color: #94a3b8; }
        .back-link a { color: var(--brand); text-decoration: none; font-weight: 600; }
        .back-link a:hover { text-decoration: underline; }

        @media(max-width:480px) {
            .card { padding: 32px 22px 28px; border-radius: 14px; }
        }

        /* ── Clock modal ── */
        .clock-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15,30,90,.55);
            backdrop-filter: blur(4px);
            z-index: 100;
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
            border: 3px solid var(--brand-light, #eef1fc);
            box-shadow: 0 4px 18px rgba(65,105,225,.2);
            display: block;
            margin: 0 auto 1rem;
        }
        .cm-avatar-placeholder {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-deep), var(--brand));
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
        .cm-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: .4rem;
        }
        .cm-time {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--brand);
            line-height: 1;
            margin-bottom: .25rem;
        }
        .cm-date {
            font-size: .8rem;
            color: #94a3b8;
            margin-bottom: 1.1rem;
        }
        .cm-info-rows {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: .5rem;
            margin-bottom: 1rem;
        }
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
        .cm-bar-wrap {
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: .5rem;
        }
        .cm-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--brand-deep), var(--brand));
            border-radius: 4px;
            animation: drainBar 5s linear forwards;
        }
        @keyframes drainBar { from{width:100%} to{width:0%} }
    </style>
</head>
<body>
    <div class="circle c1"></div>
    <div class="circle c2"></div>
    <div class="circle c3"></div>
    <div class="circle c4"></div>

    <div class="card">
        <div class="logo-wrap">
            <img src="{{ asset('logo.png') }}" alt="V&F Logo" />
            <span>V&amp;F Ice Plant &amp; Cold Storage Inc.</span>
        </div>

        <h1>Attendance Portal</h1>
        <p class="subtitle">Enter your Employee ID to time in or time out.</p>

        {{-- Success modal trigger data --}}
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
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Timed In
                        @elseif(session('action')==='out')
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Timed Out
                        @else
                            Already Timed Out
                        @endif
                    </div>

                    <div class="cm-name">{{ session('employee_name') }}</div>
                    <div class="cm-date">{{ session('date') }}</div>

                    @if(session('action') === 'out' || session('action') === 'done')
                        <div class="cm-info-rows">
                            <div class="cm-hrs">
                                <i class="fa-solid fa-right-to-bracket"></i>Time In: <strong>{{ session('time_in') }}</strong>
                            </div>
                            <div class="cm-hrs">
                                <i class="fa-solid fa-right-from-bracket"></i>Time Out: <strong>{{ session('time') }}</strong>
                            </div>
                            @if(session('hours_worked'))
                                <div class="cm-hrs">
                                    <i class="fa-solid fa-clock"></i>Hours Worked: <strong>{{ session('hours_worked') }} hrs</strong>
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
            <div class="alert alert-error">
                <i class="fa fa-circle-exclamation" style="margin-right:6px;"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('attendance.clock') }}">
            @csrf
            <div class="form-group">
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
            <button type="submit" class="btn-clock">
                Time In / Time Out
            </button>
        </form>

        <p class="back-link">
            <a href="{{ url('/') }}"><i class="fa fa-arrow-left" style="margin-right:4px;"></i>Go Back</a>
        </p>
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
</body>
</html>

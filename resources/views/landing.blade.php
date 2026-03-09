<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V&F Ice Plant and Cold Storage Inc.</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts – Inter (matches system font) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ── System palette (matches login + admin) ── */
        :root {
            --brand:       #4169E1;
            --brand-dark:  #2f50c4;
            --brand-deep:  #1e3ba8;
            --brand-light: #eef1fc;
            --brand-mid:   #d6e0fb;
            --text-head:   #1a202c;
            --text-sub:    #64748b;
            --border:      #e2e8f0;
            --radius:      12px;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: var(--text-head);
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: linear-gradient(135deg, var(--brand-deep) 0%, var(--brand-dark) 60%, var(--brand) 100%);
            backdrop-filter: blur(8px);
            padding: .85rem 0;
            transition: background 0.3s, box-shadow 0.3s;
            box-shadow: 0 2px 20px rgba(65,105,225,.25);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff !important;
            letter-spacing: .3px;
        }
        .navbar-brand img {
            width: 36px; height: 36px;
            border-radius: 8px;
            object-fit: contain;
            background: rgba(255,255,255,.15);
            padding: 3px;
        }
        .navbar-brand .brand-text { line-height: 1.2; }
        .navbar-brand .brand-sub { font-size: .68rem; font-weight: 400; opacity: .75; display: block; }
        .navbar-nav .nav-link {
            color: rgba(255,255,255,.85) !important;
            font-weight: 500;
            padding: .5rem 1rem !important;
            transition: color .2s;
            font-size: .92rem;
        }
        .navbar-nav .nav-link:hover { color: #fff !important; }
        .btn-login {
            background: rgba(255,255,255,.15);
            border: 1.5px solid rgba(255,255,255,.4);
            color: #fff !important;
            border-radius: 8px;
            padding: .42rem 1.2rem !important;
            font-weight: 600;
            font-size: .88rem;
            transition: background .2s, transform .15s, border-color .2s;
        }
        .btn-login:hover { background: rgba(255,255,255,.28); border-color: rgba(255,255,255,.7); transform: translateY(-1px); }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(145deg, var(--brand-deep) 0%, var(--brand-dark) 55%, var(--brand) 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        /* Floating circles – same as login page */
        .hero-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
            pointer-events: none;
            will-change: transform;
        }
        .hc1 { width:400px;height:400px; top:-120px;right:-120px; animation: floatA 9s ease-in-out infinite; }
        .hc2 { width:240px;height:240px; bottom:-80px;left:-80px; background:rgba(255,255,255,.06); animation:floatB 11s ease-in-out infinite; }
        .hc3 { width:150px;height:150px; top:55%;left:8%; background:rgba(255,255,255,.05); animation:floatC 7s ease-in-out infinite; }
        .hc4 { width:90px;height:90px; bottom:22%;right:14%; background:rgba(255,255,255,.07); animation:floatD 8s ease-in-out infinite 1.5s; }
        .hc5 { width:60px;height:60px; top:20%;left:20%; background:rgba(255,255,255,.06); animation:floatA 6s ease-in-out infinite 2s; }
        .hc6 { width:180px;height:180px; top:30%;right:18%; background:rgba(255,255,255,.04); animation:floatB 10s ease-in-out infinite 1s; }
        @keyframes floatA { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(-18px,22px) scale(1.05)} 66%{transform:translate(14px,-12px) scale(.96)} }
        @keyframes floatB { 0%,100%{transform:translate(0,0) scale(1)} 40%{transform:translate(20px,-26px) scale(1.07)} 70%{transform:translate(-10px,16px) scale(.94)} }
        @keyframes floatC { 0%,100%{transform:translate(0,0)} 50%{transform:translate(16px,-20px)} }
        @keyframes floatD { 0%,100%{transform:translate(0,0) rotate(0deg)} 50%{transform:translate(-14px,18px) rotate(20deg)} }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.28);
            color: #fff;
            border-radius: 50px;
            padding: .38rem 1rem;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
        }
        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 4rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -.5px;
        }
        .hero h1 .highlight { color: rgba(255,255,255,.72); font-weight: 300; }
        .hero-sub {
            color: rgba(255,255,255,.78);
            font-size: 1.05rem;
            max-width: 520px;
            line-height: 1.75;
        }
        .btn-hero-primary {
            background: #fff;
            color: var(--brand-deep);
            border-radius: 10px;
            padding: .72rem 1.8rem;
            font-weight: 700;
            font-size: .95rem;
            transition: transform .2s, box-shadow .2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }
        .btn-hero-primary:hover {
            color: var(--brand-deep);
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0,0,0,.22);
        }
        .btn-hero-outline {
            border: 1.5px solid rgba(255,255,255,.5);
            color: #fff;
            border-radius: 10px;
            padding: .72rem 1.8rem;
            font-weight: 600;
            font-size: .95rem;
            transition: background .2s, transform .2s, border-color .2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }
        .btn-hero-outline:hover {
            background: rgba(255,255,255,.14);
            border-color: rgba(255,255,255,.75);
            color: #fff;
            transform: translateY(-3px);
        }
        .hero-stats {
            display: flex;
            gap: 2.5rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,.15);
        }
        .hero-stat-num {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: .7rem;
            color: rgba(255,255,255,.6);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-top: .2rem;
        }
        /* Hero logo card */
        .hero-logo-card {
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 24px;
            padding: 2.5rem;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            backdrop-filter: blur(10px);
            animation: heroCardFloat 6s ease-in-out infinite;
        }
        .hero-logo-card img { width: 110px; height: 110px; object-fit: contain; border-radius: 16px; }
        .hero-logo-card .card-label { color: rgba(255,255,255,.85); font-size: .8rem; font-weight: 600; text-align: center; letter-spacing: .5px; }
        @keyframes heroCardFloat {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-14px); }
        }

        /* ── SCROLL REVEAL ── */
        .reveal {
            opacity: 0;
            transform: translateY(36px);
            transition: opacity .65s cubic-bezier(.4,0,.2,1), transform .65s cubic-bezier(.4,0,.2,1);
        }
        .reveal.visible {
            opacity: 1;
            transform: none;
        }
        .reveal-left  { opacity:0; transform:translateX(-40px); transition: opacity .65s cubic-bezier(.4,0,.2,1), transform .65s cubic-bezier(.4,0,.2,1); }
        .reveal-right { opacity:0; transform:translateX( 40px); transition: opacity .65s cubic-bezier(.4,0,.2,1), transform .65s cubic-bezier(.4,0,.2,1); }
        .reveal-left.visible, .reveal-right.visible { opacity:1; transform:none; }
        .reveal-delay-1 { transition-delay: .1s !important; }
        .reveal-delay-2 { transition-delay: .2s !important; }
        .reveal-delay-3 { transition-delay: .3s !important; }
        .reveal-delay-4 { transition-delay: .4s !important; }
        .reveal-delay-5 { transition-delay: .5s !important; }
        .reveal-delay-6 { transition-delay: .6s !important; }

        /* ── SECTION HEADERS ── */
        .section-eyebrow {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--brand);
            margin-bottom: .45rem;
        }
        .section-title {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 800;
            color: var(--text-head);
            line-height: 1.2;
            letter-spacing: -.3px;
        }
        .divider {
            width: 48px;
            height: 4px;
            background: var(--brand);
            border-radius: 4px;
            margin: 1rem auto 0;
        }

        /* ── ABOUT ── */
        #about { background: #f8faff; padding: 64px 0; }
        .about-img-wrap { position: relative; }
        .about-img-wrap img {
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(65,105,225,.18);
        }
        .about-badge {
            position: absolute;
            bottom: -20px;
            right: -20px;
            background: linear-gradient(135deg, var(--brand-deep), var(--brand-dark));
            color: #fff;
            border-radius: 14px;
            padding: 1.2rem 1.5rem;
            text-align: center;
            box-shadow: 0 8px 28px rgba(65,105,225,.35);
        }
        .about-badge .num { font-size: 2.2rem; font-weight: 800; line-height: 1; }
        .about-badge .lbl { font-size: .68rem; text-transform: uppercase; letter-spacing: 1.5px; opacity: .8; }
        .feature-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: var(--brand-light);
            border: 1px solid var(--brand-mid);
            border-radius: 8px;
            padding: .38rem .9rem;
            font-size: .82rem;
            font-weight: 500;
            color: var(--brand-dark);
            margin: .25rem .25rem .25rem 0;
            transition: background .2s, transform .15s;
        }
        .feature-pill:hover { background: var(--brand-mid); transform: translateY(-1px); }
        .feature-pill i { color: var(--brand); }

        /* ── SERVICES ── */
        #services { padding: 64px 0; background: #fff; }
        .service-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem 1.75rem;
            background: #fff;
            height: 100%;
            transition: transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s, border-color .3s;
            position: relative;
            overflow: hidden;
        }
        .service-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-deep), var(--brand));
            border-radius: 16px 16px 0 0;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .35s cubic-bezier(.4,0,.2,1);
        }
        .service-card:hover::before { transform: scaleX(1); }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 48px rgba(65,105,225,.13);
            border-color: var(--brand-mid);
        }
        .service-icon {
            width: 60px; height: 60px;
            border-radius: 14px;
            background: var(--brand-light);
            border: 1px solid var(--brand-mid);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.45rem;
            color: var(--brand);
            margin-bottom: 1.2rem;
            transition: background .25s, transform .25s;
        }
        .service-card:hover .service-icon {
            background: var(--brand);
            color: #fff;
            transform: scale(1.08);
        }
        .service-card h5 {
            font-weight: 700;
            color: var(--text-head);
            font-size: 1.05rem;
            margin-bottom: .55rem;
        }
        .service-card p { font-size: .875rem; color: var(--text-sub); line-height: 1.7; }

        /* ── PRODUCTS ── */
        #products { background: var(--brand-light); padding: 64px 0; }

        .products-scroll-wrapper {
            overflow-x: auto;
            padding-bottom: 1rem;
            cursor: grab;
            user-select: none;
            scrollbar-width: thin;
            scrollbar-color: var(--brand) #e2e8f0;
        }
        .products-scroll-wrapper:active,
        .products-scroll-wrapper.dragging { cursor: grabbing; user-select: none; }
        .products-scroll-wrapper::-webkit-scrollbar { height: 6px; }
        .products-scroll-wrapper::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 3px; }
        .products-scroll-wrapper::-webkit-scrollbar-thumb { background: var(--brand); border-radius: 3px; }

        .products-scroll-track {
            display: flex;
            gap: 1.25rem;
            width: max-content;
            padding: 0.5rem 0.25rem 0.5rem;
        }

        .product-card-lp {
            width: 220px;
            flex-shrink: 0;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(0,0,0,.08);
            overflow: hidden;
            transition: transform .25s, box-shadow .25s;
            display: flex;
            flex-direction: column;
        }
        .product-card-lp:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(65,105,225,.15); }

        .product-card-lp .pc-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: #f1f5f9;
        }
        .product-card-lp .pc-img-placeholder {
            width: 100%;
            height: 140px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            font-size: 2.5rem;
        }
        .product-card-lp .pc-body {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .pc-category {
            display: inline-block;
            background: #e0eaff;
            color: var(--brand);
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: .2rem .55rem;
            border-radius: 20px;
            width: fit-content;
        }
        .pc-name { font-weight: 700; color: #1e293b; font-size: .98rem; margin: 0; }
        .pc-price { color: #16a34a; font-weight: 800; font-size: 1.15rem; }
        .pc-stock { color: #94a3b8; font-size: .78rem; }
        .pc-btn {
            display: block;
            width: 100%;
            margin-top: auto;
            padding: .55rem;
            background: var(--brand);
            color: #fff;
            font-weight: 600;
            font-size: .85rem;
            border: none;
            border-radius: 0 0 16px 16px;
            text-align: center;
            text-decoration: none;
            transition: background .2s;
        }
        .pc-btn:hover { background: var(--brand-dark); color: #fff; }

        /* ── WHY CHOOSE US ── */
        #why { padding: 64px 0; background: #fff; }
        .why-card {
            text-align: center;
            padding: 2.2rem 1.5rem;
            border-radius: 16px;
            transition: background .25s, transform .25s;
        }
        .why-card:hover { background: var(--brand-light); transform: translateY(-4px); }
        .why-icon {
            width: 72px; height: 72px;
            border-radius: 18px;
            background: var(--brand-light);
            border: 1px solid var(--brand-mid);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.7rem;
            color: var(--brand);
            margin: 0 auto 1.1rem;
            transition: background .25s, transform .25s;
        }
        .why-card:hover .why-icon { background: var(--brand); color: #fff; transform: scale(1.1); }
        .why-card h5 { font-weight: 700; color: var(--text-head); font-size: 1rem; }
        .why-card p  { font-size: .875rem; color: var(--text-sub); line-height: 1.65; }

        /* ── LOCATION ── */
        #location { padding: 64px 0; background: var(--brand-light); }
        .contact-box {
            background: linear-gradient(145deg, var(--brand-deep) 0%, var(--brand-dark) 60%, var(--brand) 100%);
            color: #fff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .contact-box::before {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
            top: -60px; right: -60px;
            pointer-events: none;
        }
        .contact-box h4 { font-weight: 700; margin-bottom: 1.5rem; font-size: 1.15rem; }
        .contact-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.4rem;
            align-items: flex-start;
        }
        .contact-item .icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: rgba(255,255,255,.14);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
            color: rgba(255,255,255,.9);
        }
        .contact-item .info .label {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            opacity: .6;
        }
        .contact-item .info p {
            margin: 0;
            font-size: .9rem;
            font-weight: 500;
            line-height: 1.5;
        }
        .map-wrap iframe {
            width: 100%;
            height: 100%;
            min-height: 420px;
            border: 0;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(65,105,225,.15);
        }

        /* ── FOOTER ── */
        footer {
            background: linear-gradient(135deg, var(--brand-deep) 0%, var(--brand-dark) 100%);
            color: rgba(255,255,255,.7);
            padding: 40px 0 20px;
        }
        .footer-brand { display: flex; align-items: center; gap: .6rem; font-size: 1.05rem; font-weight: 700; color: #fff; }
        .footer-brand img { width: 32px; height: 32px; border-radius: 7px; object-fit: contain; background: rgba(255,255,255,.12); padding: 2px; }
        .footer-desc { font-size: .85rem; margin-top: .6rem; max-width: 300px; line-height: 1.7; }
        .footer-col h6 { color: #fff; font-weight: 600; margin-bottom: 1rem; font-size: .88rem; letter-spacing: .5px; text-transform: uppercase; }
        .footer-col ul { list-style: none; padding: 0; margin: 0; }
        .footer-col ul li { margin-bottom: .5rem; }
        .footer-col ul li a { color: rgba(255,255,255,.6); text-decoration: none; font-size: .85rem; transition: color .2s, padding-left .2s; }
        .footer-col ul li a:hover { color: #fff; padding-left: 4px; }
        .social-link {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: rgba(255,255,255,.1);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,.75);
            text-decoration: none;
            transition: background .2s, transform .2s, color .2s;
            margin-right: .4rem;
            font-size: .9rem;
        }
        .social-link:hover { background: var(--brand); color: #fff; transform: translateY(-2px); }
        .footer-divider { border-color: rgba(255,255,255,.1); margin: 2rem 0 1.25rem; }
        .footer-bottom { font-size: .8rem; }

        /* ── SCROLL TO TOP ── */
        #scrollTop {
            position: fixed;
            bottom: 2rem; right: 2rem;
            width: 42px; height: 42px;
            border-radius: 10px;
            background: var(--brand);
            color: #fff;
            border: none;
            box-shadow: 0 4px 16px rgba(65,105,225,.4);
            display: none;
            align-items: center; justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            z-index: 999;
            transition: transform .2s, background .2s;
        }
        #scrollTop:hover { transform: translateY(-3px); background: var(--brand-dark); }

        /* ── TEMP CARDS ── */
        .temp-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            height: 100%;
            transition: border-color .25s, box-shadow .25s, transform .25s;
        }
        .temp-card:hover { border-color: var(--brand-mid); box-shadow: 0 8px 28px rgba(65,105,225,.1); transform: translateY(-4px); }
        .temp-card .temp-num { font-size: 2.4rem; font-weight: 800; color: var(--brand); line-height: 1; margin-bottom: .5rem; }
        .temp-card h6 { font-weight: 700; color: var(--text-head); font-size: .95rem; }
        .temp-card p { font-size: .85rem; color: var(--text-sub); }
    </style>
</head>
<body>

<!-- ╔══════════════════════════════╗ -->
<!--   NAVBAR                        -->
<!-- ╚══════════════════════════════╝ -->
<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('logo.png') }}" alt="V&F Logo">
            <div class="brand-text">
                V&amp;F Ice Plant
                <span class="brand-sub">and Cold Storage Inc.</span>
            </div>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="fa-solid fa-bars text-white"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto me-3 align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#products">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="#why">Why Us</a></li>
                <li class="nav-item"><a class="nav-link" href="#location">Location</a></li>
            </ul>
            <a href="{{ route('login') }}" class="nav-link btn-login">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Login
            </a>
        </div>
    </div>
</nav>

<!-- ╔══════════════════════════════╗ -->
<!--   HERO                          -->
<!-- ╚══════════════════════════════╝ -->
<section class="hero" id="home">
    <!-- Floating circles (same as login page) -->
    <div class="hero-circle hc1"></div>
    <div class="hero-circle hc2"></div>
    <div class="hero-circle hc3"></div>
    <div class="hero-circle hc4"></div>
    <div class="hero-circle hc5"></div>
    <div class="hero-circle hc6"></div>

    <div class="container position-relative" style="z-index:2; padding-top: 80px; padding-bottom: 40px;">
        <div class="row align-items-center g-5">
            <!-- LEFT: Text -->
            <div class="col-lg-7">
                <div class="hero-badge"><i class="fa-solid fa-location-dot"></i> San Roque, Santo Tomas, Batangas</div>
                <h1>
                    V&amp;F Ice Plant<br>
                    <span class="highlight">&amp; Cold Storage</span><br>
                    Inc.
                </h1>
                <p class="hero-sub mt-3">
                    Your trusted partner for ice production, cold storage, and temperature-controlled logistics
                    — backed by a complete digital platform covering orders, GPS delivery, inventory, billing,
                    and workforce management. Serving businesses across Batangas.
                </p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="#services" class="btn-hero-primary">
                        <i class="fa-solid fa-layer-group"></i>Our Services
                    </a>
                    <a href="#location" class="btn-hero-outline">
                        <i class="fa-solid fa-map-pin"></i>Find Us
                    </a>
                    <a href="{{ route('login') }}" class="btn-hero-outline">
                        <i class="fa-solid fa-right-to-bracket"></i>Login
                    </a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-num">5</div>
                        <div class="hero-stat-label">Cold Storage Units</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">7</div>
                        <div class="hero-stat-label">Product Categories</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">24/7</div>
                        <div class="hero-stat-label">System Monitoring</div>
                    </div>
                </div>
            </div>
            <!-- RIGHT: Floating logo card -->
            <div class="col-lg-5 text-center d-none d-lg-flex justify-content-center">
                <div class="hero-logo-card">
                    <img src="{{ asset('logo.png') }}" alt="V&F Logo">
                    <div class="card-label">
                        V&amp;F Ice Plant<br>
                        <span style="font-weight:400;opacity:.7;font-size:.73rem;">and Cold Storage Inc.</span>
                    </div>
                    <div style="display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;">
                        <span style="background:rgba(255,255,255,.12);border-radius:6px;padding:.25rem .7rem;font-size:.72rem;color:rgba(255,255,255,.8);"><i class="fa-solid fa-temperature-low me-1"></i>5 Cold Units</span>
                        <span style="background:rgba(255,255,255,.12);border-radius:6px;padding:.25rem .7rem;font-size:.72rem;color:rgba(255,255,255,.8);"><i class="fa-solid fa-location-arrow me-1"></i>GPS Delivery</span>
                        <span style="background:rgba(255,255,255,.12);border-radius:6px;padding:.25rem .7rem;font-size:.72rem;color:rgba(255,255,255,.8);"><i class="fa-solid fa-store me-1"></i>Order Portal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════╗ -->
<!--   ABOUT                         -->
<!-- ╚══════════════════════════════╝ -->
<section id="about">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 text-center reveal-left">
                <svg viewBox="0 0 420 360" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%; max-width:420px; filter:drop-shadow(0 20px 50px rgba(65,105,225,.22));">
                    <!-- Background card -->
                    <rect x="10" y="10" width="400" height="340" rx="24" fill="url(#cardGrad)"/>
                    <!-- Snowflake patterns -->
                    <circle cx="60" cy="55" r="22" fill="rgba(255,255,255,.07)"/>
                    <circle cx="360" cy="290" r="30" fill="rgba(255,255,255,.06)"/>
                    <circle cx="380" cy="60" r="16" fill="rgba(255,255,255,.05)"/>
                    <!-- Warehouse building -->
                    <rect x="70" y="160" width="280" height="140" rx="6" fill="rgba(255,255,255,.13)"/>
                    <polygon points="55,160 210,80 365,160" fill="rgba(255,255,255,.2)"/>
                    <!-- Roof ridge -->
                    <line x1="210" y1="80" x2="210" y2="160" stroke="rgba(255,255,255,.15)" stroke-width="2"/>
                    <!-- Door -->
                    <rect x="170" y="220" width="80" height="80" rx="4" fill="rgba(255,255,255,.18)"/>
                    <rect x="205" y="220" width="3" height="80" fill="rgba(255,255,255,.25)"/>
                    <circle cx="196" cy="262" r="4" fill="rgba(255,255,255,.6)"/>
                    <circle cx="224" cy="262" r="4" fill="rgba(255,255,255,.6)"/>
                    <!-- Windows -->
                    <rect x="90" y="200" width="50" height="40" rx="4" fill="rgba(255,255,255,.15)"/>
                    <line x1="115" y1="200" x2="115" y2="240" stroke="rgba(255,255,255,.25)" stroke-width="1.5"/>
                    <line x1="90" y1="220" x2="140" y2="220" stroke="rgba(255,255,255,.25)" stroke-width="1.5"/>
                    <rect x="280" y="200" width="50" height="40" rx="4" fill="rgba(255,255,255,.15)"/>
                    <line x1="305" y1="200" x2="305" y2="240" stroke="rgba(255,255,255,.25)" stroke-width="1.5"/>
                    <line x1="280" y1="220" x2="330" y2="220" stroke="rgba(255,255,255,.25)" stroke-width="1.5"/>
                    <!-- Logo (center top) -->
                    <circle cx="210" cy="133" r="40" fill="rgba(255,255,255,.15)"/>
                    <image href="{{ asset('logo.png') }}" x="170" y="93" width="80" height="80" clip-path="url(#logoClip)"/>
                    <!-- Temp badge -->
                    <rect x="28" y="270" width="90" height="50" rx="10" fill="rgba(255,255,255,.15)"/>
                    <text x="73" y="292" text-anchor="middle" fill="white" font-size="11" font-family="Inter,sans-serif" font-weight="600" opacity=".7">TEMP</text>
                    <text x="73" y="312" text-anchor="middle" fill="white" font-size="15" font-family="Inter,sans-serif" font-weight="800">-18°C</text>
                    <!-- GPS badge -->
                    <rect x="302" y="270" width="90" height="50" rx="10" fill="rgba(255,255,255,.15)"/>
                    <text x="347" y="292" text-anchor="middle" fill="white" font-size="11" font-family="Inter,sans-serif" font-weight="600" opacity=".7">GPS</text>
                    <text x="347" y="312" text-anchor="middle" fill="white" font-size="15" font-family="Inter,sans-serif" font-weight="800">LIVE</text>
                    <!-- Ground line -->
                    <rect x="55" y="300" width="310" height="3" rx="2" fill="rgba(255,255,255,.12)"/>
                    <!-- Label -->
                    <text x="210" y="336" text-anchor="middle" fill="rgba(255,255,255,.45)" font-size="10" font-family="Inter,sans-serif" font-weight="700" letter-spacing="2">V&amp;F COLD STORAGE INC.</text>
                    <defs>
                        <linearGradient id="cardGrad" x1="0" y1="0" x2="400" y2="340" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#1e3ba8"/>
                            <stop offset="55%" stop-color="#2f50c4"/>
                            <stop offset="100%" stop-color="#4169E1"/>
                        </linearGradient>
                        <clipPath id="logoClip">
                            <circle cx="210" cy="133" r="40"/>
                        </clipPath>
                    </defs>
                </svg>
            </div>

            <!-- Text -->
            <div class="col-lg-7 reveal-right">
                <div class="section-eyebrow">About The Company</div>
                <h2 class="section-title">V&amp;F Ice Plant &amp;<br>Cold Storage Inc.</h2>
                <div class="divider" style="margin: .75rem 0 1.25rem; background: var(--brand);"></div>
                <p class="text-muted" style="font-size:.95rem; line-height:1.85; color:var(--text-sub) !important;">
                    Located in <strong>San Roque, City of Santo Tomas, Batangas</strong>, V&amp;F Ice Plant and Cold Storage Inc.
                    is a vital supplier in the region, providing premium ice and cold preservation services to a wide range
                    of establishments. Our facility stores diverse products under strict temperature control to maintain
                    freshness and quality for every client we serve.
                </p>
                <p class="text-muted" style="font-size:.95rem; line-height:1.85; color:var(--text-sub) !important;">
                    Our integrated management platform covers the full operation: customer order placement through
                    a self-service portal, GPS-tracked deliveries, continuous monitoring across five cold storage
                    units, inventory control with expiry alerts, staff attendance and payroll, and complete
                    digital billing — all in one system.
                </p>

                <div class="mt-3">
                    <span class="feature-pill"><i class="fa-solid fa-store"></i> Customer Order Portal</span>
                    <span class="feature-pill"><i class="fa-solid fa-temperature-low"></i> Temp Monitoring</span>
                    <span class="feature-pill"><i class="fa-solid fa-location-arrow"></i> GPS Delivery</span>
                    <span class="feature-pill"><i class="fa-solid fa-boxes-stacked"></i> Inventory Management</span>
                    <span class="feature-pill"><i class="fa-solid fa-file-invoice-dollar"></i> Digital Billing</span>
                    <span class="feature-pill"><i class="fa-solid fa-users-gear"></i> Employee Management</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ╔══════════════════════════════╗ -->
<!--   SERVICES                      -->
<!-- ╚══════════════════════════════╝ -->
<section id="services">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-eyebrow reveal">What We Offer</div>
            <h2 class="section-title reveal">Our Core Services</h2>
            <div class="divider mx-auto reveal"></div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4 reveal reveal-delay-1">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-store"></i></div>
                    <h5>Customer Ordering Portal</h5>
                    <p>Business clients register, verify their email with OTP, complete their company profile, then browse the product catalog, add to cart, and place orders online — no paperwork required.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal reveal-delay-2">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-thermometer-half"></i></div>
                    <h5>Cold Storage Monitoring</h5>
                    <p>Five climate-controlled storage units (Unit A–E) are continuously tracked for temperature and humidity. Logs are recorded around the clock with instant alerts for any deviation from safe ranges.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal reveal-delay-3">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-truck"></i></div>
                    <h5>GPS Delivery Tracking</h5>
                    <p>Each delivery is assigned to a driver who submits live GPS location updates. Admins and customers can see exactly where a shipment is at every point of the journey.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal reveal-delay-4">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <h5>Inventory Management</h5>
                    <p>Full visibility into stock quantities, storage locations, and expiration dates across all five units — with automatic status tracking (in stock, low stock, expiring soon, expired).</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal reveal-delay-5">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <h5>Billing &amp; Invoicing</h5>
                    <p>Admin generates VAT-inclusive invoices per order. Payments are recorded incrementally, with running totals for amount paid and balance. Financial summaries available in reports.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal reveal-delay-6">
                <div class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-users-gear"></i></div>
                    <h5>Workforce Management</h5>
                    <p>Track employee attendance, compute payroll based on hours worked, assign relocation tasks, and allocate workers to storage units — with a public clock-in portal for daily time records.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════╗ -->
<!--   PRODUCTS STORED               -->
<!-- ╚══════════════════════════════╝ -->
<section id="products">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-eyebrow reveal">Stored Products</div>
            <h2 class="section-title reveal">What We Preserve</h2>
            <div class="divider mx-auto reveal"></div>
            <p class="text-muted reveal" style="max-width:540px; margin:1rem auto 0; font-size:.92rem; color:var(--text-sub) !important;">
                Our cold storage facility handles a wide variety of perishable goods, each managed
                under strict temperature protocols to maintain freshness and quality.
            </p>
        </div>

        <div class="products-scroll-wrapper reveal" id="productsScrollWrapper">
            <div class="products-scroll-track" id="productsScrollTrack">
                @forelse($products as $product)
                <div class="product-card-lp">
                    @if($product->product_image)
                        <img src="{{ str_starts_with($product->product_image, 'data:') ? $product->product_image : asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}" class="pc-img">
                    @else
                        <div class="pc-img-placeholder"><i class="fa-solid fa-box-open"></i></div>
                    @endif
                    <div class="pc-body">
                        <span class="pc-category">{{ $product->category ?? 'Product' }}</span>
                        <div class="pc-name">{{ $product->product_name }}</div>
                        <div class="pc-price">₱{{ number_format($product->price ?? 0, 2) }}</div>
                        <div class="pc-stock">Stock: {{ number_format($product->quantity, 0) }} {{ $product->unit }}</div>
                    </div>
                    <a href="{{ route('login') }}" class="pc-btn"><i class="fa-solid fa-cart-shopping"></i> Order Now</a>
                </div>
                @empty
                <p class="text-muted">No products available.</p>
                @endforelse
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-md-4 reveal reveal-delay-1">
                <div class="temp-card">
                    <div class="temp-num">-18°C</div>
                    <h6>Deep Freeze</h6>
                    <p>For long-term frozen storage of meat, poultry, and seafood products.</p>
                </div>
            </div>
            <div class="col-md-4 reveal reveal-delay-2">
                <div class="temp-card">
                    <div class="temp-num">0°C – 4°C</div>
                    <h6>Chiller Units</h6>
                    <p>Ideal for fresh produce, dairy, and products requiring near-zero cooling.</p>
                </div>
            </div>
            <div class="col-md-4 reveal reveal-delay-3">
                <div class="temp-card">
                    <div class="temp-num">24/7</div>
                    <h6>Continuous Monitoring</h6>
                    <p>Instant alerts and digital logs for every unit, around the clock.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════╗ -->
<!--   WHY CHOOSE US                 -->
<!-- ╚══════════════════════════════╝ -->
<section id="why">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-eyebrow reveal">Our Advantage</div>
            <h2 class="section-title reveal">Why Choose V&amp;F?</h2>
            <div class="divider mx-auto reveal"></div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3 reveal reveal-delay-1">
                <div class="why-card">
                    <div class="why-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    <h5>Online Order &amp; Track</h5>
                    <p>Clients order online and track each step — from packing to doorstep delivery — through a dedicated self-service portal.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal reveal-delay-2">
                <div class="why-card">
                    <div class="why-icon"><i class="fa-solid fa-temperature-low"></i></div>
                    <h5>5 Monitored Cold Units</h5>
                    <p>Five dedicated storage units with continuous temperature and humidity monitoring keep every product within safe conditions at all times.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal reveal-delay-3">
                <div class="why-card">
                    <div class="why-icon"><i class="fa-solid fa-location-arrow"></i></div>
                    <h5>GPS-Tracked Deliveries</h5>
                    <p>Every shipment has an assigned driver with live location updates visible to both the admin team and the receiving customer throughout delivery.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal reveal-delay-4">
                <div class="why-card">
                    <div class="why-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <h5>Complete Billing Records</h5>
                    <p>Every order generates a VAT-inclusive invoice. Payments are logged immediately, giving you an accurate financial picture at any time.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════╗ -->
<!--   LOCATION & CONTACT            -->
<!-- ╚══════════════════════════════╝ -->
<section id="location">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-eyebrow reveal">Find Us</div>
            <h2 class="section-title reveal">Our Location &amp; Contact</h2>
            <div class="divider mx-auto reveal"></div>
        </div>

        <div class="row g-4 align-items-stretch">

            <!-- Contact Details -->
            <div class="col-lg-4 reveal-left">
                <div class="contact-box">
                    <h4><i class="fa-solid fa-address-card me-2"></i>Get In Touch</h4>

                    <div class="contact-item">
                        <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="info">
                            <div class="label">Address</div>
                            <p>San Roque, City of Santo Tomas, Batangas, Philippines</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="info">
                            <div class="label">Phone</div>
                            <p>+63 (0) XXX-XXX-XXXX</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon"><i class="fa-solid fa-envelope"></i></div>
                        <div class="info">
                            <div class="label">Email</div>
                            <p><a href="mailto:vnfstotomas@gmail.com" style="color:inherit;">vnfstotomas@gmail.com</a></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon"><i class="fa-solid fa-clock"></i></div>
                        <div class="info">
                            <div class="label">Business Hours</div>
                            <p>Mon – Sat: 6:00 AM – 6:00 PM<br>Sun: 7:00 AM – 12:00 NN</p>
                        </div>
                    </div>

                    <hr style="border-color:rgba(255,255,255,.15); margin: 1.5rem 0;">

                    <h6 class="mb-3" style="font-weight:600;">Clients We Serve</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill" style="background:rgba(255,255,255,.12); font-size:.78rem; padding:.4rem .85rem;">Wet Markets</span>
                        <span class="badge rounded-pill" style="background:rgba(255,255,255,.12); font-size:.78rem; padding:.4rem .85rem;">Restaurants</span>
                        <span class="badge rounded-pill" style="background:rgba(255,255,255,.12); font-size:.78rem; padding:.4rem .85rem;">Fisheries</span>
                        <span class="badge rounded-pill" style="background:rgba(255,255,255,.12); font-size:.78rem; padding:.4rem .85rem;">Meat Suppliers</span>
                        <span class="badge rounded-pill" style="background:rgba(255,255,255,.12); font-size:.78rem; padding:.4rem .85rem;">Groceries</span>
                        <span class="badge rounded-pill" style="background:rgba(255,255,255,.12); font-size:.78rem; padding:.4rem .85rem;">Distributors</span>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="col-lg-8 reveal-right">
                <div class="map-wrap h-100">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3868.4!2d121.1491564!3d14.099026!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd055ad697d8d9%3A0x33315dba792757fe!2sV%20%26%20F%20Ice%20Plant%20%26%20Cold%20Storage%20Inc.!5e0!3m2!1sen!2sph!4v1741000000000!5m2!1sen!2sph"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="V&F Ice Plant & Cold Storage Inc. Location">
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ╔══════════════════════════════╗ -->
<!--   FOOTER                        -->
<!-- ╚══════════════════════════════╝ -->
<footer>
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-4">
                <div class="footer-brand">
                    <img src="{{ asset('logo.png') }}" alt="V&F Logo">
                    V&amp;F Ice Plant
                </div>
                <p class="footer-desc mt-2">
                    V&F Ice Plant and Cold Storage Inc. — your reliable partner for ice production, cold storage,
                    and temperature-controlled logistics in Santo Tomas, Batangas.
                </p>
                <div class="mt-3">
                    <a class="social-link" href="https://www.facebook.com/VnFBatIcePlantandColdStorage" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a class="social-link" href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a class="social-link" href="mailto:vnfstotomas@gmail.com" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                </div>
            </div>

            <div class="col-sm-6 col-lg-2 offset-lg-1">
                <div class="footer-col">
                    <h6>Quick Links</h6>
                    <ul>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#products">Products</a></li>
                        <li><a href="#why">Why Choose Us</a></li>
                        <li><a href="#location">Location</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="footer-col">
                    <h6>Services</h6>
                    <ul>
                        <li><a href="#services">Customer Ordering</a></li>
                        <li><a href="#services">Cold Storage Monitoring</a></li>
                        <li><a href="#services">GPS Delivery Tracking</a></li>
                        <li><a href="#services">Inventory Management</a></li>
                        <li><a href="#services">Billing &amp; Invoicing</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="footer-col">
                    <h6>Staff Portal</h6>
                    <ul>
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('attendance') }}">Employee Attendance</a></li>
                    </ul>
                    <h6 class="mt-3">Address</h6>
                    <p style="font-size:.82rem; color:rgba(255,255,255,.55); line-height:1.7;">
                        San Roque, City of<br>Santo Tomas, Batangas<br>Philippines
                    </p>
                </div>
            </div>

        </div>

        <hr class="footer-divider">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center footer-bottom">
            <p class="mb-0">&copy; {{ date('Y') }} V&amp;F Ice Plant and Cold Storage Inc. All rights reserved.</p>
            <p class="mb-0 mt-2 mt-md-0">San Roque, City of Santo Tomas, Batangas, Philippines</p>
        </div>
    </div>
</footer>

<!-- Scroll to Top -->
<button id="scrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <i class="fa-solid fa-chevron-up"></i>
</button>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ── Navbar scroll effect ──
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            nav.style.boxShadow = '0 4px 24px rgba(30,59,168,.35)';
        } else {
            nav.style.boxShadow = '0 2px 20px rgba(65,105,225,.25)';
        }
    });

    // ── Scroll to top button ──
    const scrollBtn = document.getElementById('scrollTop');
    window.addEventListener('scroll', () => {
        scrollBtn.style.display = window.scrollY > 400 ? 'flex' : 'none';
    });

    // ── Active nav link highlight ──
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(s => {
            if (window.scrollY >= s.offsetTop - 130) current = s.getAttribute('id');
        });
        navLinks.forEach(l => {
            l.style.color = l.getAttribute('href') === '#' + current
                ? 'rgba(255,255,255,1)'
                : '';
            l.style.fontWeight = l.getAttribute('href') === '#' + current ? '700' : '';
        });
    });

    // ── Scroll Reveal (Intersection Observer) ──
    const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    const observer  = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });
    revealEls.forEach(el => observer.observe(el));

    // ── Stagger section headers on load ──
    document.querySelectorAll('.section-eyebrow, .section-title, .divider').forEach((el, i) => {
        el.classList.add('reveal');
        el.style.transitionDelay = (i * 0.08) + 's';
    });

    // ── Drag-to-scroll for product cards ──
    const psw = document.getElementById('productsScrollWrapper');
    if (psw) {
        let isDown = false, startX, scrollLeft;
        psw.addEventListener('mousedown', e => {
            isDown = true;
            psw.classList.add('dragging');
            startX = e.pageX - psw.offsetLeft;
            scrollLeft = psw.scrollLeft;
        });
        psw.addEventListener('mouseleave', () => { isDown = false; psw.classList.remove('dragging'); });
        psw.addEventListener('mouseup',    () => { isDown = false; psw.classList.remove('dragging'); });
        psw.addEventListener('mousemove', e => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - psw.offsetLeft;
            psw.scrollLeft = scrollLeft - (x - startX) * 1.5;
        });
    }
</script>
</body>
</html>

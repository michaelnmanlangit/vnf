@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #3498db;
    }

    .stat-card h3 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .stat-card p {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <h3></h3>
            <p>Total Storage Units</p>
        </div>
        <div class="stat-card">
            <h3></h3>
            <p>Active Inventory Iteem</p>
        </div>
        <div class="stat-card">
            <h3></h3>
            <p>Active Deliveries</p>
        </div>
        <div class="stat-card">
            <h3></h3>
            <p>Total Employees</p>
        </div>
    </div>
@endsection

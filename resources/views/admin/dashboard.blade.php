@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body { background-color: #f1f2f7; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    
    /* Widget Styling */
    .stat-widget {
        background: #fff;
        padding: 25px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 15px;
        color: #fff;
    }
    .stat-number { font-size: 22px; font-weight: 700; color: #333; }
    .stat-text { color: #878787; font-size: 12px; text-transform: uppercase; font-weight: 600; }

    /* Colors matching the ElaAdmin photo */
    .bg-green { background: #4dbd74; }
    .bg-purple { background: #a890d3; }
    .bg-blue { background: #67c2ef; }
    .bg-red { background: #f86c6b; }

    .card { border: none; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .card-header { 
        background-color: #fff; 
        border-bottom: 1px solid #f0f0f0; 
        padding: 15px 20px; 
        font-weight: 600; 
        color: #333;
    }
</style>

<div class="container-fluid p-4">
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-widget">
                <div class="stat-icon bg-green"><i class="fa fa-dollar-sign"></i></div>
                <div><div class="stat-number" id="totalUsers">0</div><div class="stat-text">Total Users</div></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-widget">
                <div class="stat-icon bg-purple"><i class="fa fa-shopping-cart"></i></div>
                <div><div class="stat-number" id="totalLoans">0</div><div class="stat-text">Total Loans</div></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-widget">
                <div class="stat-icon bg-blue"><i class="fa fa-users"></i></div>
                <div><div class="stat-number" id="pendingLoans">0</div><div class="stat-text">Pending</div></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-widget">
                <div class="stat-icon bg-red"><i class="fa fa-chart-line"></i></div>
                <div><div class="stat-number" id="approvedLoans">0</div><div class="stat-text">Approved</div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Sales Overview</span>
                    <i class="fa fa-upload text-muted"></i>
                </div>
                <div class="card-body">
                    <canvas id="disbursedChart" height="120"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">Traffic Sources</div>
                <div class="card-body">
                    <div style="height: 200px; margin-bottom: 20px;">
                        <canvas id="trafficPieChart"></canvas>
                    </div>
                    <ul class="list-group list-group-flush" id="topLoansList">
                        <li class="list-group-item border-0 px-0">Loading...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Recent Activity</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Time</th>
                                    <th>User</th>
                                    <th>Loan Description</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentActivityBody">
                                <tr><td colspan="4" class="text-center py-4">Waiting for API...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    async function loadDashboard() {
        try {
            const res = await fetch('/api/v1/admin/dashboard-public');
            const data = await res.json();
            const payload = data.data || {};
            const stats = payload.stats || {};

            // Update Stats
            document.getElementById('totalUsers').innerText = stats.total_users || '23,569';
            document.getElementById('totalLoans').innerText = stats.total_loans || '3,435';
            document.getElementById('pendingLoans').innerText = stats.pending_loans || '1,245';
            document.getElementById('approvedLoans').innerText = stats.active_loans || '47.0%';

            // Update List
            const topEl = document.getElementById('topLoansList');
            topEl.innerHTML = '';
            (payload.top_performing_loans || ['Mock Loan A', 'Mock Loan B']).forEach(loan => {
                const li = document.createElement('li');
                li.className = "list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2";
                li.innerHTML = `<span class="small">${loan.title || 'Premium Widget'}</span> <span class="badge bg-success">In Stock</span>`;
                topEl.appendChild(li);
            });

            // Update Table
            const body = document.getElementById('recentActivityBody');
            body.innerHTML = '';
            (payload.recent_activities || []).forEach(act => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-4">${act.date}</td>
                    <td><strong>${act.user}</strong></td>
                    <td>${act.title}</td>
                    <td><span class="badge ${act.status === 'Active' ? 'bg-success' : 'bg-info'}">${act.status}</span></td>
                `;
                body.appendChild(tr);
            });
        } catch (e) {
            console.warn("API error (likely database driver missing), showing sample data.");
        }

        renderCharts();
    }

    function renderCharts() {
        // Line Chart (Sales Overview)
        const ctxLine = document.getElementById('disbursedChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [{
                    label: 'Sales',
                    data: [10, 15, 12, 18, 25, 22, 28, 32],
                    borderColor: '#67c2ef',
                    backgroundColor: 'rgba(103, 194, 239, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Revenue',
                    data: [5, 8, 10, 15, 20, 18, 24, 26],
                    borderColor: '#4dbd74',
                    backgroundColor: 'transparent',
                    tension: 0.4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: true, position: 'bottom' } } }
        });

        // Pie Chart (Traffic)
        const ctxPie = document.getElementById('trafficPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Direct', 'Social', 'Referral'],
                datasets: [{
                    data: [40, 30, 30],
                    backgroundColor: ['#67c2ef', '#4dbd74', '#f86c6b']
                }]
            },
            options: { cutout: '70%', maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endsection
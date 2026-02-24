@extends('admin.layout')
@section('title','Dashboard')
@section('content')
  <div class="card">
    <h2>Overview</h2>
    <div style="display:flex;gap:12px;margin-top:12px;flex-wrap:wrap">
      <div style="flex:1;min-width:160px;padding:12px;background:#f8fafc;border-radius:6px">Users<br><strong id="totalUsers">—</strong></div>
      <div style="flex:1;min-width:160px;padding:12px;background:#f8fafc;border-radius:6px">Loans<br><strong id="totalLoans">—</strong></div>
      <div style="flex:1;min-width:160px;padding:12px;background:#f8fafc;border-radius:6px">Pending<br><strong id="pendingLoans">—</strong></div>
      <div style="flex:1;min-width:160px;padding:12px;background:#f8fafc;border-radius:6px">Approved<br><strong id="approvedLoans">—</strong></div>
    </div>
    <div style="margin-top:18px;display:flex;gap:18px;flex-wrap:wrap">
      <div style="flex:2;min-width:320px;background:#fff;padding:12px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
        <h3 style="margin-top:0">Monthly Disbursed</h3>
        <canvas id="disbursedChart" style="max-height:300px"></canvas>
      </div>
      <div style="flex:1;min-width:260px;background:#fff;padding:12px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
        <h3 style="margin-top:0">Top Loans</h3>
        <ol id="topLoansList" style="padding-left:16px;margin:0">
          <li>Loading…</li>
        </ol>
      </div>
    </div>
  </div>

  <div style="height:18px"></div>

  <div style="display:flex;gap:18px;flex-wrap:wrap">
    <div style="flex:1;min-width:420px">
      <div class="card">
        <h3 style="margin-top:0">Recent Activity</h3>
        <table style="width:100%;border-collapse:collapse">
          <thead><tr><th style="text-align:left;padding:8px">Time</th><th style="text-align:left;padding:8px">Loan</th><th style="text-align:right;padding:8px">Amount</th><th style="text-align:left;padding:8px">Status</th></tr></thead>
          <tbody id="recentActivityBody">
            <tr><td colspan="4">Loading…</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div style="flex:1;min-width:300px">
      <div class="card">
        <h3 style="margin-top:0">Quick Actions</h3>
        <p><a href="/backend/users">Manage Users</a></p>
        <p><a href="/backend/loans">Manage Loans</a></p>
        <p><a href="/backend/financial">Financial Reports</a></p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    async function loadDashboard() {
      try {
        // use development public dashboard endpoint when available
        const res = await fetch('/api/v1/admin/dashboard-public', {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) {
          console.error('Failed to load dashboard:', res.status);
          return;
        }
        const data = await res.json();

        const payload = data.data || {};
        const stats = payload.stats || {};
        document.getElementById('totalUsers').innerText = stats.total_users ?? '0';
        document.getElementById('totalLoans').innerText = stats.total_loans ?? '0';
        document.getElementById('pendingLoans').innerText = stats.pending_loans ?? '0';
        document.getElementById('approvedLoans').innerText = stats.active_loans ?? '0';

        // Top loans
        const topEl = document.getElementById('topLoansList');
        topEl.innerHTML = '';
        const topLoans = payload.top_performing_loans || [];
        if (Array.isArray(topLoans) && topLoans.length) {
          topLoans.forEach(t => {
            const li = document.createElement('li');
            li.style.marginBottom = '8px';
            const idShort = (t.loan_id || '').toString().slice(0,8);
            li.innerHTML = `Loan #${idShort} — $${Number(t.amount||t.remaining_balance||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2})} <span style="color:#6b7280">(${t.status})</span>`;
            topEl.appendChild(li);
          });
        } else {
          topEl.innerHTML = '<li>No loans</li>';
        }

        // Recent activity
        const body = document.getElementById('recentActivityBody');
        body.innerHTML = '';
        const recent = payload.recent_activities || [];
        if (Array.isArray(recent) && recent.length) {
          recent.forEach(r => {
            const tr = document.createElement('tr');
            const timeTd = document.createElement('td');
            timeTd.style.padding='8px';
            // recent activities from repository include 'date' and 'description'
            timeTd.innerText = r.date ?? '';
            const idTd = document.createElement('td'); idTd.style.padding='8px'; idTd.innerText = r.user ?? '';
            const amtTd = document.createElement('td'); amtTd.style.padding='8px'; amtTd.style.textAlign='right'; amtTd.innerText = r.title ?? '';
            const statusTd = document.createElement('td'); statusTd.style.padding='8px'; statusTd.innerText = r.status ?? '';
            tr.appendChild(timeTd); tr.appendChild(idTd); tr.appendChild(amtTd); tr.appendChild(statusTd);
            body.appendChild(tr);
          });
        } else {
          body.innerHTML = '<tr><td colspan="4">No recent activity</td></tr>';
        }

        // Chart
        // try to fetch loan analytics for chart; fallback to empty
        let labels = [];
        let values = [];
        try {
          const analyticsRes = await fetch('/api/v1/admin/dashboard/loan-analytics?period=monthly');
          if (analyticsRes.ok) {
            const analyticsJson = await analyticsRes.json();
            const a = analyticsJson.data ?? [];
            labels = a.map(x => x.period);
            values = a.map(x => Number(x.total_amount || 0));
          }
        } catch (e) { /* ignore */ }
        const ctx = document.getElementById('disbursedChart').getContext('2d');
        if (window.disbursedChart) {
          window.disbursedChart.destroy();
        }
        window.disbursedChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Disbursed',
              data: values,
              backgroundColor: 'rgba(59,130,246,0.08)',
              borderColor: 'rgba(59,130,246,0.9)',
              tension: 0.25,
              fill: true
            }]
          },
          options: {responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
        });

      } catch (err) {
        console.error('Error loading dashboard', err);
      }
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', loadDashboard);
  </script>
@endsection


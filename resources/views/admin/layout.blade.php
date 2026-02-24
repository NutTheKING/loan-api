<!-- <!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - @yield('title')</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#f3f4f6}
    .wrap{display:flex;min-height:100vh}
    .sidebar{width:220px;background:#111827;color:#fff;padding:18px}
    .sidebar a{color:#cbd5e1;display:block;padding:8px 6px;text-decoration:none;border-radius:4px}
    .sidebar a:hover{background:#1f2937;color:#fff}
    .content{flex:1;padding:24px}
    .topbar{background:#fff;padding:8px 12px;border-bottom:1px solid #e6e7eb;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between}
    .profile-menu{position:relative;display:inline-block}
    .profile-btn{background:transparent;border:0;padding:6px 8px;cursor:pointer;border-radius:6px}
    .profile-dropdown{position:absolute;right:0;top:40px;background:#fff;border:1px solid #e6e7eb;border-radius:8px;min-width:220px;box-shadow:0 6px 18px rgba(0,0,0,0.08);padding:8px;z-index:40;display:none}
    .profile-dropdown a, .profile-dropdown button, .profile-dropdown label{display:block;padding:8px;border-radius:6px;text-decoration:none;color:#111827;background:transparent;border:0;text-align:left;width:100%}
    .profile-dropdown a:hover, .profile-dropdown button:hover{background:#f3f4f6}
    .profile-row{display:flex;align-items:center;gap:8px}
    .avatar{width:34px;height:34px;border-radius:50%;background:#ddd;display:inline-block}
    .lang-select{width:100%;padding:6px;border-radius:6px;border:1px solid #e6e7eb}
    .dark-mode-on{font-weight:700;color:#059669}
    body.dark{background:#0b1220;color:#d1d5db}
    body.dark .sidebar{background:#0f1724}
    body.dark .topbar{background:#071124;border-bottom-color:#0b1420}
    .card{background:#fff;padding:16px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.04)}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="sidebar">
      <h3 style="margin-top:0">Admin</h3>
      <a href="/backend/dashboard">Dashboard</a>
      <a href="/backend/customer-management">Customer Management</a>
      <a href="/backend/loans">Loan Management</a>
      <a href="/backend/repayment-management">Repayment Management</a>
      <a href="/backend/guarantor-management">Guarantor Management</a>
      <a href="/backend/finance">Accounting / Finance</a>
      <a href="/backend/reports">Reports</a>
      <a href="/backend/usermanagement">User & Role Management</a>
      <a href="/backend/config">System Settings</a>
      <form method="POST" action="/backend/logout" style="margin-top:12px">@csrf<button style="background:#ef4444;color:#fff;border:none;padding:8px;border-radius:6px;cursor:pointer">Logout</button></form>
    </div>
    <div class="content">
      <div class="topbar">
        <strong>@yield('title')</strong>
        <div class="profile-menu" id="profileMenu">
          <button class="profile-btn" id="profileBtn">
            <span class="profile-row"><span class="avatar" aria-hidden="true"></span><span style="margin-left:6px">{{ auth()->user()->name ?? 'Admin' }}</span></span>
          </button>
          <div class="profile-dropdown" id="profileDropdown" aria-hidden="true">
            <a href="/backend/profile">View Profile</a>
            <div style="padding:6px 8px">
              <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" id="darkModeToggle"> <span>Dark Mode</span></label>
            </div>
            <div style="padding:6px 8px">
              <label style="font-size:13px;margin-bottom:6px;display:block">Language</label>
              <select id="langSelect" class="lang-select">
                <option value="en">English</option>
                <option value="es">Español</option>
                <option value="fr">Français</option>
              </select>
            </div>
            <form id="logoutFormTop" method="POST" action="/backend/logout">@csrf<button type="submit" style="margin-top:6px;background:#ef4444;color:#fff;border:none;padding:8px;border-radius:6px;cursor:pointer;width:100%">Logout</button></form>
          </div>
        </div>
      </div>
      <div>@yield('content')</div>
    </div>
  </div>
  <script>
    (function(){
      const btn = document.getElementById('profileBtn');
      const dd = document.getElementById('profileDropdown');
      const menu = document.getElementById('profileMenu');
      const darkToggle = document.getElementById('darkModeToggle');
      const langSelect = document.getElementById('langSelect');

      function openDropdown(){ dd.style.display='block'; dd.setAttribute('aria-hidden','false'); }
      function closeDropdown(){ dd.style.display='none'; dd.setAttribute('aria-hidden','true'); }

      btn.addEventListener('click', function(e){ e.preventDefault(); if(dd.style.display==='block') closeDropdown(); else openDropdown(); });
      document.addEventListener('click', function(e){ if(!menu.contains(e.target)) closeDropdown(); });

      // Dark mode
      const saved = localStorage.getItem('admin_dark_mode');
      if(saved === '1'){ document.body.classList.add('dark'); if(darkToggle) darkToggle.checked = true; }
      if(darkToggle){ darkToggle.addEventListener('change', function(){ if(this.checked){ document.body.classList.add('dark'); localStorage.setItem('admin_dark_mode','1'); } else { document.body.classList.remove('dark'); localStorage.setItem('admin_dark_mode','0'); } }); }

      // Language
      const langSaved = localStorage.getItem('admin_lang') || 'en';
      if(langSelect){ langSelect.value = langSaved; langSelect.addEventListener('change', function(){ localStorage.setItem('admin_lang', this.value); location.reload(); }); }
    })();
  </script>
</body>
</html> -->

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Loan - @yield('title')</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    *{box-sizing:border-box}
    body{font-family:'Inter',sans-serif;margin:0;background:#f3f4f6}

    .wrap{display:flex;min-height:100vh}

    /* ===== SIDEBAR ===== */
    .sidebar{
      width:240px;
      background:#0f172a;
      color:#fff;
      padding:20px;
      position:fixed;
      top:0;
      left:0;
      bottom:0;
      overflow-y:auto;
    }

    .logo{
      display:flex;
      align-items:center;
      gap:10px;
      margin-bottom:30px;
    }

    .logo img{width:36px}
    .logo span{font-weight:700;font-size:18px}

    .sidebar a{
      color:#cbd5e1;
      display:block;
      padding:10px;
      text-decoration:none;
      border-radius:6px;
      font-size:14px;
      margin-bottom:6px;
      transition:0.2s;
    }

    .sidebar a:hover{
      background:#1e293b;
      color:#fff;
      padding-left:14px;
    }

    /* ===== CONTENT ===== */
    .content{
      margin-left:240px;
      flex:1;
      padding:24px;
    }

    /* ===== TOPBAR ===== */
    .topbar{
      background:#ffffff;
      padding:12px 20px;
      border-radius:12px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      box-shadow:0 4px 12px rgba(0,0,0,0.05);
      margin-bottom:20px;
    }

    .page-title{
      font-weight:600;
      font-size:18px;
    }

    /* ===== PROFILE ===== */
    .profile-menu{position:relative}
    .profile-btn{
      background:#f1f5f9;
      border:0;
      padding:6px 12px;
      border-radius:30px;
      cursor:pointer;
      display:flex;
      align-items:center;
      gap:10px;
      transition:0.2s;
    }

    .profile-btn:hover{background:#e2e8f0}

    .avatar{
      width:36px;
      height:36px;
      border-radius:50%;
      background:url('https://i.pravatar.cc/100') center/cover;
    }

    .profile-dropdown{
      position:absolute;
      right:0;
      top:55px;
      background:#fff;
      border-radius:12px;
      width:250px;
      box-shadow:0 10px 30px rgba(0,0,0,0.1);
      padding:15px;
      display:none;
      animation:fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn{
      from{opacity:0;transform:translateY(-10px)}
      to{opacity:1;transform:translateY(0)}
    }

    .profile-header{
      text-align:center;
      margin-bottom:12px;
    }

    .profile-header img{
      width:70px;
      height:70px;
      border-radius:50%;
      margin-bottom:8px;
    }

    .profile-header h4{
      margin:0;
      font-size:16px;
    }

    .profile-dropdown a,
    .profile-dropdown button{
      display:block;
      width:100%;
      padding:8px;
      border-radius:8px;
      border:0;
      background:none;
      text-align:left;
      text-decoration:none;
      color:#0f172a;
      margin-bottom:5px;
      cursor:pointer;
      font-size:14px;
    }

    .profile-dropdown a:hover,
    .profile-dropdown button:hover{
      background:#f1f5f9;
    }

    .logout-btn{
      background:#ef4444;
      color:#fff !important;
      text-align:center;
    }

    .card{
      background:#fff;
      padding:20px;
      border-radius:12px;
      box-shadow:0 3px 10px rgba(0,0,0,0.05);
    }

    /* ===== DARK MODE ===== */
    body.dark{background:#0b1220;color:#d1d5db}
    body.dark .topbar{background:#0f172a;color:#fff}
    body.dark .card{background:#1e293b}
    body.dark .profile-dropdown{background:#1e293b;color:#fff}
  </style>
</head>

<body>
<div class="wrap">

  <!-- ===== SIDEBAR ===== -->
  <div class="sidebar">
    <div class="logo">
      <img src="https://cdn-icons-png.flaticon.com/512/2830/2830284.png" alt="Loan Logo">
      <span>Loan System</span>
    </div>

    <a href="{{ url('/backend/dashboard') }}">Dashboard</a>
    <a href="{{ url('/backend/users') }}">Customer Management</a>
    <a href="{{ url('/backend/loans') }}">Loan Management</a>
    <a href="{{ url('/backend/loans') }}">Repayment Management</a>
    <a href="{{ url('/backend/users') }}">Guarantor Management</a>
    <a href="{{ url('/backend/financial') }}">Accounting / Finance</a>
    <a href="{{ url('/backend/notifications') }}">Notifications</a>
    <a href="{{ url('/backend/permissions') }}">User & Role Management</a>
    <a href="{{ url('/backend/config') }}">System Settings</a>
  </div>

  <!-- ===== CONTENT ===== -->
  <div class="content">
    <div class="topbar">
      <div class="page-title">@yield('title')</div>

      <div class="profile-menu" id="profileMenu">
        <button class="profile-btn" id="profileBtn">
          <div class="avatar"></div>
          <span>{{ auth()->user()->name ?? 'Admin' }}</span>
        </button>

        <div class="profile-dropdown" id="profileDropdown">
          <div class="profile-header">
            <img src="https://i.pravatar.cc/150" alt="">
            <h4>{{ auth()->user()->name ?? 'Admin' }}</h4>
            <small>{{ auth()->user()->email ?? 'admin@email.com' }}</small>
          </div>

          <a href="{{ url('/backend/profile') }}">View Profile</a>

          <label style="display:flex;align-items:center;gap:6px">
            <input type="checkbox" id="darkModeToggle"> Dark Mode
          </label>

          <form method="POST" action="{{ url('/backend/logout') }}">
            @csrf
            <button class="logout-btn">Logout</button>
          </form>
        </div>
      </div>
    </div>

    <div>@yield('content')</div>
  </div>

</div>

<script>
(function(){
  const btn = document.getElementById('profileBtn');
  const dd = document.getElementById('profileDropdown');
  const menu = document.getElementById('profileMenu');
  const darkToggle = document.getElementById('darkModeToggle');

  btn.addEventListener('click', function(e){
    e.preventDefault();
    dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', function(e){
    if(!menu.contains(e.target)) dd.style.display='none';
  });

  const saved = localStorage.getItem('loan_dark_mode');
  if(saved==='1'){ document.body.classList.add('dark'); if(darkToggle) darkToggle.checked=true; }

  if(darkToggle){
    darkToggle.addEventListener('change', function(){
      if(this.checked){
        document.body.classList.add('dark');
        localStorage.setItem('loan_dark_mode','1');
      }else{
        document.body.classList.remove('dark');
        localStorage.setItem('loan_dark_mode','0');
      }
    });
  }
})();
</script>

</body>
</html>

<!doctype html>
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
      <a href="/backend/users">User Management</a>
      <a href="/backend/loans">Loan Management</a>
      <a href="/backend/financial">Financial</a>
      <a href="/backend/notifications">Notifications</a>
      <a href="/backend/permissions">Permissions</a>
      <a href="/backend/config">Configuration</a>
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
</html>

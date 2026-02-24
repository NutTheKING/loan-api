<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f3f4f6}
    .card{max-width:420px;margin:80px auto;padding:24px;background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.06)}
    .field{margin-bottom:12px}
    label{display:block;font-size:13px;margin-bottom:6px}
    input{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px}
    button{background:#1e88e5;color:#fff;border:none;padding:10px 14px;border-radius:6px;cursor:pointer}
    .error{color:#b91c1c;font-size:13px;margin-bottom:8px}
  </style>
</head>
<body>
  <div class="card">
    <h2>Admin Login</h2>
    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ url('/backend/login') }}">
      @csrf
      <div class="field">
        <label for="identifier">Username or Email</label>
        <input id="identifier" name="identifier" value="{{ old('identifier') }}" />
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" />
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center">
        <button type="submit">Sign in</button>
        <a href="/">Back</a>
      </div>
    </form>
  </div>
</body>
</html>

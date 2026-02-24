@extends('admin.layout')
@section('title','User Management')
@section('content')
  <div class="card">
    <h2>Users</h2>
    <table style="width:100%;border-collapse:collapse;margin-top:12px">
      <thead><tr><th style="text-align:left;padding:8px">ID</th><th style="text-align:left;padding:8px">Name</th><th style="text-align:left;padding:8px">Email</th><th style="text-align:left;padding:8px">Joined</th></tr></thead>
      <tbody>
        @foreach($users as $u)
          <tr><td style="padding:8px">{{ $u->id }}</td><td style="padding:8px">{{ $u->name }}</td><td style="padding:8px">{{ $u->email }}</td><td style="padding:8px">{{ $u->created_at->toDateString() }}</td></tr>
        @endforeach
      </tbody>
    </table>
    <div style="margin-top:12px">{{ $users->links() }}</div>
  </div>
@endsection

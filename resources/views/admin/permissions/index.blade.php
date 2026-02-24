@extends('admin.layout')
@section('title','Permissions')
@section('content')
  <div class="card">
    <h2>Roles & Permissions</h2>
    <ul>
      @foreach($roles as $r)
        <li>{{ $r->name }} ({{ $r->slug ?? '' }})</li>
      @endforeach
    </ul>
  </div>
@endsection

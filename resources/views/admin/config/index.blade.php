@extends('admin.layout')
@section('title','Configuration')
@section('content')
  <div class="card">
    <h2>Configuration</h2>
    <p>App Name: {{ $appName }}</p>
    <p>Environment: {{ $env }}</p>
  </div>
@endsection

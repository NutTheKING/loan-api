@extends('admin.layout')
@section('title','Financial')
@section('content')
  <div class="card">
    <h2>Financial Overview</h2>
    <p>Total Disbursed: <strong>{{ number_format($totalDisbursed,2) }}</strong></p>
    <p>Total Repaid: <strong>{{ number_format($totalRepaid,2) }}</strong></p>
  </div>
@endsection

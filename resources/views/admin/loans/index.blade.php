@extends('admin.layout')
@section('title','Loan Management')
@section('content')
  <div class="card">
    <h2>Loans</h2>
    <table style="width:100%;border-collapse:collapse;margin-top:12px">
      <thead><tr><th style="text-align:left;padding:8px">ID</th><th style="text-align:left;padding:8px">Amount</th><th style="text-align:left;padding:8px">Term</th><th style="text-align:left;padding:8px">Status</th></tr></thead>
      <tbody>
        @foreach($loans as $l)
          <tr><td style="padding:8px">{{ $l->id }}</td><td style="padding:8px">{{ $l->amount }}</td><td style="padding:8px">{{ $l->term }}</td><td style="padding:8px">{{ $l->status }}</td></tr>
        @endforeach
      </tbody>
    </table>
    <div style="margin-top:12px">{{ $loans->links() }}</div>
  </div>
@endsection

@extends('admin.layout')
@section('title', 'Create User & Permissions')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .permission-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #495057;
        margin-bottom: 1rem;
        display: block;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }
    .form-check-label { font-size: 0.9rem; color: #555; }
    .card { border: none; }
</style>

<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-user-plus me-2"></i>Create New System User</h5>
                </div>
                <div class="card-body">
                    <form action="/backend/users/store" method="POST">
                        @csrf
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Default Password</label>
                                <input type="text" name="password" class="form-control" value="Loan12345!" readonly>
                                <small class="text-muted">User will be prompted to change this.</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-4 text-secondary uppercase small fw-bold"><i class="fas fa-shield-alt me-2"></i>Access Control & Permissions</h6>
                        
                        <div class="row g-4">
                            <div class="col-md-3">
                                <span class="permission-section-title"><i class="fa fa-users-cog me-2"></i>User Management</span>
                                @foreach(['user.create' => 'Create User', 'user.update' => 'Update User', 'user.delete' => 'Delete User', 'role.assign' => 'Assign Roles'] as $val => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $val }}" id="{{ $val }}">
                                    <label class="form-check-label" for="{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>

                            <div class="col-md-3">
                                <span class="permission-section-title"><i class="fa fa-university me-2 text-success"></i>Loan Ops</span>
                                @foreach(['loan.create' => 'Create Loan', 'loan.approve' => 'Approve/Reject', 'loan.disburse' => 'Disburse Funds', 'loan.view_all' => 'View All Loans'] as $val => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $val }}" id="{{ $val }}">
                                    <label class="form-check-label" for="{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>

                            <div class="col-md-3">
                                <span class="permission-section-title"><i class="fa fa-credit-card me-2 text-info"></i>Finance</span>
                                @foreach(['payment.record' => 'Record Payment', 'payment.refund' => 'Process Refund', 'report.view' => 'View Reports', 'report.export' => 'Export Data'] as $val => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $val }}" id="{{ $val }}">
                                    <label class="form-check-label" for="{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>

                            <div class="col-md-3">
                                <span class="permission-section-title"><i class="fa fa-mobile-alt me-2 text-danger"></i>System & Mobile</span>
                                @foreach(['settings.update' => 'System Settings', 'audit.view' => 'Audit Logs', 'mobile.access' => 'App Login Access', 'docs.upload' => 'Document Management'] as $val => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $val }}" id="{{ $val }}">
                                    <label class="form-check-label" for="{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                            <p class="text-muted small mb-0"><i class="fa fa-info-circle me-1"></i> Permissions will take effect immediately after user login.</p>
                            <div>
                                <button type="reset" class="btn btn-light me-2 border">Clear All</button>
                                <button type="submit" class="btn btn-primary px-5 fw-bold">Create User & Save Permissions</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
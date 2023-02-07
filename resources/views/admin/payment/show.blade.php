@extends('layouts.backend.app')

@section('title','Payment')

@push('css')

@endpush

@section('content')
    <div class="container-fluid">
        <!-- Vertical Layout | With Floating Label -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2 style="color: #FF9494; font-weight: 900">
                            PAYMENT
                        </h2>
                    </div>
                    <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Doctor Name</label>
                                        <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $payment->consultation->userDoctorDetail->user->name }}"  disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group label-floating">
                                        <label class="control-label">User Name</label>
                                        <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $payment->consultation->user->name }}"  disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Consultation Date</label>
                                        <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $payment->consultation->created_at }}"  disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Bank Name</label>
                                        <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $payment->bank_name }}"  disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Sender Name</label>
                                        <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $payment->sender_name }}"  disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label">Image</label>
                                </div>
                                <div class="col-md-6">
                                    <img src="{{ asset('uploads/payments/'.$payment->image) }}" style="height: 200px; width: 200px;">
                                </div>
                            </div>

                            @if($payment->consultation->status == "Ditolak" || $payment->consultation->status == "Menunggu Konfirmasi Pembayaran" )
                                
                                <form id="status-form-{{ $payment->id }}" method="post" action="{{ route('admin.payment.status',$payment->id ) }}"
                                    style="display: none;">
                                    @csrf
                                </form>
                                <button type="button" class="btn btn-success btn-sm" name="action" value="accept"
                                    onclick="if(confirm('Are you sure?')){
                                    event.preventDefault();
                                    document.getElementById('status-form-{{ $payment->id }}').submit();
                                    }else {
                                        event.preventDefault();
                                    }">Accept</button>
                                
                                <form id="status-deny-form-{{ $payment->id }}" method="post" action="{{ route('admin.payment.status_deny',$payment->id ) }}"
                                    style="display: none;">
                                    @csrf
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" name="action" value="deny"
                                    onclick="if(confirm('Are you sure?')){
                                    event.preventDefault();
                                    document.getElementById('status-deny-form-{{ $payment->id }}').submit();
                                    }else {
                                        event.preventDefault();
                                    }">Deny</button>
                            @else
                                <a href="{{ route('author.payment.index') }}" class="btn btn-danger">Back</a>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
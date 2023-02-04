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
                        <h2>
                            PAYMENT
                        </h2>
                    </div>
                    <div class="body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Doctor Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ $payment->consultation->userDoctorDetail->user->name }}"  disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group label-floating">
                                        <label class="control-label">User Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ $payment->consultation->user->name }}"  disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Consultation Date</label>
                                        <input type="text" class="form-control" name="name" value="{{ $payment->consultation->created_at }}"  disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Bank Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ $payment->bank_name }}"  disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Sender Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ $payment->sender_name }}"  disabled>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Image</label>
                                </div>
                                <div class="col-md-12">
                                    <img src="{{ asset('uploads/payments/'.$payment->image) }}" style="height: 200px; width: 200px;">
                                </div>
                            </div>

                            <a href="{{ route('author.payment.index') }}" class="btn btn-danger">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
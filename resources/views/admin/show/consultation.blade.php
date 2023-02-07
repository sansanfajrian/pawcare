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
                        Doctor
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                    <div class="col-md-6">
                            <div class="form-group label-floating">
                                <label class="control-label">Doctor Name</label>
                                <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $data['user_doctor_detail']['user']['name'] }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group label-floating">
                                <label class="control-label">Patient Name</label>
                                <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $data['user']['name'] }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group label-floating">
                                <label class="control-label">Status</label>
                                <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $data['status'] }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group label-floating">
                                <label class="control-label">Price</label>
                                <input type="text" class="form-control style-label-pawcare" name="name" value="{{ $data['user_doctor_detail']['price'] }}" disabled>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('admin.consultations.index') }}" class="btn btn-danger">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush
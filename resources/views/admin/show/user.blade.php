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
                        User
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $data->name }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label">email</label>
                                <input type="email" class="form-control" name="name" value="{{ $data->email }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label">phone</label>
                                <input type="text" class="form-control" name="name" value="{{ $data->phone }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label">Address</label>
                                <input type="text" class="form-control" name="name" value="{{ $data->address }}" disabled>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('admin.users.index') }}" class="btn btn-danger">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush
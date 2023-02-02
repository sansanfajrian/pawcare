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
                        Review
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $data['consultation']['user']['name'] }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label">Review</label>
                                <input type="text" class="form-control" name="name" value="{{ $data['review'] }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label">Star</label>
                                <input type="text" class="form-control" name="name" value="{{ $data['star'] }}" disabled>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('author.reviews.index') }}" class="btn btn-danger">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush
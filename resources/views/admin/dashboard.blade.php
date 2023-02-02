@extends('layouts.backend.app')

@section('title','Dashboard')

@push('css')

@endpush

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>DASHBOARD</h2>
    </div>

    <!-- Widgets -->
    <div class="row clearfix">

    </div>
    <!-- #END# Widgets -->
    <!-- Widgets -->
    <div class="row clearfix">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="info-box bg-purple hover-zoom-effect">
                <div class="icon">
                    <i class="material-icons">account_circle</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL AUTHOR</div>
                    <div class="number count-to" data-from="0" data-to="{{ $author_count }}" data-speed="15" data-fresh-interval="20"></div>
                </div>
            </div>
            <div class="info-box bg-deep-purple hover-zoom-effect">
                <div class="icon">
                    <i class="material-icons">fiber_new</i>
                </div>
                <div class="content">
                    <div class="text">TODAY AUTHOR</div>
                    <div class="number count-to" data-from="0" data-to="{{ $new_authors_today }}" data-speed="15" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-9">

        </div>
    </div>
    <!-- #END# Widgets -->

    <div class="row clearfix">
        <!-- Task Info -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

        </div>
        <!-- #END# Task Info -->
    </div>
</div>

<!-- mt-5 doesn't work, so sorry... so i margin the container manually to create space -->
<div class="container-fluid" style="margin-top: 10px;">
    <h1>List Doctors</h1>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Price</th>
                <th scope="col">Phone</th>
                <th scope="col">Description</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($doctors as $index => $item)
            <tr>
                <th scope="row">{{$index + 1}}</th>
                <td>{{$item['name']}}</td>
                <td>{{$item['price']}}</td>
                <td>{{$item['phone']}}</td>
                <td>{{$item['description']}}</td>
                <td>
                    <span class="badge badge-pill badge-primary">
                        {{$item['status'] == 0 ? 'Not Approved' : 'Approved'}}
                    </span>
                </td>
                <td class="text-center">
                    <a href="{{ route('admin.dashboard-show-doctor',$item['id']) }}" class="btn btn-info waves-effect">
                        <i class="material-icons">details</i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- mt-5 doesn't work, so sorry... so i margin the container manually to create space -->
<div class="container-fluid" style="margin-top: 10px;">
    <h1>List Users</h1>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">created_at</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $index => $item)
            <tr>
                <th scope="row">{{$index + 1}}</th>
                <td>{{$item['name']}}</td>
                <td>{{$item['price']}}</td>
                <td>{{$item['phone']}}</td>
                <td>{{$item['created_at']}}</td>
                <td class="text-center">
                    <a href="{{ route('admin.dashboard-show-user',$item['id']) }}" class="btn btn-info waves-effect">
                        <i class="material-icons">details</i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- mt-5 doesn't work, so sorry... so i margin the container manually to create space -->
<div class="container-fluid" style="margin-top: 10px;">
    <h1>List Consultations</h1>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Patient Name</th>
                <th scope="col">Doctor Name</th>
                <th scope="col">Gender</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($consultations as $index => $item)
            <tr>
                <th scope="row">{{$index + 1}}</th>
                <td>{{$item['patient_name']}}</td>
                <td>{{$item['doctor_name']}}</td>
                <td>{{$item['gender']}}</td>
                <td>
                    <span class="badge badge-pill badge-primary">
                        {{$item['status'] }}
                    </span>
                </td>
                <td class="text-center">
                    <a href="{{ route('admin.dashboard-show-consultation',$item['id']) }}" class="btn btn-info waves-effect">
                        <i class="material-icons">details</i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('js')
<!-- Jquery CountTo Plugin Js -->
<script src="{{ asset('assets/backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>

<!-- Morris Plugin Js -->
<script src="{{ asset('assets/backend/plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/morrisjs/morris.js') }}"></script>

<!-- ChartJs -->
<script src="{{ asset('assets/backend/plugins/chartjs/Chart.bundle.js') }}"></script>

<!-- Flot Charts Plugin Js -->
<script src="assets/backend/plugins/flot-charts/jquery.flot.js"></script>
<script src="assets/backend/plugins/flot-charts/jquery.flot.resize.js"></script>
<script src="assets/backend/plugins/flot-charts/jquery.flot.pie.js"></script>
<script src="assets/backend/plugins/flot-charts/jquery.flot.categories.js"></script>
<script src="assets/backend/plugins/flot-charts/jquery.flot.time.js"></script>

<!-- Sparkline Chart Plugin Js -->
<script src="assets/backend/plugins/jquery-sparkline/jquery.sparkline.js"></script>
<script src="{{ asset('assets/backend/js/pages/index.js') }}"></script>
@endpush
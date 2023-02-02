@extends('layouts.backend.app')

@section('title','Dashboard')

@push('css')

@endpush

@section('content')
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
            @foreach ($data as $index => $item)
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
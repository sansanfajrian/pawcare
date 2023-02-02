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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- #END# Widgets -->


    <div class="row clearfix">
        <!-- Task Info -->
        <h1>List Reviews</h1>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Patient Name</th>
                    <th scope="col">Star</th>
                    <th scope="col">Review</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reviews as $index => $item)
                <tr>
                    <th scope="row">{{$index + 1}}</th>
                    <td>{{$item['name']}}</td>
                    <td>{{$item['star']}}</td>
                    <td>{{$item['review']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- #END# Task Info -->

    </div>
</div>


@endsection

@push('js')
<!-- Jquery CountTo Plugin Js -->
<script src="{{ asset('assets/backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>
<script src="{{ asset('assets/backend/js/pages/index.js') }}"></script>
@endpush
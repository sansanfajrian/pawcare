@extends('layouts.backend.app')

@section('title','Dashboard')

@push('css')

@endpush

@section('content')
<div class="container-fluid">
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
                @foreach ($data as $index => $item)
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
                        <a href="{{ route('author.dashboard-show-consultation',$item['id']) }}" class="btn btn-info waves-effect">
                            <i class="material-icons">details</i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('js')
<!-- Jquery CountTo Plugin Js -->
<script src="{{ asset('assets/backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>
<script src="{{ asset('assets/backend/js/pages/index.js') }}"></script>
@endpush
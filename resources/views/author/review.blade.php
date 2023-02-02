@extends('layouts.backend.app')

@section('title','Dashboard')

@push('css')

@endpush

@section('content')
<div class="container-fluid">
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
            @foreach ($data as $index => $item)
            <tr>
                <th scope="row">{{$index + 1}}</th>
                <td>{{$item['name']}}</td>
                <td>{{$item['star']}}</td>
                <td>{{$item['review']}}</td>
                <td class="text-center">
                    <a href="{{ route('author.dashboard-show-review',$item['id']) }}" class="btn btn-info waves-effect">
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
<script src="{{ asset('assets/backend/js/pages/index.js') }}"></script>
@endpush
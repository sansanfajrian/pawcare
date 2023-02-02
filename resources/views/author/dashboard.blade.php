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
   
    <!-- #END# Widgets -->


    <div class="row clearfix">
        <!-- Task Info -->
        <!-- #END# Task Info -->

    </div>
</div>


@endsection

@push('js')
<!-- Jquery CountTo Plugin Js -->
<script src="{{ asset('assets/backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>
<script src="{{ asset('assets/backend/js/pages/index.js') }}"></script>
@endpush
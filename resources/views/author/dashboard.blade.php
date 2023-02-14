@extends('layouts.backend.app')

@section('title','Dashboard')

@push('css')

@endpush

@section('content')
<div class="container-fluid">
    <div class="block-header" style="height: 100vh;">
    <div style="display: flex; margin-bottom: 1rem;">
    <div style=" 
    padding: 1rem;
    background-color: #ffffff;
    border: solid #fb483a;
    margin-top: 3rem;
    margin-right: 2rem;
    ">
        <h2 style="color: #fb483a !important;">Jumlah Konsultasi : </h2> 
        <h2 style="color: #000000 !important; font-weight: 900; margin-top: 1rem !important">{{ $consultation_count }} User </h2>
    </div>

    <div style=" 
    padding: 1rem;
    background-color: #ffffff;
    border: solid #fb483a;
    margin-top: 3rem;">
        <h2 style="color: #fb483a !important;">Jumlah Ulasan :</h2> 
        <h2 style="color: #000000 !important; font-weight: 900; margin-top: 1rem !important">{{$review_count}}  Ulasan</h2>
    </div>
    </div>
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
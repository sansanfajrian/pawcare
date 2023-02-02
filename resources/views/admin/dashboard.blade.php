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
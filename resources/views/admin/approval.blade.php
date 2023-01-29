@extends('layouts.backend.app')

@section('title','Authors')

@push('css')
<!-- JQuery DataTable Css -->
<link href="{{ asset('assets/backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <table class="table">
        <thead>
            <th>Name</th>
            <th>Email</th>
            <th>Price</th>
            <th>Description</th>
            <th>Registration Date</th>
            <!-- <th>Status</th> -->
            <th>Action</th>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item['requester']['name'] }}</td>
                <td>{{ $item['requester']['email'] }}</td>
                <td>{{ $item['requester']['user_doctor_details']['price'] }}</td>
                <td>{{ $item['requester']['user_doctor_details']['description'] }}</td>
                <td>{{ $item['created_at'] }}</td>
                <!-- <td>
                    <span class="badge badge-pill badge-secondary">
                        @convertStatus({$item['status']})
                    </span>
                </td> -->
                <td>
                    <form action={{ route('admin.approvals.approve') }} method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="doctor_id" value={{ $item['id'] }}>
                        <input type="hidden" name="approval_id" value={{ $item['requester']['user_doctor_details']['id'] }}>
                        <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('js')
<!-- Jquery DataTable Plugin Js -->
<script src="{{ asset('assets/backend/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.flash.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/jszip.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.print.min.js') }}"></script>

<script src="{{ asset('assets/backend/js/pages/tables/jquery-datatable.js') }}"></script>
<script src="https://unpkg.com/sweetalert2@7.19.1/dist/sweetalert2.all.js"></script>
<script type="text/javascript">
    function deleteAuthors(id) {
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                event.preventDefault();
                document.getElementById('delete-form-' + id).submit();
            } else if (
                // Read more about handling dismissals
                result.dismiss === swal.DismissReason.cancel
            ) {
                swal(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                )
            }
        })
    }
</script>
@endpush
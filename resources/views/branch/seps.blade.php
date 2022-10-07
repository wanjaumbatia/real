@extends('layouts.branch')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Sales Executives
                </div>
                <div class="card-body">
                    <table id="table" class="table table striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created On</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seps as $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
                                <td><a href="#" class="btn btn-primary btn-sm">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection
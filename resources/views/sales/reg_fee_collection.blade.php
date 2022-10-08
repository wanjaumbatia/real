@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Registration Commision</div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="">Number of Customer</label>
                                <input type="text" class="form-control" value="{{count($data)}}" disabled>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="">Total Registration Commission</label>
                                <input type="text" class="form-control" value="{{number_format(count($data)*250)}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table id="table" class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Remarks</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->customer_name}}</td>
                                <td>{{number_format(250,2)}}</td>
                                <td>{{$item->status}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
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
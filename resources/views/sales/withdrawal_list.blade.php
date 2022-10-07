@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <table id='table' class="table table-striped">
                <thead>
                    <tr>
                        <th>Posting Date</th>
                        <th>Customer</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->customer_name}}</td>
                        <td>{{$item->plan}}</td>
                        <td>{{$item->amount}}</td>
                        <td>{{$item->status}}</td>
                        <td>{{$item->remarks}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
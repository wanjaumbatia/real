@extends('layouts.branch')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Approved Loan
                </div>
                <div class="card-body">
                    <table id='table' class="table table-striped mt-2">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Appliaction Date</th>
                                <th>Amount</th>
                                <th>Interest</th>
                                <th>Duration</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td>{{$item->application_date}}</td>
                                <td>{{number_format($item->amount, 2)}}</td>
                                <td>{{$item->interest_percentage}} %</td>
                                <td>{{$item->duration}} Months</td>
                                <td><a href="/branch_loan/{{$item->id}}" class="btn btn-primary btn-block">Open</a></td>
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
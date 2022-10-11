@extends('layouts.branch')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Loans for {{$branch}}
                </div>
                <div class="card-body">
                    <form method="get" action="/branch_loans">
                        <div class="row w-100">
                            <div class="col-9">
                                <select class="form-control w-100" name="status">
                                    <option value="all">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="approved">Approved</option>
                                    <option value="running">Running</option>
                                    <option value="bad_loan">Bad Loan</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <button class="btn btn-primary w-100">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr />
                    <table id='table' class="table table-striped mt-2">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Appliaction Date</th>
                                <th>Amount</th>
                                <th>Interest</th>
                                <th>Duration</th>
                                @if($status=='running')
                                <th>Paid Amount</th>
                                @endif
                                <th>Status</th>
                                @if($status=='pending')
                                <th>Currenct Savings</th>
                                @endif
                                @if($status=='pending')
                                <th>Balance</th>
                                <th>Action</th>
                                
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $item)
                            <tr>
                                <td>{{$item->customer}}</td>
                                <td>{{$item->application_date}}</td>
                                <td>{{number_format($item->loane_amount, 2)}}</td>
                                <td>{{$item->percentage}} %</td>
                                <td>{{$item->duration}} Months</td>
                                <td>{{number_format($item->total_balance, 0)}}</td>
                                <td><a href="/branch_loan/{{$item->id}}" class="btn btn-primary btn-sm">View</a></td>
                                
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
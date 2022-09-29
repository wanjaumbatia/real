@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Loans
                </div>
                <div class="card-body">
                    <form method="get" action="/loans">
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
                                    Search
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
                                @if($status=='running')
                                <td>{{number_format($item->paid, 2)}}</td>
                                @endif
                                <td>
                                    @if($item->status=='running')
                                    <span class="btn btn-success p-1">Running</span>
                                    @endif
                                    @if($item->status=='pending')
                                    <span class="btn btn-info p-1 text-white">Pending</span>
                                    @endif
                                    @if($item->status=='processing')
                                    <span class="btn btn-warning p-1">Processing</span>
                                    @endif
                                    @if($item->status=='processing')
                                    <span class="btn btn-default p-1">Approved</span>
                                    @endif
                                    @if($item->status=='bad_loan')
                                    <span class="btn btn-danger p-1">Bad Loan</span>
                                    @endif
                                </td>
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
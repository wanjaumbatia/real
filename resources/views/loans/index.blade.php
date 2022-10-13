@extends('layouts.loan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Loans
                </div>
                <div class="card-body">
                    <form method="get" action="/loans/index">
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
                    <div class="table-responsive">
                        <table id='table' class="table table-striped mt-2">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Start Date</th>
                                    <th>Exit Date</th>
                                    <th>Amount</th>
                                    <th>Interest</th>
                                    <th>Duration</th>
                                    <th>Paid Amount</th>
                                    <th>Status</th>
                                    <th>Branch</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $item)
                                <tr>
                                    <td>{{$item->customer}}</td>
                                    <td>{{date('d-m-Y', strtotime($item->start_date))}}</td>
                                    <td>{{date('d-m-Y', strtotime($item->exit_date))}}</td>
                                    <td>{{number_format($item->loan_amount)}}</td>
                                    <td>{{$item->percentage}} %</td>
                                    <td>{{$item->duration}} Months</td>
                                    <td>{{number_format($item->total_balance,0)}}</td>
                                    @if($item->loan_status == 'BAD')
                                    <td>
                                        <p class="text-danger font-weight-bold">{{$item->loan_status}}</p>
                                    </td>
                                    @elseif(($item->loan_status == 'EXPIRED'))
                                    <td>
                                        <p class="font-weight-bold" style="color: #f5b942">{{$item->loan_status}}</p>
                                    </td>
                                    @else
                                    <td>
                                        <p class="text-primary font-weight-bold">{{$item->loan_status}}</p>
                                    </td>
                                    @endif
                                    <td>{{$item->branch}}</td>
                                    <td><a href="/loan_card/{{$item->id}}" class="btn btn-primary btn-block">Open</a></td>
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
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ],
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection
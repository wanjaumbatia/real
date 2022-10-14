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

                    <table id='table' class="table table-striped mt-2">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                @if($status == 'active')
                                <th>Start date</th>
                                <th>Exit date</th>
                                @endif
                                <th>Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance</th>
                                <th>Savings</th>
                                <th>Status</th>
                                <th>Countdown</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $item)
                            <tr>
                                <td>{{$item->customer}}</td>
                                @if($status == 'active')
                                <td>{{date('d-m-Y', strtotime($item->start_date))}}</td>
                                <td>{{date('d-m-Y', strtotime($item->exit_date))}}</td>
                                @endif
                                <td>{{number_format($item->loan_amount,0)}}</td>
                                <td>{{number_format($item->total_amount_paid, 0)}}</td>
                                <td>{{number_format($item->total_balance, 0)}}</td>
                                <td>{{number_format($item->savings)}}</td>
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
                                <td>
                                    @if($item->countdown <0) <p class="text-danger">{{$item->countdown}}</p>
                                        @else
                                        <p class="text-success">{{$item->countdown}}</p>
                                        @endif
                                </td>
                                <td><a href="/loan_status/{{$item->id}}" class="btn btn-sm btn-primary">open</a></td>
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
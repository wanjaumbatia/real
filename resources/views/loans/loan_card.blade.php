@extends('layouts.loan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            {{$customer->name}}
                        </div>
                        <div class="col-6 text-end">
                            <a href='/change_status/{{$loan->id}}' class="btn btn-primary btn-sm mx-1">Change Status</a>
                            <!-- <a href='/customer/{{$customer->id}}' class="btn btn-primary btn-sm mx-1">Customer Details</a> -->
                            <a href='#' class="btn btn-primary btn-sm mx-1">Close</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Customer Name</label>
                                    <input type="text" class="form-control" value="{{$customer->name}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Phone Number</label>
                                    <input type="text" class="form-control" value="{{$customer->phone}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Address</label>
                                    <input type="text" class="form-control" value="{{$customer->address}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Handler</label>
                                    <input type="text" class="form-control" value="{{$customer->handler}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Loan Amount</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->loan_amount, 0)}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Interest</label>
                                    <input type="text" class="form-control" value="{{$loan->percentage}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Duration</label>
                                    <input type="text" class="form-control" value="{{$loan->duration}} Months" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Balance</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->total_balance)}}" disabled>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Loan Charges</div>
                <div class="card-body">
                    <table class="table">
                        @foreach($deductions as $item)
                        <tr>
                            <td style="font-weight: 600;">{{$item['name']}}</td>
                            <td>{{number_format($item['amount'])}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Repayment Details</div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="">Expected Total Capital</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->loan_amount, 2)}}" disabled />
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="">Total Paid Capital</label>
                                <input type="text" class="form-control" value="{{number_format(($loan->total_amount_paid - $loan->total_interest_paid), 2)}}" disabled />
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="">Expected Total Interest</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->total_interest, 2)}}" disabled />
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="">Total Paid Interest</label>
                                <input type="text" class="form-control" value="{{number_format($loan->total_interest_paid, 2)}}" disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="">Expected Monthly Capital Repayment</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->monthly_principle, 2)}}" disabled />
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="">Paid Monthly Capital Repayment</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->monthly_principle_paid, 2)}}" disabled />
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="">Expected Monthly Interest Payment</label>
                                <input type="text" class="form-control" value="{{number_format($loan->monthly_interest, 2)}}" disabled />
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="">Paid Monthly Interest Payment</label>
                                <input type="text" class="form-control" value="{{number_format($loan->monthly_interest_paid, 2)}}" disabled />
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Loan Statement</div>
                <div class="card-body">
                    <table id="table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Remarks</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $item)
                            <tr>
                                <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
                                <td>{{$item->description}}</td>
                                <td>{{$item->amount}}</td>
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
            'info': false
        });
    });
</script>

@endsection
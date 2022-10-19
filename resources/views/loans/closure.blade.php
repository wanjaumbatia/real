@extends('layouts.loan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            Loan Closure
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

            <div class="accordion mt-3" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Repayment Details
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="card mt-3">
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

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseTwo">
                            Loan Statement
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="card mt-3">
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

            <div class="card mt-3">
                <div class="card-header">Close Loan</div>
                <div class="card-body">
                    <form action="/close_loan/{{$loan->id}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="">Close Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="Normal">Normal</option>
                                <option value="Forced">Forced</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-1">Close</button>
                    </form>
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
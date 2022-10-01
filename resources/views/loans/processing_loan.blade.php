@extends('layouts.loan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{$customer->name}}
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

                        <div class="row mt-1">
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

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Loan Amount</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->amount, 0)}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Interest</label>
                                    <input type="text" class="form-control" value="{{$loan->interest_percentage}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Duration</label>
                                    <input type="text" class="form-control" value="{{$loan->duration}} Months" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Current Savings</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->current_savings, 2)}}" disabled>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card mt-3">
                        <div class="card-header">Loan Charges</div>
                        <div class="card-body">
                            <table class="table">
                                @foreach($deductions as $item)
                                <tr>
                                    <td style="font-weight: 600;">{{$item['name']}}</td>
                                    <td>{{number_format($item['amount'], 2)}}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">Expected Repayment</div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="">Expected Total Capital</label>
                                            <input type="text" class="form-control" value="{{number_format($loan->amount, 2)}}" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <label for="">Expected Total Interest</label>
                                        <input type="text" class="form-control" value="{{number_format(($loan->amount*(5.5/100)*$loan->duration), 2)}}" disabled />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="">Monthly Capital Repayment</label>
                                            <input type="text" class="form-control" value="{{number_format(($loan->amount)/$loan->duration, 2)}}" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <label for="">Monthly Interest Payment</label>
                                        <input type="text" class="form-control" value="{{number_format($loan->amount*(5.5/100), 2)}}" disabled />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="">Monthly Capital Repayment</label>
                                            <input type="text" class="form-control" value="{{number_format((($loan->amount)/$loan->duration)+($loan->amount*(5.5/100)), 2)}}" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <label for="">Monthly Interest Payment</label>
                                        <input type="text" class="form-control" value="{{number_format($loan->amount + ($loan->amount*(5.5/100)*$loan->duration), 2)}}" disabled />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            Upload Documents
                        </div>
                        <div class="card-body">
                            <!-- <form>
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        @if($identity==false)
                                        <label for="">ID Card</label>
                                        <input type="file" name="id_card" id="id_card" class="form-control">
                                        @else
                                        <div class="row">
                                            <div class="col-6"><label for="">ID Card</label></div>
                                            <div class="col-6"><a href="" class="btn btn-primary btn-sm">Download</a></div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        @if($photo==false)
                                        <label for="">Passport Photos</label>
                                        <input type="file" name="passport_photo" id="passport_photo" class="form-control">
                                        @else
                                        <div class="row">
                                            <div class="col-6"><label for="">Passport Photos</label></div>
                                            <div class="col-6"><a href="" class="btn btn-primary btn-sm">Download</a></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6 col-sm-12">
                                        @if($form==false)
                                        <label for="">Loan Form</label>
                                        <input type="file" name="loan_form" id="loan_form" class="form-control">
                                        @else
                                        <div class="row">
                                            <div class="col-6"><label for="">Loan Form</label></div>
                                            <div class="col-6"><a href="" class="btn btn-primary btn-sm">Download</a></div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 col-sm-12">

                                        @if($guarantor==false)
                                        <label for="">Guarantorship</label>
                                        <input type="file" name="guarantor_form" id="guarantor_form" class="form-control">
                                        @else
                                        <div class="row">
                                            <div class="col-6"><label for="">Guarantorship</label></div>
                                            <div class="col-6"><a href="" class="btn btn-primary btn-sm">Download</a></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6 col-sm-12">
                                        @if($agreement==false)
                                        <label for="">Loan Agreement</label>
                                        <input type="file" name="agreement_form" id="agreement_form" class="form-control">
                                        @else
                                        <div class="row">
                                            <div class="col-6"><label for="">Loan Agreement</label></div>
                                            <div class="col-6"><a href="" class="btn btn-primary btn-sm">Download</a></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </form> -->
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Document</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loan_forms as $form)
                                    <tr>
                                        <td>{{$form->title}}</td>
                                        <td>
                                            <a href="/{{$form->url}}" class="btn btn-primary btn-sm">Download</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card mt-2">
                        <div class="card-header">Approval</div>
                        <div class="card-body">
                            <form method="post" action="/branch_approve_loan/{{$loan->id}}">
                                @csrf
                                <textarea class="form-control w-100" placeholder="Extra Comments" rows="3" name="comment"></textarea>
                                <button class="btn btn-primary w-100 mt-2">Approve</button>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-2">
                        <div class="card-header">Rejection</div>
                        <div class="card-body">
                            <form>
                                <textarea class="form-control w-100" placeholder="Reason for rejection" rows="3"></textarea>
                                <button class="btn btn-danger w-100 mt-2">Reject</button>
                            </form>
                        </div>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
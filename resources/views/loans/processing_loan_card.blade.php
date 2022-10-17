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

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Duration</label>
                                    <input type="text" class="form-control" value="{{$loan->duration}} Months" disabled>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <input type="text" class="form-control" value="{{$loan->loan_status}}" disabled>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Balance</label>
                                    <input type="text" class="form-control" value="{{number_format($savings)}}" disabled>
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

                    <!-- <div class="card mt-3">
                        <div class="card-header">
                            Upload Documents
                        </div>
                        <div class="card-body">
                            <form action="/branch_upload_forms/{{$loan->id}}" method="post" enctype="multipart/form-data">
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
                                        <label for="">Passport Photo</label>
                                        <input type="file" name="passport_photo" id="passport_photo" class="form-control">
                                        @else
                                        <div class="row">
                                            <div class="col-6"><label for="">Passport Photo</label></div>
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
                                        <label for="">Security Document</label>
                                        <input type="file" name="guarantor_form" id="guarantor_form" class="form-control">
                                        @else
                                        <div class="row">
                                            <div class="col-6"><label for="">Security Document</label></div>
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
                                <div class="row mt-2">
                                    <div class="col-md-12 col-sm-12">
                                        <label></label>
                                        @if($loan->loan_status=='pending')
                                        <button type="submit" class="btn btn-primary w-100">Upload</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> -->

                    <div class="card mt-3">
                        <div class="card-header">Approval Status</div>
                        <div class="card-body">
                            @if($loan->branch_manager_approval)
                            <div class="form-group mb-2">
                                <label for="">Approved By Branch Manager</label>
                                <input class="form-control" disabled value="{{$loan->branch_manager_remarks}}">
                            </div>
                            @endif

                            @if($loan->loan_officer_approval)
                            <div class="form-group mb-2">
                                <label for="">Approved By Loan Officer</label>
                                <input class="form-control" disabled value="{{$loan->loan_officer_remarks}}">
                            </div>
                            @endif


                            <!-- @if($loan->direct)
                            <div class="form-group mb-2">
                                <label for="">Approved By Lo</label>
                                <input class="form-control" disabled value="{{$loan->loan_officer_remarks}}">
                            </div>
                            @endif -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $('#security').on('change', function() {
            //alert(this.value);
        });
    });
</script>

@endsection
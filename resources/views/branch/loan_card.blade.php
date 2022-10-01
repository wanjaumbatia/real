@extends('layouts.branch')

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

                    <div class="card mt-2">
                        <div class="card-header">Security</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="">Security Type</label>
                                <select id="security" name="security" class="form-control">
                                    @foreach($securities as $item)
                                    <option value="{{$item->type}}">{{$item->type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
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
                                        <label for="">Passport Photos</label>
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
                                    <div class="col-md-6 col-sm-12">
                                        <label></label>
                                        <button type="submit" class="btn btn-primary w-100">Upload</button>
                                    </div>
                                </div>
                            </form>
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

<script>
    $(document).ready(function() {
        $('#security').on('change', function() {
           //alert(this.value);
        });
    });
</script>

@endsection
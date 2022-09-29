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
                                    <input type="text" class="form-control" value="{{$loan->current_savings}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                Upload Documents
                            </div> 
                            <div class="card-body">
                                <form action="/upload_forms/{{$loan->id}" method="post">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <label for="">Loan Form</label>
                                            <input type="file" name="loan-form" id="loan-form" class="form-control">
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <label for="">Guarantorship</label>
                                            <input type="file" name="guarantor-form" id="guarantor-form" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <label for="">Loan Agreement</label>
                                            <input type="file" name="agreement-form" id="loan-form" class="form-control">
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
                                <form>
                                    <textarea class="form-control w-100" placeholder="Extra Comments" rows="3"></textarea>
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
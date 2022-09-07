@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            Customer Details
                        </div>
                        <div class="col-6 text-end">
                            <a href="/customers/contribution/{{$customer['no']}}" class="btn btn-primary btn-sm">New Payment</a>
                            <a class="btn btn-primary btn-sm">Make Payment</a>
                            <a class="btn btn-primary btn-sm">Request Loan</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="post" action="{{route('customers.store')}}">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name">Customer Number</label>
                                    <input type="text" class="form-control" disabled value="{{$customer['no']}}" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" class="form-control" disabled value="{{$customer['name']}}" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <col-6 class="col-md-6 col-sm-12">
                                <div class="form-group mt-2">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" class="form-control" disabled value="{{$customer['phoneNumber']}}" />
                                </div>
                            </col-6>
                            <col-6 class="col-md-6 col-sm-12">
                                <div class="form-group mt-2">
                                    <label for="town">Town</label>
                                    <input type="text" class="form-control" disabled value="{{$customer['town']}}" />
                                </div>
                            </col-6>
                        </div>

                        <div class="row">
                            <col-6 class="col-md-6 col-sm-12">
                                <div class="form-group mt-2">
                                    <label for="phone">Handler</label>
                                    <input type="text" class="form-control" disabled value="{{$customer['handler']}}" />
                                </div>
                            </col-6>
                            <col-6 class="col-md-6 col-sm-12">
                                <div class="form-group mt-2">
                                    <label for="town">Branch</label>
                                    <input type="text" class="form-control" disabled value="{{$customer['branch']}}" />
                                </div>
                            </col-6>
                        </div>

                        <div class="form-group mt-2">
                            <label for="gender">Address</label>
                            <input type="text" class="form-control" disabled value="{{$customer['address']}}" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9 mt-2">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            Account Infomation
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <col-md-6>
                            <div class="form-group">
                                <label>Regestration Fee</label>
                                <input class="form-control" value="{{$balances['regFee']}}" disabled />
                            </div>
                            <div class="form-group">
                                <label>Savings</label>
                                <input class="form-control" value="{{$balances['savings']}}" disabled />
                            </div>
                        </col-md-6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
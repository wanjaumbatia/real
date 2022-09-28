@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">
                    <div class="row">
                        <div class="col-6 px-2">
                            <div class="row">
                                <p style="font-weight: 600;">{{$customer->name}}</p>
                            </div>
                        </div>
                        @if($customer->phone_verified == true)
                        <div class="col-6 d-flex justify-content-end">
                            <button class="btn btn-success btn-sm">Verified</button>
                        </div>
                        @else
                        <div class="col-6 d-flex justify-content-end">
                            <button class="btn btn-danger btn-sm">Not Verified</button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" disabled value="{{$customer->name}}">
                        </div>

                        <div class="form-group mt-2">
                            <label>Customer No</label>
                            <input type="text" class="form-control" disabled value="{{$customer->no}}">
                        </div>


                        <div class="form-group mt-2">
                            <label>Address</label>
                            <input type="text" class="form-control" disabled value="{{$customer->address}}">
                        </div>

                        <div class="form-group mt-2">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" disabled value="{{$customer->phone}}">
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                Balance from Old System
                            </div>
                            <div class="card-body">
                                @foreach($balances as $balance)
                                <div class="form-group mt-2">
                                    <label>{{$balance->plan}}</label>
                                    <input type="text" class="form-control" disabled value="{{number_format($balance->amount)}}">
                                </div>
                                <div class="form-group mt-2">
                                    <label>Opened On</label>
                                    <input type="text" class="form-control" disabled value="{{$balance->created_at}}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </form>

                    <div class="accordion mt-4" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Migrate Opening Balance
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <form action="/migrate_plan" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <input hidden name="customer" value="{{$customer->id}}" />
                                            <label for="">Select Plan</label>
                                            <select name="plan" id="plan" class="form-control">
                                                @foreach($plans as $plan)
                                                <option value="{{$plan->id}}">{{$plan->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="">Balance</label>
                                            <input type="number" class="form-control" name="balance" />
                                        </div>

                                        <button class="btn btn-primary w-100 mt-3">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseOne">
                                    Accounts - {{count($accounts)}}
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Plan</th>
                                                <th>Created</th>
                                                <th>Branch</th>
                                                <th>Balance</th>
                                                <th>Pending</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($accounts as $item)
                                            <tr>
                                                <td>{{$item->plan}}</td>
                                                <td>{{$item->created_by}}</td>
                                                <td>{{$item->branch}}</td>
                                                <td>{{number_format($item->balance, 2)}}</td>
                                                <td>{{number_format($item->pending, 2)}}</td>
                                                <td>{{$item->created_at}}</td>
                                                <td><a href="/delete_saving_account/{{$item->id}}" class="btn btn-danger bt-sm">DELETE</a></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="true" aria-controls="collapseOne">
                                    Payments - {{count($savings)}}
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Plan</th>
                                                <th>Transaction</th>
                                                <th>Branch</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($savings as $item)
                                            <tr>
                                                <td>{{$item->plan}}</td>
                                                <td>{{$item->transaction_type}}</td>
                                                <td>{{$item->branch}}</td>
                                                <td>{{number_format($item->amount, 2)}}</td>
                                                <td>{{$item->created_at}}</td>
                                                <td><a href="/delete_payment/{{$item->id}}" class="btn btn-danger bt-sm">DELETE</a></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseOne">
                                    Loan Repayments - {{count($loan_repayments)}}
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Loan Number</th>
                                                <th>Amount</th>
                                                <th>Handler</th>
                                                <th>Branch</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($loan_repayments as $item)
                                            <tr>
                                                <td>{{$item->loan_number}}</td>
                                                <td>{{$item->amount}}</td>
                                                <td>{{$item->handler}}</td>
                                                <td>{{$item->branch}}</td>
                                                <td>{{$item->created_at}}</td>
                                                <td><a href="/delete_loan_payment/{{$item->id}}" class="btn btn-danger bt-sm">DELETE</a></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseOne">
                                    Phone Number Change
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <form action="/phone_change" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label for="">Old Phone Number</label>
                                            <input type="number" class="form-control" value="{{$customer->phone}}" disabled />
                                        </div>

                                        <div class="form-group">
                                            <label for="">New Phone Number</label>
                                            <input type="number" class="form-control" name="new_phone" />
                                        </div>

                                        <button class="btn btn-primary w-100 mt-3">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
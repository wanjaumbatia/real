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

                    <a href="/customer/{{$customer->id}}" class="btn btn-primary w-100">Open Card</a>

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
                                    <div class="table-responsive">
                                        <table class="table table-striped table-responsive-sm">
                                            <thead>
                                                <tr>
                                                    <th>Plan</th>
                                                    <th>Created</th>
                                                    <th>Branch</th>
                                                    <th>Balance</th>
                                                    <th>Pending</th>
                                                    <th>Date</th>
                                                    @if($customer->id > 73382)
                                                    <th>Registration Fee</th>
                                                    @endif
                                                    <th>Change Plan</th>
                                                    <th>Withdrawal</th>
                                                    <th>Deposit</th>
                                                    <th></th>
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
                                                    @if($customer->id > 73382)
                                                    @if($item->reefee == 0)
                                                    <td>
                                                        <form action="/withdrawal_fix" method="Post">
                                                            @csrf
                                                            <input type="text" hidden value='{{$item->id}}' class="form-control" name="id" />
                                                            <button class="form-control btn btn-primary mt-1" type="submit">Create Reg Fee</button>
                                                        </form>
                                                    </td>
                                                    @endif
                                                    @endif
                                                    <td>
                                                        <form action="/change_plan" method="post">
                                                            @csrf
                                                            <input type="number" name="id" value="{{$item->id}}" hidden>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <select name="plan" id="plan" class="form-control">
                                                                        @foreach($plans as $plan)
                                                                        <option></option>
                                                                        <option value="{{$plan->id}}">{{$plan->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button class="btn btn-primary w-100 mt-1">Edit</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form action="/post_withdrawal1" method="POST">
                                                            @csrf
                                                            <input type="number" name="id" value="{{$item->id}}" hidden>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <input type="text" name="id" value="{{$item->id}}" hidden>
                                                                    <input value="{{$customer->id}}" hidden name="dt" id="dt" />
                                                                    <div class="form-group">
                                                                        <input class="form-control" name='amount' type="number" placeholder="Amount" />
                                                                    </div>
                                                                    <div class="form-group my-1">
                                                                        <input class="form-control" name='commission' type="number" placeholder="Commission" />
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <input class="form-control" name='payment' type="number" value="Office Admin" hidden />
                                                                    </div>
                                                                    <button class="btn btn-primary w-100" type="submit">Post</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form action="/make_deposit" method="POST">
                                                            @csrf
                                                            <input type="number" name="id" value="{{$item->id}}" hidden>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <input type="text" name="id" value="{{$item->id}}" hidden>
                                                                    <input value="{{$customer->id}}" hidden name="dt" id="dt" />
                                                                    <div class="form-group">
                                                                        <input class="form-control" name='amount' type="number" placeholder="Amount" />
                                                                    </div>
                                                                    <div class="form-group my-1">
                                                                        <input type="date" class="form-control" name='date' type="number" placeholder="Commission" />
                                                                    </div>
                                                                    <button class="btn btn-primary w-100" type="submit">Post</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td><a href="/delete_saving_account/{{$item->id}}" class="btn btn-danger bt-sm">DELETE</a></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="true" aria-controls="collapseOne">
                                    Payments
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="table-response">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Plan</th>
                                                    <th>Transaction</th>
                                                    <th>Branch</th>
                                                    <th>Amount</th>
                                                    <th>Remarks</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($savings as $item)
                                                @if($item->remarks == 'Opening Balance' || $item->status=='pending')
                                                <tr>
                                                    <td>{{$item->plan}}</td>
                                                    <td>{{$item->transaction_type}}</td>
                                                    <td>{{$item->branch}}</td>
                                                    <td>{{number_format($item->amount, 2)}}</td>
                                                    <td>{{$item->remarks}}</td>
                                                    <td>{{$item->created_at}}</td>
                                                    <td>
                                                        <form action="/change_amount" method="post">
                                                            @csrf
                                                            <input type="number" name="id" value="{{$item->id}}" hidden>
                                                            <div class="row">
                                                                <div class="col-8"><input type="number" class="form-control" required name="amount" /></div>
                                                                <div class="col-4"><button class="btn btn-primary w-100">Edit</button></div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    @if($item->remarks != 'Opening Balance')
                                                    <td><a href="/delete_payment/{{$item->id}}" class="btn btn-danger w-100">DELETE</a></td>
                                                    @endif
                                                </tr>
                                                @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSeven">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="true" aria-controls="collapseOne">
                                    Withdrawals - {{count($withdrawals)}}
                                </button>
                            </h2>
                            <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="table-reponsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Plan</th>
                                                    <th>Transaction</th>
                                                    <th>Branch</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($withdrawals as $item)
                                                <tr>
                                                    <td>{{$item->plan}}</td>
                                                    <td>{{$item->transaction_type}}</td>
                                                    <td>{{$item->branch}}</td>
                                                    <td>{{number_format($item->credit, 2)}}</td>
                                                    <td>{{$item->created_at}}</td>
                                                    <th>
                                                        @if($item->status=='confirmed')
                                                        <p class="text-success">{{$item->status}}</p>
                                                        @else
                                                        <p class="text-danger">{{$item->status}}</p>
                                                        @endif
                                                    </th>
                                                    <td><a href="/delete_payment/{{$item->id}}" class="btn btn-danger bt-sm">DELETE</a></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
                                    <div class="table-responsive">
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
                                                    <td>
                                                        <form action="/change_loan_amount" method="post">
                                                            @csrf
                                                            <input type="number" name="id" value="{{$item->id}}" hidden>
                                                            <div class="row">
                                                                <div class="col-8"><input type="number" class="form-control" required name="amount" /></div>
                                                                <div class="col-4"><button class="btn btn-primary w-100">Edit</button></div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td><a href="/delete_loan_payment/{{$item->id}}" class="btn btn-danger bt-sm">DELETE</a></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="true" aria-controls="collapseOne">
                                    Sales Excutive Change
                                </button>
                            </h2>
                            <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <form method="post" action="/handler_change/{{$customer->id}}">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="">Current Handler</label>
                                                    <input type="text" class="form-control" value="{{$customer->handler}}" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="">New Handler</label>
                                                    <select name="handler" id="seps" class="form-control" style="width: 100%;">
                                                        <option></option>
                                                        @foreach($seps as $sep)
                                                        <option value="{{$sep->name}}">{{$sep->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button class="btn btn-primary w-100 mt-2" type="submit">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingten">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseten" aria-expanded="true" aria-controls="collapseOne">
                                    Edit Customer Name
                                </button>
                            </h2>
                            <div id="collapseten" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="table-response">
                                        <form action="/change_name" method="post">
                                            @csrf
                                            <div class="form-group">
                                                <label for="">Old Name</label>
                                                <input type="number" class="form-control" name="id" value="{{$customer->id}}" hidden />
                                                <input type="text" class="form-control" name="old_name" value="{{$customer->name}}" hidden />
                                                <input type="text" class="form-control" name="old_name" value="{{$customer->name}}" disabled />
                                            </div>
                                            <div class="form-group">
                                                <label for="">New Name</label>
                                                <input type="text" class="form-control" name="new_name" />
                                            </div>

                                            <button class="btn btn-primary w-100 mt-3">Submit</button>
                                        </form>
                                    </div>
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
                                    <form action="/change_phone" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label for="">Old Phone Number</label>
                                            <input type="number" class="form-control" name="id" value="{{$customer->id}}" hidden />
                                            <input type="number" class="form-control" name="old_phone" value="{{$customer->phone}}" hidden />
                                            <input type="number" class="form-control" name="old_phone" value="{{$customer->phone}}" disabled />
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#seps').select2();
        const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;
        $('#withdrawal-form').submit((e) => {
            e.preventDefault();

            var id = $('#id').val();
            var dt = $('#dt').val();
            var amount = $('#amount').val();
            var commission = $('#commission').val();
            var payment = $('#payment').val();

            var data = {
                id: id,
                amount: amount,
                commission: commission,
                payment: 'Office Admin',
            };

            fetch("/post_withdrawal", {
                    method: 'post',
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify(data),
                })
                .then(results => results.json())
                .then((data) => {
                    log(data);
                    window.location.replace("/sep_customer/" + dt);
                })
                .catch(error => console.error(error));
        });

    });
</script>
@endsection
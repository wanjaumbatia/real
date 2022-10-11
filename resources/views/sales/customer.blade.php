@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">{{$customer->name}}</div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label for="">Customer Number</label>
                                    <input type="text" class="form-control" value="{{$customer->no}}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="">Address</label>
                                    <input type="text" class="form-control" value="{{$customer->address}}" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="">Phone Number</label>
                                    <input type="text" class="form-control" value="{{$customer->phone}}" disabled>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($result['accounts'])
                    @foreach($result['accounts'] as $item)
                    <div class="card mt-2">
                        <div class="card-header">
                            <div class="row font-weight-bold">
                                <div class="col-6">
                                    <h3 class="font-weight-bold">{{$item['details']->plan}}</h3>
                                    <h6>{{$item['details']->name}}</h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a href="/statement/{{$item['details']->id}}" class="btn btn-primary btn-sm">Statement</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label>Balance</label>
                                    <input class="form-control" disabled value="{{$item['confirmed'], 0}}" />
                                </div>
                                <div class="form-group">
                                    <label>Pending Collection</label>
                                    <input class="form-control" disabled value="{{$item['pending'], 0}}" />
                                </div>
                                <div class="form-group">
                                    <label>Pending Withdrawals</label>
                                    <input class="form-control" disabled value="{{$item['pending_withdrawal'], 0}}" />
                                </div>

                                <a href="/withdrawal/{{$item['details']->id}}" class="btn btn-primary w-100 btn-sm mt-2">Withdraw</a>
                            </form>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p>This Customer has no plan. kindly contact the IT Department.</p>
                    @endif

                    @if($result['loan']!=null)
                    <div class="card mt-2">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h3 class="font-weight-bold">Loan</h3>
                                </div>
                                <div class="col-6 text-end">
                                    <a href="/loan_status/{{$result['loan']->id}}" class="btn btn-primary btn-sm">View</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                @if($result['loan']->loan_status=='PENDING'||$result['loan']->loan_status=='PROCESSING')
                                <div class="form-group">
                                    <label for="">Amount</label>
                                    <input type="text" class="form-control" value=" {{number_format($result['loan']->loan_amount, 2)}}" disabled />
                                </div>
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <input type="text" class="form-control" value="Pending" disabled />
                                </div>
                                @endif

                                @if($result['loan']->loan_status=='ACTIVE'||$result['loan']->loan_status=='EXPIRED'||$result['loan']->loan_status=='BAD')
                                <div class="form-group">
                                    <label for="">Amount</label>
                                    <input type="text" class="form-control" value=" {{number_format($result['loan']->total_balance, 2)}}" disabled />
                                </div>
                                <a href="/repay/{{$customer->id}}" class="btn btn-primary btn-sm mt-2 w-100">Repay</a>
                                @endif
                            </form>
                        </div>
                    </div>
                    @endif
                    <div class="card mt-2">
                        <div class="card-body">
                            <a href="/loan/{{$customer->id}}" class="btn btn-primary btn-sm w-100 my-1">Apply Loan</a>
                            <a href="/collection/{{$customer->id}}" class="btn btn-primary btn-sm w-100 my-1">Go to payments</a>
                            <button id='plan_modal' class="btn btn-primary btn-sm w-100 my-1">New Plan</buttin>
                        </div>
                    </div>

                    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Create Plan</h5>
                                </div>
                                <div class="modal-body">
                                    <form action="/create_account/{{$customer->id}}" method="POST">
                                        @csrf
                                        <select name="plan" id="plan" class="form-control">
                                            @foreach($plans as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary w-100 mt-2" type="submit">Create</button>
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

<script>
    $(document).ready(function() {
        $("#plan_modal").click(function() {
            $('#modal').modal('show');
        });

        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection
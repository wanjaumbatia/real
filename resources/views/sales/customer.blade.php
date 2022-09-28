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
                                <h3 class="font-weight-bold">{{$item['details']->plan}}</h3>
                                <h6>{{$item['details']->name}}</h6>
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
                    <p>This Customer has no plan.</p>
                    @endif

                    @if($result['loan']!=null)
                    <div class="card mt-2">
                        <div class="card-header">
                            Loan
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label for="">Amount</label>
                                    <input type="text" class="form-control" value=" {{number_format($result['loan']->amount, 2)}}" disabled />
                                </div>
                                @if($result['loan']->status=='pending')
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <input type="text" class="form-control" value="Pending" disabled />
                                </div>
                                @endif

                                @if($result['loan']->status=='running')
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
                            <a href="/" class="btn btn-primary btn-sm w-100 my-1">New Plan</a>
                        </div>
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
            "info": true
        });
    });
</script>
@endsection
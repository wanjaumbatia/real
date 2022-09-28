@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        <h3 class="font-weight-bold">{{$customer->name}}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label>Customer No.</label>
                            <input class="form-control" disabled value="{{$customer->no}}" />
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input class="form-control" disabled value="{{$customer->address}}" />
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input class="form-control" disabled value="{{$customer->phone}}" />
                        </div>
                    </form>
                </div>
            </div>

            @foreach($result['accounts'] as $item)
            <div class="card mt-2">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        <h3 class="font-weight-bold">{{$item['details']->plan}}</h3>
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

                        <a href="/ios/withdrawal/{{$item['details']->id}}" class="btn btn-primary w-100 btn-sm mt-2">Withdraw</a>
                    </form>
                </div>
            </div>
            @endforeach

            <a href="" class="btn btn-primary w-100 btn-sm mt-2">Apply Loan</a>
            <a href="/ios/payment/{{$customer->id}}" class="btn btn-primary w-100 btn-sm mt-2">Make Payment</a>
            <button type="button" data-bs-toggle="modal" data-bs-target="#plansModal" class="btn btn-primary w-100 btn-sm mt-2">New Plan</button>

            <div class="modal fade" id="plansModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Select A Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @foreach($plans as $item)
                                <form method="post" action="/ios/create_plan">
                                    @csrf
                                    <input hidden value="{{$item->id}}" name="plan"/>
                                    <input hidden value="{{$customer->id}}" name="customer"/>
                                    <button type="submit" class="btn btn-primary btn-sm w-100 my-2">{{$item->name}}</button>
                                </form>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            "paging": false,
            "ordering": false,
            "info": false
        });
    });
</script>
@endsection
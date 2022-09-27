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
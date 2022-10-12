@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        <h3 class="font-weight-bold">{{$customer->name}}</h3>
                        <h4>Balance {{number_format($balance, 2)}}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="/ios/withdraw">
                        @csrf
                        <div class="form-group">
                            <input hidden value="{{$account->id}}" name="id"/>
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount">
                        </div>
                        
                        <div class="form-group">
                            <label>Commission</label>
                            <input type="number" class="form-control" name="commission">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
]
@endsection
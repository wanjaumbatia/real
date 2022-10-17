@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Transfer Funds
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form action="/post_cashflow" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="">Branch</label>
                            <select name="branch" id="branch" class="form-control">
                                @foreach($branches as $item)
                                <option value="{{$item->name}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="number" class="form-control" name="amount" />
                        </div>

                        <div class="form-group">
                            <label for="">Type</label>
                            <select name="direction" id="" class="form-control">
                                <option value="1">To HQ</option>
                                <option value="2">From HQ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea name="description" id="description" rows="5" class="form-control"></textarea>
                        </div>

                        <button class="btn btn-primary w-100 mt-2">Submit</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
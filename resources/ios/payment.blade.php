@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        <h3 class="font-weight-bold">Payment for {{$customer->name}}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="/ios/pay">
                        @csrf
                        @foreach($accounts as $item)
                        <div class="form-group">
                            <label>{{$item->plan}} | {{$item->name}}</label>
                            <input type="number" class="form-control" name="{{$item->id}}" />
                        </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
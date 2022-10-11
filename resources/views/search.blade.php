@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{ __('Search Customer') }}
                </div> 
            </div>
        </div>
        <div class="card-body">
            <form action="/search" method="get">
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" class="form-control" name="userid" required />
                    <button type="submit" class="btn btn-primary w-100 mt-2">Search</button>
                </div>
            </form>

            @if($customer !== null)
                <table class="table">
                    <tr>
                        <td>{{$customer->name}}</td>
                        <td>{{$customer->phone}}</td>
                        <td>{{$customer->handler}}</td>
                        <td>
                            <a href="/sep_customer/{{$customer->id}}" class="btn btn-primary btn-sm w-100">Open</a>
                        </td>
                    </tr>
                </table>
            @endif
        </div>
    </div>
    @endsection
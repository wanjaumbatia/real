@if(Auth::user()->operations_manager==true)
@extends('layouts.operations')
@else
@extends('layouts.app')
@endif

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Withdrawal Requests') }}</span></div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Sales Executive</th>
                                <th>Branch</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $item)
                            <tr>
                                <td>{{$item->customer_name}}</td>
                                <td>₦. {{number_format($item->credit,0)}}</td>
                                <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
                                <td>{{$item->created_by}}</td>
                                <td>{{$item->branch}}</td>
                                @if($item->request_approval==false)
                                <td><a href="/office/reconcile_withdrawal/{{$item->id}}" class="btn btn-primary btn-sm">Disburse</a></td>
                                @else
                                <td><span class="text-primary">Awaiting Approval</span></td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
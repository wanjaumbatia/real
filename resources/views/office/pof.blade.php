@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            {{ __('Payment on field') }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{$item->customer_name}}</td>
                                <td>₦. {{number_format($item->credit,0)}}</td>
                                <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
                                <td>{{$item->created_by}}</td>
                                <td>{{$item->branch}}</td>
                                @if($item->status=='pending')
                                <td><a href="/office/reconcile_withdrawal/{{$item->id}}" class="btn btn-primary btn-sm">Confirm</a></td>
                                @else
                                <td><span class="text-primary">Confirmed</span></td>
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
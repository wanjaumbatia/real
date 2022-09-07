@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            {{ __('Contributions') }} - N.{{number_format($total,0)}}
                        </div>
                        <div class="col-4 text-end">
                            <a href="/contributions/create" class="btn btn-primary btn-sm">Create New</a>
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
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Sales Executive</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{$transaction->no}}</td>
                                <td>{{$transaction->name}}</td>
                                <td>₦.{{number_format($transaction->amount,0)}}</td>
                                <td>{{$transaction->handler}}</td>
                                <td>{{$transaction->status}}</td>
                                <td>{{date('d-m-Y', strtotime($transaction->created_at))}}</td>
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
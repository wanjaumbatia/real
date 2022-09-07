@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            {{ __('Withdrawals') }}
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
                            @foreach($withdrawals as $item)
                            <tr>
                                <td>{{$item->no}}</td>
                                <td>{{$item->name}}</td>
                                <td>₦.{{number_format($item->amount,0)}}</td>
                                <td>{{$item->handler}}</td>
                                <td>{{$item->status}}</td>
                                <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
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
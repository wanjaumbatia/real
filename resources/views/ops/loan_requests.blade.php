@extends('layouts.operations')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Loan Requests
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id='table' class="table table-striped mt-2">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Appliaction Date</th>
                                    <th>Amount</th>
                                    <th>Interest</th>
                                    <th>Duration</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $item)
                                <tr>
                                    <td>{{$item->customer}}</td>
                                    <td>{{$item->created_at}}</td>
                                    <td>{{number_format($item->loan_amount, 2)}}</td>
                                    <td>{{$item->percentage}} %</td>
                                    <td>{{$item->duration}} Months</td>
                                    <td><a href="/admin_loan_request/{{$item->id}}" class="btn btn-primary btn-block">Open</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
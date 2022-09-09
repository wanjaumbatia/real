@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            {{ __('Loans') }} 
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
                                <th>Customer</th>                                
                                <th>Amount</th>                            
                                <th>Interest Rate</th>
                                <th>Duration</th>
                                <th>Application Date</th>
                                <th>Sales Executive</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td>₦{{number_format($item->amount)}}</td>
                                <td>{{$item->interest_percentage}}</td>
                                <td>{{$item->duration}} Months</td>
                                <td>{{date('d-m-Y', strtotime($item->application_date))}}</td>                                
                                <td>{{$item->handler}}</td>
                                <td>{{$item->status}}</td>
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
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            {{ __('Total Branch Commisions') }} - <span style="font-weight: bold !important;">₦.{{number_format($total,0)}}</span>
                        </div>

                        <!-- <div class="col-4 text-end">
                            {{ __('Pending Loan Collection') }} - <span style="font-weight: bold !important;">₦.{{number_format(1000,0)}}</span>
                        </div> -->
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
                                <th>Sales Executive</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Branch</th>
                                <th>Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commissions as $item)
                            <tr>
                                <td>{{$item->handler}}</td>
                                <td>₦{{$item->amount}}</td>
                                <td>{{$item->description}}</td>
                                <td>{{$item->branch}}</td>
                                <td>
                                    @if($item->approved == true)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Not Approved</span>
                                    @endif
                                </td>
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
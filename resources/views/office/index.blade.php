@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        Total Expected = {{number_format($total_expected, 2)}}
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
                                <th>Collection</th>
                                <th>Withdrawal</th>
                                <th>Unconfirmed POF</th>
                                <th>Pay On Field</th>
                                <th>Expected Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                @if($item->savings>0)
                                <td><a href="" class="text-danger" style="text-decoration: none;">₦ {{number_format($item->savings + $item->loan_collection, 0)}}</span></td>
                                @else
                                <td><a class="text-success"></span>0</td>
                                @endif
                                @if($item->savings>0)
                                <td><a href="/office/withdrawal_list/{{$item->name}}" class="text-danger" style="text-decoration: none;">₦ {{number_format($item->withdrawals - $item->unconfirmed_pof, 0)}}</a></span></td>
                                @else
                                <td><a class="text-success"></span>0</td>
                                @endif
                                @if($item->unconfirmed_pof>0)
                                <td><a href="/office/pof/{{$item->name}}" class="text-danger" style="text-decoration: none;">₦ {{number_format($item->unconfirmed_pof,0)}}</span></td>
                                @else
                                <td><a class="text-success"></span>0</td>
                                @endif

                                @if($item->pof>0)
                                <td><a href="" class="text-danger" style="text-decoration: none;">₦ {{number_format($item->pof,0)}}</span></td>
                                @else
                                <td><a class="text-success"></span>0</td>
                                @endif
                                @if($item->savings>0)
                                <td><a href="/office/reconcile/{{$item->name}}" class="text-danger" style="text-decoration: none;">₦ {{number_format($item->savings+$item->loan_collection - $item->pof,0)}}</span></td>
                                @else
                                <td><a class="text-success"></span>0</td>
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
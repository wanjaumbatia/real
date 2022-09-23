@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            {{ __('Expected Collection') }} - <span style="font-weight: bold !important;">₦.{{number_format($total_savings,0)}}</span>
                        </div>
                        <div class="col-6 text-end">
                            {{ __('Pending Withdrawals') }} - <span style="font-weight: bold !important;">₦.{{number_format($total_withdrawals,0)}}</span>
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
                                <th>Collection</th>
                                <th>Withdrawal</th>
                                <th>UnconfirmedPay On Field</th>
                                <th>Pay On Field</th>
                                <th>Expected Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $sep)
                            <tr>
                                <td>{{$sep['sep']}}</td>
                                @if($sep['savings']>0)
                                <td><a href="#" class="text-danger" style="text-decoration: none;">₦ {{number_format($sep['savings'],0)}}</span></td>
                                @else
                                <td><a class="text-success">0</span></td>
                                @endif
                                @if($sep['withdrawals']>0)
                                <td><a href="/office/withdrawal_list/{{$sep['sep']}}" class="text-danger" style="text-decoration: none;">₦ {{number_format($sep['withdrawals'],0)}}</span></td>
                                @else
                                <td><a class="text-success">0</span></td>
                                @endif 
                                @if($sep['unconfirmed_pof']>0)
                                <td><a href="/office/pof/{{$sep['sep']}}" class="text-danger" style="text-decoration: none;">₦ {{number_format($sep['unconfirmed_pof'],0)}}</span></td>
                                @else
                                <td><a class="text-success"></span>0</td>
                                @endif
                                @if($sep['pof']>0)
                                <td><a href="#" class="text-danger" style="text-decoration: none;">₦ {{number_format($sep['pof'],0)}}</span></td>
                                @else
                                <td><a class="text-success"></span>0</td>
                                @endif
                                @if($sep['expected']>0)
                                <td><a href="/office/reconcile/{{$sep['sep']}}" class="text-danger" style="text-decoration: none;">₦ {{number_format($sep['expected'],0)}}</span></td>
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
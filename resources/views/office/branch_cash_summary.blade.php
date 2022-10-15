@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{ __('Cash Summary') }}
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-stripped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Opening Balance</th>
                            <th>Remmitance</th>
                            <th>Cash Inflow</th>
                            <th>Expense</th>
                            <th>Cash Ouflow</th>
                            <th>Cash Ouflow HQ</th>
                            <th>Cash Ouflow Others</th>
                            <th>Withdrawals</th>
                            <th>Loan Issued</th>
                            <th>Cashbook Balance</th>
                            <th>Cash At Hand</th>
                            <th>Shortage (+/-)</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($data as $item)
                        <tr>
                            <td>{{$item->report_date}}</td>
                            <td>{{number_format($item->opening_balance)}}</td>
                            <td>{{number_format($item->Remmitance)}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{number_format($item->Withdrawals *-1)}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach 
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
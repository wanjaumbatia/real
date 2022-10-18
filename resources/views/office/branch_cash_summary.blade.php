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
                            <th>Withdrawals</th>
                            <th>Loan Issued</th>
                            <th>Cashbook Balance</th>
                            <th>Cash At Hand</th>
                            <th>Shortage (+/-)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                        <tr>
                            <td>{{$item['report_date']}}</td>
                            <td>{{number_format($item['opening_balance'])}}</td>
                            <td align="right"><a href="/remmittance?date={{$item['date']}}" style="text-decoration: none;">{{number_format($item['remmittance'])}}</a></td>
                            <td align="right"><a href="#" style="text-decoration: none;">{{number_format($item['inflow'])}}</a></td>
                            <td align="right"><a href="#" style="text-decoration: none;">{{number_format($item['expenses'])}}</a></td>
                            <td align="right"><a href="#" style="text-decoration: none;">{{number_format($item['outflow'])}}</a></td>
                            <td align="right"><a href="/cash_summary_withdrawals?date={{$item['date']}}" style="text-decoration: none;">{{number_format($item['withdrawals'])}}</a></td>
                            <td align="right">{{number_format($item['loans'])}}</td>
                            <td align="right">{{number_format(($item['opening_balance'] + $item['remmittance'] + $item['inflow']) - ($item['expenses'] + $item['outflow'] + $item['withdrawals'] + $item['loans']))}}</td>
                            <td>{{number_format($item['at_hand'])}}</td>
                            <td>{{number_format($item['at_hand'] - (($item['opening_balance'] + $item['remmittance'] + $item['inflow']) - ($item['expenses'] + $item['outflow'] + $item['withdrawals'] + $item['loans'])))}}</td>
                            <td><button class="btn btn-primary btn-sm" onclick="openModal('{{$item['date']}}', '{{$item['opening_balance']}}', '{{$item['remmittance']}}', '{{$item['inflow']}}', '{{$item['expenses']}}','{{$item['outflow']}}','{{$item['withdrawals']}}','{{$item['loans']}}')" ,>Close</button></td>
                        </tr>
                        @endforeach
                        @foreach ($data1 as $item)
                        <tr>
                            <td>{{$item->report_date}}</td>
                            <td>{{number_format($item->opening_balance)}}</td>
                            <td align="right"><a href="/remmittance?date={{$item->report_date}}" style="text-decoration: none;">{{number_format($item->Remmitance)}}</a></td>
                            <td align="right"><a href="#" style="text-decoration: none;">{{number_format($item->CashInflow)}}</a></td>
                            <td align="right"><a href="#" style="text-decoration: none;">{{number_format($item->Expense)}}</a></td>
                            <td align="right"><a href="#" style="text-decoration: none;">{{number_format($item->CashOutflow)}}</a></td>
                            <td align="right"><a href="/cash_summary_withdrawals?date={{$item['date']}}" style="text-decoration: none;">{{number_format($item->Withdrawals)}}</a></td>
                            <td align="right">{{number_format($item->LoanIssued)}}</td>
                            <td align="right">{{number_format(($item->opening_balance + $item->Remmitance + $item->CashInflow) - ($item['expenses'] + $item['outflow'] + $item->Withdrawals + $item->LoanIssued))}}</td>
                            <td>{{number_format($item->CashAtHand)}}</td>
                            <td>{{number_format($item->CashAtHand - (($item->opening_balance + $item->Remmitance + $item->CashInflow) - ($item->Expense + $item->CashOutflow + $item->Withdrawals + $item->LoanIssued)))}}</td>
                            <td><button class="btn btn-primary btn-sm" onclick="openModal('{{$item->report_date}}', '{{$item->opening_balance}}', '{{$item->Remmitance}}', '{{$item->CashInflow}}', '{{$item->Expense}}','{{$item->CashOutflow}}','{{$item->Withdrawals}}','{{$item->LoanIssued}}')" ,>Close</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Enter Cash At Hand</h5>
                </div>
                <div class="modal-body">
                    <form action="/save_cash_at_hand" method="POST">
                        @csrf
                        <input id="date" hidden name="date" />
                        <input id="opening_balance" hidden name="opening_balance" />
                        <input id="deposits" hidden name="deposits" />
                        <input id="inflow" hidden name="inflow" />
                        <input id="expenses" hidden name="expenses" />
                        <input id="outflow" hidden name="outflow" />
                        <input id="withdrawals" hidden name="withdrawals" />
                        <input id="loans" hidden name="loans" />
                        <div class="form-group">
                            <label for="">Amount At Hand</label>
                            <input type="text" class="form-control" name="amount">
                        </div>

                        <div class="form-group">
                            <label for="">Remarks</label>
                            <textarea name="remarks" rows="3" class="form-control"></textarea>
                        </div>
                        <button class="btn btn-primary w-100 mt-2" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function openModal(a, b, c, d, e, f, g, h) {
        $('#date').val(a);
        $('#opening_balance').val(b);
        $('#deposits').val(c);
        $('#inflow').val(d);
        $('#expenses').val(e);
        $('#outflow').val(f);
        $('#withdrawals').val(g);
        $('#loans').val(h);
        $('#modal').modal('show');


    }
    $(document).ready(function() {
        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>

@endsection
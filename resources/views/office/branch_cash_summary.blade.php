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
                            <td></td>
                            <td></td>
                            <td><button class="btn btn-primary btn-sm" onclick="openModal('{{$item['date']}}')">Close</button></td>
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
                        <input id="date" hidden/>
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
    function openModal(e) {
        $('#date').val(e);
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
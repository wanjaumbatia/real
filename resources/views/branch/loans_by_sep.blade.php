@extends('layouts.branch')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Loans
                </div>
                <div class="card-body">
                    <form method="get" action="/branch_loan_by_sep">
                        <div class="row w-100">
                            <div class="col-9">
                                <select class="form-control w-100" name="name" id="seps">
                                    @foreach($seps as $item)
                                    <option value="{{$item->name}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <button class="btn btn-primary w-100 btn-sm">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr />
                    <div class="table-responsive">
                        <table id='table' class="table table-striped mt-2">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Start Date</th>
                                    <th>Exit Date</th>
                                    <th>Amount</th>
                                    <th>Interest</th>
                                    <th>Duration</th>
                                    <th>Balance</th>
                                    <th>Savings</th>
                                    <th>Status</th>
                                    <th>Branch</th>
                                    <th>Count Down</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $item)
                                <tr>
                                    <td>{{$item->customer}}</td>
                                    <td>{{date('d-m-Y', strtotime($item->start_date))}}</td>
                                    <td>{{date('d-m-Y', strtotime($item->exit_date))}}</td>
                                    <td>{{number_format($item->loan_amount)}}</td>
                                    <td>{{$item->percentage}} %</td>
                                    <td>{{$item->duration}} Months</td>
                                    <td>{{number_format($item->total_balance,0)}}</td>
                                    <td>
                                        <button onclick="openModal('{{$item->id}}', '{{$item->savings}}')" class="btn btn-link">{{number_format($item->savings,0)}}</button>
                                    </td>
                                    @if($item->loan_status == 'BAD')
                                    <td>
                                        <p class="text-danger font-weight-bold">{{$item->loan_status}}</p>
                                    </td>
                                    @elseif(($item->loan_status == 'EXPIRED'))
                                    <td>
                                        <p class="font-weight-bold" style="color: #f5b942">{{$item->loan_status}}</p>
                                    </td>
                                    @else
                                    <td>
                                        <p class="text-primary font-weight-bold">{{$item->loan_status}}</p>
                                    </td>
                                    @endif
                                    <td>{{$item->branch}}</td>
                                    <td>
                                        @if($item->countdown <0) <p class="text-danger">{{$item->countdown}}</p>
                                            @else
                                            <p class="text-success">{{$item->countdown}}</p>
                                            @endif
                                    </td>
                                    <td><a href="/loan_review/{{$item->id}}" class="btn btn-primary btn-sm">Review</a></td>
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
<!-- Modal -->
<div class="modal fade" id="move_money" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" onclick="closeModal()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/move_saving" method="POST">
                    @csrf
                    <input type="text" id="id" name="id" hidden>
                    <div class="form-group">
                        <label for="">Savings</label>
                        <input type="text" class="form-control" id="savings" disabled />
                    </div>
                    <div class="form-group">
                        <label for="">Amount to move</label>
                        <input type="text" class="form-control" id="amount" name="amount" required />
                    </div>
                    <button class="btn btn-primary w-100 mt-2" type="submit">Move Savings to Loan</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#seps').select2();
        $('#table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ],
            "paging": true,
            "ordering": true,
            "info": true
        });
    });

    function openModal(id, amount) {
        $('#id').val(id);
        $('#savings').val(amount);
        $('#move_money').modal('show');
    }

    function closeModal() {
        $('#move_money').modal('hide');
    }
</script>
@endsection
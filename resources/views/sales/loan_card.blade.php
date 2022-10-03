@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{$customer->name}}
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Customer Name</label>
                                    <input type="text" class="form-control" value="{{$customer->name}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Phone Number</label>
                                    <input type="text" class="form-control" value="{{$customer->phone}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-1">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Address</label>
                                    <input type="text" class="form-control" value="{{$customer->address}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Handler</label>
                                    <input type="text" class="form-control" value="{{$customer->handler}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Loan Amount</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->amount, 0)}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Interest</label>
                                    <input type="text" class="form-control" value="{{$loan->interest_percentage}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-1">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Duration</label>
                                    <input type="text" class="form-control" value="{{$loan->duration}} Months" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Current Savings</label>
                                    <input type="text" class="form-control" value="{{number_format($loan->current_savings, 2)}}" disabled>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card mt-3">
                        <div class="card-header">Repayment Details</div>
                        <div class="progress mx-3 mt-2">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: <?php echo $progress ?>%" aria-valuenow="{{$progress}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="card-body">
                            <form>
                               
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="">Expected Total Capital</label>
                                            <input type="text" class="form-control" value="{{number_format($loan->amount, 2)}}" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <label for="">Expected Total Interest</label>
                                        <input type="text" class="form-control" value="{{number_format(($loan->amount*(5.5/100)*$loan->duration), 2)}}" disabled />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="">Monthly Capital Repayment</label>
                                            <input type="text" class="form-control" value="{{number_format(($loan->amount)/$loan->duration, 2)}}" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <label for="">Monthly Interest Payment</label>
                                        <input type="text" class="form-control" value="{{number_format($loan->amount*(5.5/100), 2)}}" disabled />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">Repayment Statement</div>
                        <div class="card-body">
                            <table id="table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $item)
                                    <tr>
                                        <td>{{$item->created_at}}</td>
                                        <td>{{$item->description}}</td>
                                        <td>{{$item->amount}}</td>
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
    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            'info': false
        });
    });
</script>

@endsection
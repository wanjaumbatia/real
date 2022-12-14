@extends('layouts.loan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Loans
                </div>
                <div class="card-body">
                    <form action="/active_loans" method="get">
                        <select name="branch" id="branch" class="form-control">
                            @foreach($branches as $branch)
                            <option value="{{$branch->name}}" {{$selected == $branch->name ? 'selected' : ''}}>{{$branch->name}}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>
                    </form>
                    <div class="table-responsive mt-3">
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
                                    <th>Status</th>
                                    <th>Countdown</th>
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
                                    <td>{{$item->loan_status}}</td>
                                    <td>
                                        @if($item->countdown <0) <p class="text-danger">{{$item->countdown}}</p>
                                            @else
                                            <p class="text-success">{{$item->countdown}}</p>
                                            @endif
                                    </td>
                                    <td><a href="/loan_card/{{$item->id}}" class="btn btn-primary btn-block">Open</a></td>
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

<script>
    $(document).ready(function() {
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
</script>
@endsection
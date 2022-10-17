@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Active Real Invest</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table table-stripped">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Sales Exec.</th>
                                        <th>Phone</th>
                                        <th>Start Date</th>
                                        <th>Exit Date</th>
                                        <th>Amount</th>
                                        <th>Plan</th>
                                        <th>Expected Returns</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <td>{{$item->customer_name}}</td>
                                        <td>{{$item->handler}}</td>
                                        <td>{{$item->phone}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->start_date))}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->exit_date))}}</td>
                                        <td>{{number_format($item->amount)}}</td>
                                        <td>{{$item->duration}} Months</td>
                                        <td>{{number_format($item->amount + ($item->amount * $item->percentage/100))}}</td>
                                        <td>{{$item->status}}</td> </tr>
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

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection
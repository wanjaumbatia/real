@extends('layouts.sales')

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
                                        <th>Phone</th>
                                        <th>Start Date</th>
                                        <th>Exit Date</th>
                                        <th>Amount</th>
                                        <th>Plan</th>
                                        @if(auth()->user()->office_admin == true)
                                        <th>Action</th>
                                        @endif
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $item)
                                    <tr>
                                        <td>{{$item->customer_name}}</td>
                                        <td>{{$item->phone}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->start_date))}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->exit_date))}}</td>
                                        <td>{{number_format($item->amount)}}</td>
                                        <td>{{$item->duration}} Months</td>
                                        <td>
                                            @if(auth()->user()->office_admin == true)
                                            <a href="/confirm_payment" class="btn btn-primary btn-sm">Confirm</a>
                                            @endif
                                            <a href="/confirm_payment" class="btn btn-primary btn-sm">Confirm</a>
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
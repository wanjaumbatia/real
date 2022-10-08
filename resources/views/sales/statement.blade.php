@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">Statement for {{$customer->name}}</div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Remarks</th>
                                            <th>Transaction Type</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $item)
                                        <tr>
                                            <td>{{$item->created_at}}</td>
                                            <td>{{$item->remarks}}</td>
                                            <td>{{$item->transaction_type}}</td>
                                            <td>{{$item->amount}}</td>
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
            "paging": false,
            "ordering": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endsection
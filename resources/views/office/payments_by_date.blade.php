@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Breakdown List</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id='table' class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Posting Date</th>
                                    <th>Customer</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{$item->created_at}}</td>
                                    <td>{{$item->customer_name}}</td>
                                    <td>{{$item->plan}}</td>
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
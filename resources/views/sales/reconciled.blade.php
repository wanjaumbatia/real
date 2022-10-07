@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Reconciled Records</div>
                <div class="card-body">
                    <table id="table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Expexted Amount</th>
                                <th>Submitted Amount</th>
                                <th>Reference</th>
                                <th>Reconciled By</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->expected}}</td>
                                <td>{{$item->submited}}</td>
                                <td>{{$item->reconciliation_reference}}</td>
                                <td>{{$item->reconciled_by}}</td>
                                <td><a href="/reconciliation/{{$item->reconciliation_reference}}" class="btn btn-primary btn-sm">Open</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
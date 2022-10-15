@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        Expenses
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id='table' class="table table-striped">
                            <thead>
                                <th>Code</th>
                                <th>Branch</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Created By</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{$item->type}}</td>
                                    <td>{{$item->branch}}</td>
                                    <td>{{$item->description}}</td>
                                    <td>{{$item->status}}</td>
                                    <td>{{number_format($item->amount)}}</td>
                                    <td>{{$item->created_by}}</td>
                                    <td>
                                        <a href="/approve_expenses/{{$item->id}}" class="btn btn-primary btn-sm">Approve</a>
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
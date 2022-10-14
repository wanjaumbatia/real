@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
                                <th>Branch</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Created By</th>
                            </thead>
                            <tbody>
                                @foreach($expenses as $item)
                                <tr>
                                    <td>{{$item->branch}}</td>
                                    <td>{{$item->description}}</td>
                                    <td>{{$item->status}}</td>
                                    <td>{{$item->amount}}</td>
                                    <td>{{$item->remarks}}</td>
                                    <td>{{$item->created_by}}</td>
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
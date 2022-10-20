@extends('layouts.operations')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6"> Report Cash Flow</div>
                        <div class="col-6 text-end">
                            <a href="/add_cashflow" class="btn btn-primary btn-sm">New Cashfow</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <th>Branch</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Created On</th>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{$item->branch}}</td>
                                    <td>{{$item->from}}</td>
                                    <td>{{$item->to}}</td>
                                    <td>{{number_format($item->amount)}}</td>
                                    <td>{{$item->description}}</td>
                                    <td>{{$item->created_at}}</td>
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

</script>

@endsection
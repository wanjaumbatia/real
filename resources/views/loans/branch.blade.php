@extends('layouts.loan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Branch Loans
                </div>
                <div class="card-body">
                    <form action="/loans_by_branch" method="get">
                        <select name="branch" id="branch" class="form-control">
                            @foreach($branches as $branch)
                            <option value="{{$branch->name}}">{{$branch->name}}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>
                    </form>

                    <div class="table-responsive mt-2">
                        <table id='table' class="table table-striped mt-2">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Appliaction Date</th>
                                    <th>Amount</th>
                                    <th>Interest</th>
                                    <th>Duration</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $item)
                                <tr>
                                    <td>{{$item->customer}}</td>
                                    <td>{{$item->created_at}}</td>
                                    <td>{{number_format($item->loan_amount, 2)}}</td>
                                    <td>{{$item->percentage}} %</td>
                                    <td>{{$item->duration}} Months</td>
                                    <td>{{number_format($item->total_balance)}}</td>
                                    <td><a href="/request_card/{{$item->id}}" class="btn btn-primary btn-sm">Open</a></td>
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
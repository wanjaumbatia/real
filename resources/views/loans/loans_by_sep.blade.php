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
                    <form class="mb-3" method="get" action="/load_seps">
                        <div class="row w-100">
                            <div class="col-9">
                                <select class="form-control w-100" name="branch" id="seps">
                                    @foreach($branches as $item)
                                    <option {{$bb == $item->name ? 'selected' : ''}} value="{{$item->name}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <button class="btn btn-primary w-100 btn-sm">
                                    Load
                                </button>
                            </div>
                        </div>
                    </form>

                    <form method="get" action="/loan_by_sep">
                        <div class="row w-100">
                            <div class="col-9">
                                <input type="text" value="{{$bb}}" hidden name="bb">
                                <select class="form-control w-100" name="name" id="branch">
                                    @foreach($seps as $item)
                                    <option {{$sep == $item->name ? 'selected' : ''}} value="{{$item->name}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <button class="btn btn-primary w-100 btn-sm">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr />
                    <div class="table-responsive">
                        <table id='table' class="table table-striped mt-2">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Start Date</th>
                                    <th>Exit Date</th>
                                    <th>Amount</th>
                                    <th>Interest</th>
                                    <th>Duration</th>
                                    <th>Paid Amount</th>
                                    <th>Status</th>
                                    <th>Branch</th>
                                    <th>Count Down</th>
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
                                    @if($item->loan_status == 'BAD')
                                    <td>
                                        <p class="text-danger font-weight-bold">{{$item->loan_status}}</p>
                                    </td>
                                    @elseif(($item->loan_status == 'EXPIRED'))
                                    <td>
                                        <p class="font-weight-bold" style="color: #f5b942">{{$item->loan_status}}</p>
                                    </td>
                                    @else
                                    <td>
                                        <p class="text-primary font-weight-bold">{{$item->loan_status}}</p>
                                    </td>
                                    @endif
                                    <td>{{$item->branch}}</td>
                                    <td>
                                        @if($item->countdown <0) <p class="text-danger">{{$item->countdown}}</p>
                                            @else
                                            <p class="text-success">{{$item->countdown}}</p>
                                            @endif
                                    </td>
                                    <td><a href="/loan_review/{{$item->id}}" class="btn btn-primary btn-sm">Review</a></td>
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
        $('#seps').select2();
        $('#branch').select2();
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
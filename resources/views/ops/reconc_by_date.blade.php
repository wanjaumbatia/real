@extends('layouts.operations')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <form action="/admin_recon" method="GET">
                                <div class="row">
                                    <div class="col-8">
                                        <select name="branch" id="branch" class="form-control">
                                            <option value="all">All</option>
                                            @foreach($branches as $item)
                                            <option value="{{$item->name}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-primary w-100">Sumbit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12 mt-2 m-2">
                            Reconciled Records
                        </div>
                    </div>


                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-stripped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Deposits</th>
                                    <th>Withdrawals</th>
                                    <th>Commission</th>
                                    <th>Registration</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{$item['date']}}</td>
                                    <td><a href="/recon_statement?type=savings&&date={{$item['date']}}">{{number_format($item['deposits'], 0)}}</a></td>
                                    <td><a href="/recon_statement?type=withdrawal&&date={{$item['date']}}">{{number_format($item['withdrawals'], 0)}}</a></td>
                                    <td><a href="/recon_statement?type=charge&&date={{$item['date']}}">{{number_format($item['charges'], 0)}}</a></td>
                                    <td><a href="/recon_statement?type=registration&&date={{$item['date']}}">{{number_format($item['regfees'], 0)}}</a></td>
                                    <!-- <td><a style="text-decoration: none;" href="/reconciled/{{$item['date']}}">Open</a></td> -->
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
    // $(document).ready(function() {
    //     $('#table').DataTable({
    //         "paging": true,
    //         "ordering": true,
    //         "info": true
    //     });
    // });
</script>
@endsection
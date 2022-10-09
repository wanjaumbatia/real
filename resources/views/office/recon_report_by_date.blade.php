@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Reconciled Records</div>
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
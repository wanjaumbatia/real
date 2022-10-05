@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        Reconciliantion Report
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sales Executive</th>
                                <th>Expected Amount</th>
                                <th>Submited Amount</th>
                                <th>Shortage</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recon as $item)
                            <tr>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->handler}}</td>
                                <td>{{number_format($item->expected, 2)}}</td>
                                <td>{{number_format($item->submited, 2)}}</td>
                                <td>
                                    @if($item->shortage==1)
                                    Yes
                                    @else
                                    No
                                    @endif
                                </td>
                                <td>
                                    <a href="/recon_per_ref/{{$item->reconciliation_reference}}" class="btn btn-primary btn-sm">View</a>
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
@endsection
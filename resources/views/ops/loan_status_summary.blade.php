@extends('layouts.operations')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-9">
            <div class="card">
                <div class="card-header">
                    Loan Status Summary
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <div class="card">
                                <div class="card-header">Active Loans</div>
                                <div class="card-body">
                                    Number : {{$data['active']}}
                                    <hr>
                                    Amount:{{number_format($data['active_amount'])}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="card">
                                <div class="card-header">Expired Loans</div>
                                <div class="card-body">
                                    Number : {{$data['expired']}}
                                    <hr>
                                    Amount:{{number_format($data['expired_amount'])}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="card">
                                <div class="card-header">Bad Loans</div>
                                <div class="card-body">
                                    Number : {{$data['bad']}}
                                    <hr>
                                    Amount:{{number_format($data['bad_amount'])}}
                                </div>
                            </div>
                        </div>
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
@extends('layouts.loan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Dashboard
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-2">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">Total Loans</div>
                                        <div class="col-6 text-end"></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="w-100">
                                        {{number_format($loan_totals->total_loan_amount, 0)}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-2">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">Active Loans</div>
                                        <div class="col-6 text-end"></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{number_format($loan_totals->total_loan_amount_active, 0)}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-2">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">Expired Loans</div>
                                        <div class="col-6 text-end"></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{number_format($loan_totals->total_loan_amount_expire, 0)}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-2">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-8">Bad/Doubtful Loans</div>
                                        <div class="col-4 text-end"></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{number_format($loan_totals->total_loan_amount_bad, 0)}}
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
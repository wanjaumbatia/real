@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">Withdrawal</div>
                    </div>
                </div>

                <div class="card-body">
                    <h1>Withdrawals</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;


    });
</script>
@endsection
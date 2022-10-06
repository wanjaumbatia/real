@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <p class="font-weight-bold">{{ $customer->name }} </p>
                        </div>
                        <div class="col-12">
                            Monthy Balance : {{number_format(($loan->total_monthly_payment - $loan->total_monthly_paid), 0)}}
                        </div>
                        <div class="col-12">
                            Total Balance : {{number_format($loan->total_balance, 0)}}
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id='loan-repay-form' method="post">
                        <input value="{{$loan->id}}" hidden name="id" id="loan_no" />
                        <input value="{{$customer->id}}" hidden name="id" id="id" />
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="number" class="form-control" name="amount" id="amount" required>
                        </div>

                        <button id="submit" class="btn btn-primary w-100 mt-2">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;
        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });

        $('#loan-repay-form').submit((e) => {
            e.preventDefault();
            var id = $('#id').val();
            var loan_no = $('#loan_no').val();
            var amount = $('#amount').val();

            var data = {
                loan_no: loan_no,
                amount: amount
            };

            fetch("/loan_repayment", {
                    method: 'post',
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify(data),
                })
                .then(results => results.json())
                .then((data) => {
                    if (data.success = true) {
                        swal("Success!", "Loan Repayed!", "success", {
                            buttons: {
                                catch: {
                                    text: "Ok",
                                    value: "ok",
                                },
                            },
                        }).then((value) => {
                            switch (value) {
                                case "ok":
                                    window.location.replace("/customer/" + id);
                                    break;
                            }
                        });;
                    } else {
                        swal("Failed!", "Loan repayment was unsuccessfull", "error");
                    }
                })
                .catch(error => console.error(error));
        });



    });
</script>
@endsection
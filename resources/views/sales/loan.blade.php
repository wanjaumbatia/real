@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">{{ $customer->name }}</div>
                    </div>

                </div>

                <div class="card-body">
                    <form id='loan-form' action="/loan/{{$customer->id}}" method="post">
                        <input value="{{$customer->id}}" hidden name="id" id="id" />
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="number" class="form-control" name="amount" id="amount" required>
                        </div>

                        <div class="form-group mt-2">
                            <label for="">Duration (months)</label>
                            <input type="number" class="form-control" name="duration" id="duration" required>
                        </div>

                        <div class="form-group mt-2">
                            <label for="">Purpose</label>
                            <input type="text" class="form-control" name="purpose" id="purpose" required>
                        </div>

                        <button id="submit" class="btn btn-primary w-100 mt-2">Apply Loan</button>
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

        $('#loan-form').submit((e) => {
            e.preventDefault();
            var id = $('#id').val();
            var amount = $('#amount').val();
            var duration = $('#duration').val();
            var purpose = $('#purpose').val();

            var data = {
                id: id,
                amount: amount,
                duration: duration,
                purpose: purpose
            };

            fetch("/loan", {
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
                    if (data.success == true) {
                        swal("Success!", data.message, "success", {
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
                        swal("Failed!", data.message, "error");
                    }
                })
                .catch(error => console.error(error));
        });
    });
</script>
@endsection
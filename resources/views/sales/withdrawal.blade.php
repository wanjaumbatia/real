@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">{{ $customer->name }} - {{$account->plan}}</div>
                    </div>
                </div>

                <div class="card-body">
                    <input value="{{$account->id}}" hidden name="id" id="id" />
                    <form id='withdrawal-form' method="post">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="number" class="form-control" id="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="">Commission</label>
                            <input type="number" class="form-control" id="commission" required>
                        </div>


                        <div class="form-group">
                            <label for="">Transfer Type</label>
                            <select class="form-control" id="payment" required>
                                <option></option>
                                <option value="Office Admin">Office Admin</option>
                                <option value="Pay On Field">Pay On Field</option>
                            </select>
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

        $('#withdrawal-form').submit((e) => {
            e.preventDefault();
            var id = $('#id').val();
            var amount = $('#amount').val();
            var commission = $('#commission').val();
            var payment = $('#commission').val();

            var data = {
                id: id,
                amount: amount,
                commission: commission,
                payment,
            };
            console.log(data);
            fetch("/post_withdrawal", {
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
                    console.log(data);
                    if (data.success == true) {
                        swal("Enter OTP:", {
                                content: "input",
                            })
                            .then((value) => {
                                var data1 = {
                                    payment: payment,
                                    otp: value.trim()
                                }
                                fetch("/verify", {
                                        method: 'post',
                                        headers: {
                                            "Content-Type": "application/json",
                                            "Accept": "application/json, text-plain, */*",
                                            "X-Requested-With": "XMLHttpRequest",
                                            "X-CSRF-TOKEN": csrfToken
                                        },
                                        body: JSON.stringify(data1),
                                    })
                                    .then(results => results.json())
                                    .then((data) => {
                                        if (data.success = true) {
                                            swal("Success!", "Withdrawal completed successfully", "success", {
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
                                            swal("Failed!", "An error occured, please again later.", "error");
                                        }
                                    })
                                    .catch(error => console.error(error));
                            });
                        // swal("Success!", "Withdrawal posted successfully", "success", {
                        //     buttons: {
                        //         catch: {
                        //             text: "Ok",
                        //             value: "ok",
                        //         },
                        //     },
                        // }).then((value) => {
                        //     switch (value) {
                        //         case "ok":
                        //             window.location.replace("/customer/" + id);
                        //             break;
                        //     }
                        // });;
                    } else {
                        swal("Failed!", data.message, "error");
                    }
                })
                .catch(error => console.error(error));
        });
    });
</script>
@endsection
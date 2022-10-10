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
                    <input value="{{$customer->id}}" hidden name="id" id="id" />
                    <form id='collection-form'>
                        @foreach ($accounts as $acc)
                        <div class="form-group">
                            <label for="">{{$acc->name}}</label>
                            <input type="number" class="form-control" name="{{$acc->id}}" id="{{$acc->id}}" required>
                        </div>
                        @endforeach
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

        $('#collection-form').submit((e) => {
            e.preventDefault();
            console.log('paying');
            var id = $('#id').val();
            var items = $('#collection-form').serializeArray()
            var transactions = [];

            items.forEach(element => {
                transactions.push({
                    "id": element.name,
                    "amount": element.value
                });
            });

            var data = {
                "transactions": transactions,
                "longitude": 77.4977,
                "latitude": 27.2046
            }

            console.log(JSON.stringify(data));


            fetch("/pay", {
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
                        swal("Success!", "Transaction posted successfully", "success", {
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
    });
</script>
@endsection
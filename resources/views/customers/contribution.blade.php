@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            Make Contribution for {{$customer['name']}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div>
                        @csrf
                        <div class="form-group">
                            <label for="name">Customer Number</label>
                            <input id="no" type="text" class="form-control disabled" hidden name="no" value="{{$customer['no']}}" />
                            <input type="text" class="form-control disabled" disabled value="{{$customer['no']}}" />
                        </div>

                        <div class="form-group">
                            <label for="name">Customer Name</label>
                            <input id="name" type="text" class="form-control" name="name" disabled value="{{$customer['name']}}" />
                        </div>

                        <div class="form-group mt-2">
                            <label for="phone">Amount</label>
                            <input id="amount" type="number" class="form-control" name="amount" />
                        </div>

                        <div class="form-group mt-2">
                            <button id="submit" class="btn btn-primary w-100">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#submit').click(function(e) {
            e.preventDefault();
            var amount = $("#amount").val();
            var no = $("#no").val();
            var name = $("#name").val();
            if (amount == null || amount == undefined || amount == 0) {
                swal("Invalid Amount!", {
                    icon: "success",
                })
            } else {
                swal({
                        title: "Confirm Posting?",
                        text: "Are you sure you want to post N." + amount + " to " + name + " account?",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.post('/customers/contribute', {
                                    '_token': $('meta[name=csrf-token]').attr('content'),
                                    task: 'comment_insert',
                                    no: no,
                                    amount: amount,
                                })
                                .error(
                                    swal("Payment not posted!", {
                                        icon: "error"
                                    })
                                )
                                .success(
                                    swal("Posting sucessfull!", {
                                        icon: "success",
                                    })
                                );

                        } else {
                            // swal("You have canceled this posting!", {
                            //     icon: "error"
                            // });
                        }
                    });
            }
        })
    })
</script>
@endsection
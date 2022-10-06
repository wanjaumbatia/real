@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">New customer</div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="/new_customer" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="">Customer Name</label>
                            <input type="text" class="form-control" name="name" required />
                        </div>
                        <div class="form-group">
                            <label for="">Phone Number</label>
                            <input type="text" class="form-control" name="phone" />
                        </div>
                        <div class="form-group">
                            <label for="">Email Address</label>
                            <input type="email" class="form-control" name="email" />
                        </div>
                        <div class="form-group">
                            <label for="">Town</label>
                            <input type="text" class="form-control" name="town" />
                        </div>
                        <div class="form-group">
                            <label for="">Address</label>
                            <input type="text" class="form-control" name="address" />
                        </div>
                        <div class="form-group">
                            <label for="">Gender</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="Male">Male</option>
                                <option value="Male">Female</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Business</label>
                            <select name="business" class="form-control">
                                <option></option>
                                <option value="Petty Traders">Petty Traders</option>
                                <option value="Provision Shop/Supermarket/superstores">Provision Shop/Supermarket/superstores</option>
                                <option value="Fashion/Boutique">Fashion/Boutique</option>
                                <option value="Transporters">Transporters</option>
                                <option value="Medicals">Medicals</option>
                                <option value="Technician">Technician</option>
                                <option value="Professional">Professional</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Bank</label>
                            <select id='bank_select' name="bank" class="form-control">
                                @foreach($banks as $item)
                                <option value="{{$item}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Bank Account</label>
                            <input type="number" class="form-control" name="account">
                        </div>

                        <div class="form-group">
                            <label for="">Bank Branch</label>
                            <input type="text" class="form-control" name="bank_branch">
                        </div>

                        <button class="btn btn-primary w-100" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#plan_modal").click(function() {
            $('#modal').modal('show');
        });

        $('#bank_select').select2()

        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection
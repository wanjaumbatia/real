@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">New Real Invest</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="form_group">
                            <label for="">Customer Type</label>
                            <select onchange="setCustomer()" name="isCustomer" id="isCustomer" class="form-control">
                                <option value="1">New Customer</option>
                                <option value="2">Old Customer</option>
                            </select>
                        </div>
                        <form id='new_customer' class="mt-3" action="/create_real_invest" method="post">
                            @csrf
                            <input type="number" value="1" name="type" hidden>
                            <div class="form-group">
                                <label for="">Customer Name</label>
                                <input type="text" class="form-control" name="name" />
                            </div>

                            <div class="form-group">
                                <label for="">Customer Phone</label>
                                <input type="text" class="form-control" name="phone" />
                            </div>

                            <div class="form-group">
                                <label for="">Customer Address</label>
                                <input type="text" class="form-control" name="address" />
                            </div>

                            <div class="form-group">
                                <label for="">Deposit Target</label>
                                <input type="number" class="form-control" name="amount" />
                            </div>

                            <div class="form-group">
                                <label for="">Tenure</label>
                                <select name="tenure" id="isCustomer" class="form-control">
                                    <option value="6">6 Months 10%</option>
                                    <option value="12">12 Months 22%</option>
                                    <option value="12">18 Months 35%</option>
                                    <option value="12">24 Months 50%</option>
                                </select>
                            </div>

                            <button class="btn btn-primary w-100">Submit</button>
                        </form>

                        <form id='old_customer' class="mt-3" action="/create_real_invest" method="post">
                            @csrf
                            <input type="number" value="2" name="type" hidden>
                            <div class="form-group">
                                <label for="">Select Customer</label>
                                <select name="customer" class="form-control" id="customer_select">
                                    @foreach($customers as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Deposit Target</label>
                                <input type="number" class="form-control" name="amount" />
                            </div>

                            <div class="form-group">
                                <label for="">Tenure</label>
                                <select name="tenure" id="isCustomer" class="form-control">
                                    <option value="6">6 Months 10%</option>
                                    <option value="12">12 Months 22%</option>
                                    <option value="12">18 Months 35%</option>
                                    <option value="12">24 Months 50%</option>
                                </select>
                            </div>

                            <button class="btn btn-primary w-100">Submit</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setCustomer() {
        if ($('#isCustomer').val() == '1') {
            //new customer
            $('#new_customer').show();
            $('#old_customer').hide();
        } else if ($('#isCustomer').val() == '2') {
            //old customer
            $('#new_customer').hide();
            $('#old_customer').show();
        }
    }

    $(document).ready(function() {
        $('#new_customer').show();
        $('#old_customer').hide();
        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });
        $('#customer_select').select2();
    });
</script>
@endsection
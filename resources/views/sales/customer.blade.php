@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">{{$customer->name}}</div>
                    </div>

                </div>

                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label for="">Customer Number</label>
                                    <input type="text" class="form-control" value="{{$customer->no}}">
                                </div>
                                <div class="form-group">
                                    <label for="">Address</label>
                                    <input type="text" class="form-control" value="{{$customer->address}}">
                                </div>

                                <div class="form-group">
                                    <label for="">Phone Number</label>
                                    <input type="text" class="form-control" value="{{$customer->phone}}">
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">Regular</div>
                            </div>

                        </div>

                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label for="">Balance</label>
                                    <input type="text" class="form-control" value="{{number_format(10000, 2)}}">
                                </div>

                                <div class="form-group">
                                    <label for="">Pending Collections</label>
                                    <input type="text" class="form-control" value="{{number_format(10000, 2)}}">
                                </div>

                                <div class="form-group">
                                    <label for="">Pending Balance</label>
                                    <input type="text" class="form-control" value="{{number_format(10000, 2)}}">
                                </div>

                                <button class="btn btn-primary btn-sm w-100">withdraw</button>
                            </form>
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
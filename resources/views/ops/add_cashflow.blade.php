@extends('layouts.operations')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Report Cash Flow
                </div>
                <div class="card-body">
                    <form action="/post_cashflow" method="POST">
                        @csrf
                        <div class="fomr-group">
                            <label for="">Branch</label>
                            <select name="branch" id="branch" class="form-control">
                                @foreach($branches as $item)
                                <option value="{{$item->name}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="fomr-group">
                            <label for="">Date</label>
                            <input type="date" name="date" id="date" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Direction</label>
                            <select name="direction" id="direct" class="form-control">
                                <option value="1">To HQ</option>
                                <option value="2">From HQ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Description</label>
                            <input type="text" name="description" id="description" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary mt-2 w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $('#security').on('change', function() {
            //alert(this.value);
        });
    });
</script>

@endsection
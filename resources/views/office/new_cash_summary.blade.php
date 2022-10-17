@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        Post Expense
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form action="/post_cash_summary" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="">Branch</label>
                            <select name="branch" id="branch" class="form-control">
                                @foreach($branches as $item)
                                <option value="{{$item->name}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-1">
                            <label for="">Opening Balance</label>
                            <input type="number" class="form-control" name="opening_balance">
                        </div>

                        <div class="form-group mt-1">
                            <label for="">Remmittance</label>
                            <input type="number" class="form-control" name="remmittance">
                        </div>

                        <div class="form-group mt-1">
                            <label for="">Opening Balance</label>
                            <input type="number" class="form-control" name="remmitance">
                        </div>

                        <div class="form-group mt-1">
                            <label for="">Cash Inflow</label>
                            <input type="number" class="form-control" name="cash_inflow">
                        </div>
                        <div class="form-group mt-1">
                            <label for="">Cash Outflow</label>
                            <textarea type="text" class="form-control" name="cash_outflow" rows="3"></textarea>
                        </div>

                        <button class="btn btn-primary w-100 mt-2">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $('type').select2();
    $(document).ready(function() {
        $('#select_item').select2();
        $('#table').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection
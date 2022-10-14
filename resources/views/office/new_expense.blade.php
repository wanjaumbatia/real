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

                    <form action="">
                        <div class="form-group">
                            <label for="">Branch</label>
                            <input type="text" class="form-control" value="{{Auth::user()->branch}}" disabled />
                        </div>

                        <div class="form-group mt-1">
                            <label for="">Description</label>
                            <input type="text" class="form-control" name="description">
                        </div>

                        <div class="form-group mt-1">
                            <label for="">Amount</label>
                            <input type="text" class="form-control" name="amount">
                        </div>
                        <div class="form-group mt-1">
                            <label for="">Remarks</label>
                            <textarea type="text" class="form-control" name="remarks" rows="3"></textarea>
                        </div>
                        <div class="form-group mt-1">
                            <label for="">Created By</label>
                            <input type="text" class="form-control" value="{{Auth::user()->name}}" disabled>
                        </div>

                        <button class="btn btn-primary w-100 mt-2">Submit</button>
                    </form>
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
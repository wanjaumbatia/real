@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            {{ __('Previous Target for Asaba') }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <form method="post" action="{{route('admin.targets.store')}}">
                        @csrf
                        <input hidden name="branch" value="{{$branch}}" />
                        <div class="form-group">
                            <label>Previous Target:</label>
                            <input type="number" disabled value="{{$prev_target}}" class="form-control" />
                        </div>
                        <div class="form-group mt-2">
                            <label>New Target:</label>
                            <input type="number" class="form-control" name="amount" />
                        </div>

                        <div class="form-group mt-3">
                            <button class="btn btn-primary w-100">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
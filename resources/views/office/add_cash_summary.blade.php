@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            {{ __('Cash Summary Form') }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form action="/save_summary" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="branch" class="form-control">
                                    @foreach($branches as $item)
                                    <option value="{{$item->name}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Opening Balance</label>
                                <input type="number" class="form-control" name="opening_balance">
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <input name="date" type="date" class="form-control" name="opening_balance">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
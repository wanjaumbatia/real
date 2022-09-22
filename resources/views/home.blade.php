@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="/import" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Import Users</label>
                                    <input required class="form-control" type="file" name="file" />
                                </div>
                                <button class="btn btn-primary w-100 mt-2">Upload</button>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <form method="post" action="/import_customers" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Import Customers</label>
                                    <input required class="form-control" type="file" name="file" />
                                </div>
                                <button class="btn btn-primary w-100 mt-2">Upload</button>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <form method="post" action="/import_loans" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Import Loans</label>
                                    <input required class="form-control" type="file" name="file" />
                                </div>
                                <button class="btn btn-primary w-100 mt-2">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
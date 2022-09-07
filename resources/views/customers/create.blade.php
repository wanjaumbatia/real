@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            {{ __('New Customer') }}
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
                    <form method="post" action="{{route('customers.store')}}">
                        @csrf
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" name="name" />
                        </div>

                        <div class="form-group mt-2">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" name="phone" />
                        </div>

                        <div class="form-group mt-2">
                            <label for="town">Town</label>
                            <input type="text" class="form-control" name="town" />
                        </div>

                        <div class="form-group mt-2">
                            <label for="gender">Gender</label>
                            <input type="text" class="form-control" name="gender" />
                        </div>

                        <div class="form-group mt-2">
                            <label for="gender">Date of Birth</label>
                            <input type="date" class="form-control" name="dob" />
                        </div>

                        <div class="form-group mt-2">
                            <label for="gender">Address</label>
                            <input type="text" class="form-control" name="address" />
                        </div>
                        <div class="form-group mt-2">
                            <label for="gender">Type of Business</label>
                            <input type="text" class="form-control" name="business" />
                        </div>

                        <div class="form-group mt-2">
                            <label for="gender">Bank</label>
                            <input type="text" class="form-control" name="bank" />
                        </div>

                        <div class="form-group mt-2">
                            <label for="gender">Bank Account Number</label>
                            <input type="text" class="form-control" name="bankacc" />
                        </div>

                        <div class="form-group mt-2">
                            <button class="btn btn-primary w-100">Create Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
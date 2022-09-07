@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            Create New User
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{route('admin.team.list')}}" class="btn-primary btn btn-sm">Back</a>
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
                    <form method="post" action="{{route('admin.team.store')}}">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Name:</label>
                                    <input name="name" type="text" class="form-control" value="{{old('name')}}" />

                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Phone Number:</label>
                                    <input name="phone" type="text" class="form-control" value="{{old('phone')}}" />
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Username:</label>
                                    <input name="username" type="text" class="form-control" value="{{old('username')}}" />

                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Role:</label>
                                    <select name="role" class="form-control" value="{{old('role')}}">
                                        @foreach($roles as $role)
                                        <option value="{{$role->name}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Branch:</label>
                                    <select name="branch" class="form-control" value="{{old('branch')}}">
                                        @foreach($branches as $branch)
                                        <option value="{{$branch->name}}">{{$branch->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select name="active" class="form-control" value="{{old('active')}}">
                                        <option value="true">Active</option>
                                        <option value="false">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary mt-3 w-100">Update</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
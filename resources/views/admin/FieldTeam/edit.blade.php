@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            {{$user->name}}
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
                    <form>
                        @csrf
                        <div class="row">
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Name:</label>
                                    <input hidden name="id" value="{{$user->id}}" />
                                    <input name="name" type="text" class="form-control" value="{{$user->name}}" />
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Phone Number:</label>
                                    <input name="phone" type="text" class="form-control" value="{{$user->phone}}" />
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Username:</label>
                                    <input name="username" type="text" class="form-control" value="{{$user->email}}" />
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Branch:</label>
                                    <input name="branch" type="text" class="form-control" value="{{$user->branch}}" />
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Role:</label>
                                    <select name="branch" class="form-control">
                                        <option>Sales Executiver</option>
                                        <option>Office Administrator</option>
                                        <option>Assistant Branch Manager</option>
                                        <option>Branch Manager</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select name="active" class="form-control">
                                        <option>Active</option>
                                        <option>Inactive</option>
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
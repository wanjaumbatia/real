@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            {{ __('Application Users') }}
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{route('admin.team.create')}}" class="btn-primary btn btn-sm">New User</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <table class="table table-striped">
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th>branch</th>
                            <th>Created On</th>
                            <th></th>
                        </tr>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{$item->name}}</td>
                            <td>{{$item->email}}</td>
                            <td>{{$item->phone}}</td>
                            <td>{{$item->branch}}</td>
                            <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
                            <td>
                                <a href="{{route('admin.team.edit' ,$item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
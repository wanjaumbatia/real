@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            Total Customers - {{count($customers)}}
                        </div>
                        @if(Auth::user()->sales_executive==true)
                        <div class="col-4 text-end">
                            <a href="/customers/create" class="btn btn-primary btn-sm">Create New</a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Branch</th>
                                <th>Handler</th>
                                <th>Registered Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $item)
                            <tr>
                                <td>{{$item['no']}}</td>
                                <td>{{$item['name']}}</td>
                                <td>{{$item['phone']}}</td>
                                <td>{{$item['branch']}}</td>
                                <td>{{$item['handler']}}</td>                                
                                <td>{{date('d-m-Y', strtotime($item['created_by']))}}</td>  
                                <td><a href="/customers/show/{{$item['id']}}" class="btn btn-primary btn-sm">Open</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$customers->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
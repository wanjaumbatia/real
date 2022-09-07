@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            {{ __('Branch Target') }}
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
                        <thead>
                            <tr>
                                <th>Branch Name</th>
                                <th>Target</th>
                                <th>Archieved</th>
                                <th>Deficit</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                                <tr>
                                    <td>{{$item['branch']}}</td>
                                    <td>{{$item['target']}}</td>
                                    <td></td>
                                    <td></td>
                                    <td align="right"><a href="/admin/branch_targets/create/{{$item['branch']}}" class="btn btn-sm btn-primary">Create Target</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
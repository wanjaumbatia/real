@extends('layouts.app')

@section('content')
<div class="container">

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{ __('Plans') }}
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Branch Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                    <tr>
                        <td>
                            <a style="text-decoration: none;" href="/sep/{{$branch->name}}">{{$branch->name}}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
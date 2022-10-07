@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{ __('Import Loans') }}
                </div>
            </div>
        </div>  
        <div class="card-body">
            <form method="post" action="/import_loans_new" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Select File</label>
                    <input required class="form-control" type="file" name="file" required/>
                </div>
                <button class="btn btn-primary w-100 mt-2">Upload</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{ __('Branches') }}
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
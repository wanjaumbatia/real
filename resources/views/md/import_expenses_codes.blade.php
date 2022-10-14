@extends('layouts.md')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Import Expenses Codes
                </div>
                <div class="card-body">
                    <form action="post_expense_code_excel" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="">Import Excel</label>
                            <input type="file" name="file" id="file" class="form-control">
                        </div>
                        <button class="btn btn-primary btn-block w-100 mt-2">Submit</button>
                    </form>

                    <table class="table table-stripped mt-3">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($codes as $item)
                            <tr>
                                <td>{{$item->expense_type}}</td>
                                <td>{{$item->category}}</td>
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
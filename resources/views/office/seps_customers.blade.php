@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{ __('Customers') }}
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table" id="table">
                <thead>
                    <tr>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>
                            <a style="text-decoration: none;" href="/sep_customer/{{$customer->id}}">{{$customer->name}}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "paging": true,
                "ordering": true,
                "info": true
            });
        });
    </script>
    @endsection
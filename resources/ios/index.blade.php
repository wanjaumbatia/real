@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        <h3 class="font-weight-bold">Customers</h3>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $item)
                            <tr>
                                <td><a href="/ios/customer/{{$item->id}}" style="text-decoration: none;">{{$item->name}}</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            "paging": false,
            "ordering": false,
            "info": false
        });
    });
</script>
@endsection
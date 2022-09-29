@extends('layouts.sales')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Start Date</label>
                        <input type="date" class="form-control" id="min" name="min">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">End Date</label>
                        <input type="date" class="form-control" id="max" name="max">
                    </div>
                </div>
            </div>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->customer_name}}</td>
                        <td>{{$item->plan}}</td>
                        <td>{{number_format($item->debit, 2)}}</td>
                        <td>{{$item->status}}</td>
                        <td>{{$item->remarks}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    var minDate, maxDate;

    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var min = minDate.val();
            var max = maxDate.val();
            var date = new Date(data[4]);

            if (
                (min === null && max === null) ||
                (min === null && date <= max) ||
                (min <= date && max === null) ||
                (min <= date && date <= max)
            ) {
                return true;
            }
            return false;
        }
    );

    $(document).ready(function() {
        // Create date inputs
        minDate = new DateTime($('#min'), {
            format: 'MMMM Do YYYY'
        });
        maxDate = new DateTime($('#max'), {
            format: 'MMMM Do YYYY'
        });

        // DataTables initialisation
        var table = $('#table').DataTable();

        // Refilter the table
        $('#min, #max').on('change', function() {
            table.draw();
        });
    });
</script>
@endsection
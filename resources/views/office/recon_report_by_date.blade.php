@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row font-weight-bold">
                        Reconciliantion Report
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="">Select Year</label>
                                <select onchange="rangeChanged()" name="year" id="year" class="form-control">
                                    <option selected value="2022">2022</option>
                                    <option value="2021">2021</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="">Select Month</label>
                                <select onchange="rangeChanged" name="month" id="month" class="form-control">
                                    <option selected value="0">January</option>
                                    <option value="1">Febuary</option>
                                    <option value="2">March</option>
                                    <option value="3">April</option>
                                    <option value="4">May</option>
                                    <option value="5">June</option>
                                    <option value="6">July</option>
                                    <option value="7">August</option>
                                    <option value="8">September</option>
                                    <option value="9">October</option>
                                    <option value="10">November</option>
                                    <option value="11">December</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{$item['date']}}</td>
                                <td>{{number_format($item['amount'], 2)}}</td>
                                <td><a style="text-decoration: none;" href="/recon_statement?date= {{$item['date']}}">Open</a></td>
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
    const now = new Date();

    function getAllDaysInMonth(year, month) {
        const date = new Date(year, month, 1);

        const dates = [];

        while (date.getMonth() === month) {
            dates.push(new Date(date));
            date.setDate(date.getDate() + 1);
        }

        return dates;
    }

    function rangeChanged() {
        // 👇️ all days of the current month
        console.log(getAllDaysInMonth(now.getFullYear(), now.getMonth()));

        const date = new Date('2022-02-24');

        // 👇️ All days in March of 2022
        console.log(getAllDaysInMonth(date.getFullYear(), date.getMonth()));
    }
</script>
@endsection
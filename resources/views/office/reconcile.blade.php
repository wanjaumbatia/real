@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Reconcile Collection') }}</span></div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="post" action="{{route('office.receive')}}">
                        @csrf
                        <div class="form-group ">
                            <input name="handler" hidden value="{{$handler}}" />
                            <label for="amount">Expected Amount</label>
                            <input type="text" class="form-control" disabled value="₦.{{number_format($total,0)}}" />
                        </div>

                        <div class="form-group">
                            <label for="amount">Received Amount</label>
                            <input type="number" class="form-control" name="amount" />
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary w-100 my-2">Confirm</button>
                        </div>
                    </form>


                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Sales Executive</th>
                                <th>Branch</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td>₦. {{number_format($item->amount,0)}}</td>
                                <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
                                <td>{{$item->handler}}</td>
                                <td>{{$item->branch}}</td>
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
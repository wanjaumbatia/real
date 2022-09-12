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

                    <form method="post" action="{{route('office.disburse')}}">
                        @csrf
                        <div class="form-group ">
                            <input name="id" hidden value="{{$transaction->id}}" />
                            <label for="amount">Disbument Amount</label>
                            <input type="text" class="form-control" disabled value="₦.{{number_format($transaction->credit,0)}}" />
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary w-100 my-2">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
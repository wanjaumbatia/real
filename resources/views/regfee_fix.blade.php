@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{ __('Regestration Fee Fix') }}
                </div>
            </div>
        </div>
        <div class="card-body">
            @foreach($data as $item)
            {{$item}}
            @endforeach
        </div>
    </div>
    @endsection
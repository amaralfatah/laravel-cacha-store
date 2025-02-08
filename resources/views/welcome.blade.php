@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Welcome to the Laravel 11</h1>
                {{ Auth::user()->role }}
            </div>
        </div>
    </div>
@endsection

@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-7 mb-4">
            @livewire('skill-form')
        </div>
        <div class="col-lg-5">
            @livewire('skill-table')
        </div>
    </div>
@endsection 
@extends('layouts.dashboard')

@section('content')
    @livewire('project-form', ['projectId' => request()->route('project')])
@endsection 
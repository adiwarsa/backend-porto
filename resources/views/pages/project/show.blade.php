@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>{{ __('menu.project') }}</h5>
            <div>
                <a href="{{ route('project.index') }}"
                   class="btn btn-outline-secondary">{{ __('button.back') }}</a>
                @can('edit_project')
                <a href="{{ route('project.edit', $item->id) }}"
                   class="btn btn-primary">{{ __('button.edit') }}</a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.title') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->title }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.type') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->type }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.author') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->author }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.date') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->date }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.status') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->status }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.image') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->image }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.gradient') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->gradient }}">
                </div>
                <div class="mb-3 col-md-12">
                    <label class="form-label">{{ __('field.description') }}</label>
                    <textarea class="form-control-plaintext" readonly>{{ $item->description }}</textarea>
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.technologies') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ is_array($item->technologies) ? implode(', ', $item->technologies) : $item->technologies }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.features') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ is_array($item->features) ? implode(', ', $item->features) : $item->features }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.liveUrl') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->liveUrl }}">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ __('field.githubUrl') }}</label>
                    <input type="text" class="form-control-plaintext" readonly value="{{ $item->githubUrl }}">
                </div>
            </div>
        </div>
    </div>
@endsection 
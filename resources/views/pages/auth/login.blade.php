@extends('layouts.app')

@section('body')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Login -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{ route('welcome') }}" class="app-brand-link gap-2">
                                
                                <span class="app-brand-text text-body fw-bold fs-3">{{ config('app.name') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        
                        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <x-forms.input name="email" :value="null" type="email"/>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                
                                <div class="input-group input-group-merge">
                                    <input
                                        type="password"
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password"/>
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    <span class="error invalid-feedback">{{ $errors->first('password') }}</span>
                                </div>
                            </div>
                           
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100"
                                        type="submit">{{ __('button.login') }}</button>
                            </div>
                        </form>

                        
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}"/>
@endpush

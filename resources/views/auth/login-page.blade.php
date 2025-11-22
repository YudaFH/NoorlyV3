@extends('layouts.auth')

@section('title', 'Masuk | Noorly')

@section('content')
  <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-4 py-16">
    @livewire('auth.login-form')
  </div>
@endsection

{{-- resources/views/auth/register-page.blade.php --}}
@extends('layouts.auth')

@section('title', 'Daftar | Noorly')

@section('content')
  <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-4 py-16">
    @livewire('auth.register-form')
  </div>
@endsection

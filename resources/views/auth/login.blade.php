@extends('layouts.app')

@section('title', 'Iniciar sesión · Sr WOK CallCenter')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#FFEBEE]">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-sm p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-[#C62828]">Sr WOK</h1>
            <p class="text-gray-500 text-sm mt-1">CallCenter · Acceso interno</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828] @error('email') border-red-400 @enderror" />
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828]" />
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Extensión</label>
                <input type="number" name="extension" value="{{ old('extension') }}" min="0" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828] @error('extension') border-red-400 @enderror" />
                @error('extension')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded">
                <label for="remember" class="text-sm text-gray-600">Recordarme</label>
            </div>
            <button type="submit"
                class="w-full bg-[#C62828] hover:bg-[#B71C1C] text-white font-semibold py-2 rounded-lg transition text-sm">
                Ingresar
            </button>
        </form>
    </div>
</div>
@endsection

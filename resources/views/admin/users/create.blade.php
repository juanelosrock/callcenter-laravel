@extends('layouts.panel')

@section('title', 'Nuevo usuario · Sr WOK CallCenter')

@section('content')
<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>
        <h1 class="text-xl font-bold text-gray-800">Nuevo usuario</h1>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828] @error('name') border-red-400 @enderror" />
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828] @error('email') border-red-400 @enderror" />
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" name="password" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828]" />
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828]" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Perfil</label>
            <select name="role" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828]">
                <option value="">Selecciona un perfil</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }} class="capitalize">
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="active" value="1" checked class="rounded">
            <label for="active" class="text-sm text-gray-700">Usuario activo</label>
        </div>
        <div class="pt-2">
            <button type="submit"
                class="bg-[#C62828] hover:bg-[#B71C1C] text-white font-semibold px-5 py-2 rounded-lg transition text-sm">
                Crear usuario
            </button>
        </div>
    </form>
</div>
@endsection

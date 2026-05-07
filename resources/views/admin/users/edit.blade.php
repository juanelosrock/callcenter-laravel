@extends('layouts.panel')

@section('title', 'Editar usuario · Sr WOK CallCenter')

@section('content')
<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>
        <h1 class="text-xl font-bold text-gray-800">Editar usuario</h1>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828] @error('name') border-red-400 @enderror" />
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828] @error('email') border-red-400 @enderror" />
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña <span class="text-gray-400 font-normal">(dejar vacío para no cambiar)</span></label>
            <input type="password" name="password"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828]" />
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
            <input type="password" name="password_confirmation"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828]" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Perfil</label>
            <select name="role" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828]">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }} class="capitalize">
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Extensión</label>
                <input type="number" name="extension" value="{{ old('extension', $user->extension) }}" min="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828] @error('extension') border-red-400 @enderror" />
                @error('extension')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agente</label>
                <input type="text" name="agente" value="{{ old('agente', $user->agente) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C62828] @error('agente') border-red-400 @enderror" />
                @error('agente')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="active" value="1" {{ $user->active ? 'checked' : '' }} class="rounded">
            <label for="active" class="text-sm text-gray-700">Usuario activo</label>
        </div>
        <div class="pt-2">
            <button type="submit"
                class="bg-[#C62828] hover:bg-[#B71C1C] text-white font-semibold px-5 py-2 rounded-lg transition text-sm">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection

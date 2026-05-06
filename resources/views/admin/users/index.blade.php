@extends('layouts.panel')

@section('title', 'Usuarios · Sr WOK CallCenter')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-800">Usuarios</h1>
    <a href="{{ route('admin.users.create') }}"
        class="bg-[#C62828] hover:bg-[#B71C1C] text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        + Nuevo usuario
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">Nombre</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">Correo</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">Perfil</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">Estado</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    <span class="bg-[#FFEBEE] text-[#C62828] text-xs font-medium px-2 py-0.5 rounded capitalize">
                        {{ $user->getRoleNames()->first() ?? '—' }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    @if($user->active)
                        <span class="bg-green-50 text-green-700 text-xs font-medium px-2 py-0.5 rounded">Activo</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2 py-0.5 rounded">Inactivo</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-right flex gap-2 justify-end">
                    <a href="{{ route('admin.users.edit', $user) }}"
                        class="text-xs text-blue-600 hover:underline">Editar</a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                        onsubmit="return confirm('¿Eliminar a {{ $user->name }}?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-500 hover:underline">Eliminar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-8 text-center text-gray-400">No hay usuarios registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

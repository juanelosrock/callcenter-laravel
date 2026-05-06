@extends('layouts.panel')

@section('title', 'Dashboard · Sr WOK CallCenter')

@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-6">Dashboard</h1>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Bienvenido</p>
        <p class="text-lg font-bold text-[#C62828] mt-1">{{ auth()->user()->name }}</p>
        <p class="text-xs text-gray-400 capitalize mt-1">{{ auth()->user()->getRoleNames()->first() }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Acceso rápido</p>
        <a href="{{ route('pedido.nuevo') }}" class="inline-block mt-2 bg-[#C62828] hover:bg-[#B71C1C] text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            Nuevo Pedido
        </a>
    </div>
</div>
@endsection

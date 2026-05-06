<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Sr WOK - CallCenter')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="font-[Poppins] bg-gray-100 text-gray-900 antialiased">

<div class="min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-[#C62828] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
            <span class="font-bold text-lg tracking-wide">Sr WOK · CallCenter</span>
            <div class="flex items-center gap-4 text-sm">
                <span class="opacity-80">{{ auth()->user()->name }}</span>
                <span class="bg-white/20 rounded px-2 py-0.5 text-xs capitalize">{{ auth()->user()->getRoleNames()->first() }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="opacity-80 hover:opacity-100 transition">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Sidebar + Content --}}
    <div class="flex flex-1">
        <aside class="w-56 bg-white shadow-sm flex flex-col py-6 px-3 gap-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-[#FFEBEE] text-[#C62828]' : 'text-gray-600 hover:bg-gray-100' }}">
                Dashboard
            </a>
            <a href="{{ route('pedido.nuevo') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pedido.*') ? 'bg-[#FFEBEE] text-[#C62828]' : 'text-gray-600 hover:bg-gray-100' }}">
                Nuevo Pedido
            </a>
            @role('admin')
            <hr class="my-2 border-gray-200">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Administración</p>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-[#FFEBEE] text-[#C62828]' : 'text-gray-600 hover:bg-gray-100' }}">
                Usuarios
            </a>
            @endrole
        </aside>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>

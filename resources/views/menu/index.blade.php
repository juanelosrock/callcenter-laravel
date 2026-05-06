@extends('layouts.app')

@section('title', 'Sr WOK — POS')

@push('head')
<style>
    :root {
        --pos-red:       #C62828;
        --pos-red-dark:  #B71C1C;
        --pos-red-light: #FFEBEE;
        --pos-header:    #16213e;
        --pos-sidebar:   #1e293b;
    }
    html, body { height: 100%; overflow: hidden; }

    .pos-scroll::-webkit-scrollbar { width: 4px; }
    .pos-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }

    .cat-btn { transition: background .15s, color .15s; }
    .cat-active { background: var(--pos-red) !important; color: #fff !important; }

    .prod-card { transition: transform .15s ease, box-shadow .15s ease; }
    .prod-card:hover  { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.13); }
    .prod-card:active { transform: scale(.97); }

    .img-cover { width:100%; height:100%; object-fit:cover; display:block; }
    .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .line-clamp-1 { display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden; }
</style>
@endpush

@section('content')
<div x-data="menuApp()" x-init="iniciar()"
     class="flex flex-col"
     style="height:100vh; background:#f0f2f5;">

    {{-- ══════════ HEADER ══════════ --}}
    <header class="flex items-center gap-4 px-4 h-14 flex-shrink-0 shadow-lg z-20"
            style="background:var(--pos-header);">

        {{-- Logo --}}
        <div class="w-9 h-9 rounded-lg flex-shrink-0 border border-white/20 flex items-center justify-center font-bold text-white text-xs"
             style="background:var(--pos-red);">WOK</div>

        {{-- Tienda --}}
        <div class="flex-shrink-0 leading-tight">
            <p class="text-white font-bold text-sm" x-text="tienda.nombre || 'Sr WOK'"></p>
            <p class="text-gray-400 text-xs"   x-text="tienda.descripcion || 'Oriental Buffet'"></p>
        </div>

        <div class="w-px h-6 bg-white/20"></div>

        {{-- Dirección --}}
        <div class="flex items-center gap-1.5 text-xs text-gray-300 min-w-0 flex-1">
            <svg class="w-3.5 h-3.5 flex-shrink-0 text-[#C62828]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="truncate" x-text="(localStorage.getItem('nombreciudad') || '') + '  ·  ' + (localStorage.getItem('direccion') || '')"></span>
        </div>

        {{-- Estado --}}
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
            <span class="text-green-400 text-xs font-medium">Abierto</span>
        </div>

        <div class="w-px h-6 bg-white/20"></div>

        {{-- Tiempo entrega --}}
        <div class="flex items-center gap-1 text-xs text-gray-300 flex-shrink-0">
            <svg class="w-3.5 h-3.5 text-[#C62828]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-text="(tienda.tiempoEntrega || '30-45') + ' min'"></span>
        </div>

        <div class="w-px h-6 bg-white/20"></div>

        {{-- Nuevo pedido --}}
        <button @click="nuevoPedido()"
                class="flex items-center gap-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo pedido
        </button>
    </header>

    {{-- ══════════ BODY ══════════ --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- ── Sidebar categorías ── --}}
        <aside class="flex-shrink-0 flex flex-col overflow-y-auto pos-scroll py-2 px-2 gap-1"
               style="width:156px; background:var(--pos-sidebar);">

            {{-- Skeleton cargando --}}
            <template x-if="cargando">
                <div class="space-y-2 px-1 pt-1">
                    <template x-for="i in 7" :key="i">
                        <div class="h-9 bg-white/10 rounded-lg animate-pulse"></div>
                    </template>
                </div>
            </template>

            <template x-if="!cargando">
                <div class="flex flex-col gap-1">
                    {{-- Todos --}}
                    <button @click="categoriaFiltro = '0'"
                            :class="categoriaFiltro === '0' ? 'cat-active' : 'text-gray-300 hover:bg-white/10'"
                            class="cat-btn w-full text-left text-xs font-semibold px-3 py-2.5 rounded-lg flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Todos
                    </button>
                    <template x-for="cat in categorias" :key="cat.comboid">
                        <button
                            @click="categoriaFiltro = String(cat.comboid); $nextTick(() => scrollToCategory(cat.comboid))"
                            :class="categoriaFiltro === String(cat.comboid) ? 'cat-active' : 'text-gray-300 hover:bg-white/10'"
                            class="cat-btn w-full text-left text-xs font-semibold px-3 py-2.5 rounded-lg"
                            x-text="cat.combo"
                        ></button>
                    </template>
                </div>
            </template>
        </aside>

        {{-- ── Grilla de productos ── --}}
        <main class="flex-1 overflow-y-auto pos-scroll px-5 py-4">

            {{-- Skeleton --}}
            <div x-show="cargando" class="grid grid-cols-3 gap-3">
                <template x-for="i in 16" :key="i">
                    <div class="bg-white rounded-xl overflow-hidden animate-pulse shadow-sm">
                        <div class="h-28 bg-gray-200"></div>
                        <div class="p-3 space-y-2">
                            <div class="h-3.5 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="!cargando">
                <div>
                    <template x-for="cat in menuFiltrado" :key="cat.comboid">
                        <div :id="'cat-' + cat.comboid" class="mb-6">

                            {{-- Título categoría --}}
                            <div class="flex items-center gap-2 mb-3 sticky top-0 bg-[#f0f2f5] py-1 z-10">
                                <span class="w-1 h-5 rounded-full flex-shrink-0" style="background:var(--pos-red)"></span>
                                <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider" x-text="cat.combo"></h2>
                                <div class="flex-1 h-px bg-gray-200 ml-1"></div>
                                <span class="text-xs text-gray-400 flex-shrink-0" x-text="cat.productos.length + ' items'"></span>
                            </div>

                            {{-- Cards --}}
                            <div class="grid grid-cols-3 gap-3">
                                <template x-for="prod in cat.productos" :key="prod.id">
                                    <button
                                        @click="tiendaAbierta ? abrirProducto(prod, cat) : (modal = 'cerrado')"
                                        class="prod-card bg-white rounded-xl overflow-hidden text-left shadow-sm focus:outline-none"
                                    >
                                        {{-- Imagen --}}
                                        <div class="relative h-28 bg-gray-100">
                                            <img :src="prod.fotoproducto" :alt="prod.nombre"
                                                 class="img-cover"
                                                 onerror="this.onerror=null;this.style.display='none'"/>
                                            <template x-if="parseInt(prod.descuento) > 0">
                                                <span class="absolute top-1.5 left-1.5 text-[10px] font-bold bg-[#C62828] text-white px-1.5 py-0.5 rounded"
                                                      x-text="prod.descuento + '% OFF'"></span>
                                            </template>
                                        </div>
                                        {{-- Info --}}
                                        <div class="p-3">
                                            <h3 class="text-xs font-bold text-gray-900 leading-snug line-clamp-2 mb-1" x-text="prod.nombre"></h3>
                                            <p class="text-[11px] text-gray-400 line-clamp-1 mb-2" x-text="prod.descripcion || ''"></p>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-extrabold" style="color:var(--pos-red)"
                                                      x-text="'$' + formatNum(prod.precio)"></span>
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0"
                                                     style="background:var(--pos-red)">
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </main>

        {{-- ── Panel de pedido (derecha) ── --}}
        <aside class="flex-shrink-0 flex flex-col border-l border-gray-200 bg-white"
               style="width:300px;">

            {{-- ── Tarjeta cliente ── --}}
            <div class="flex-shrink-0 border-b border-gray-200"
                 style="background:#1e293b;">
                {{-- Sin cliente aún --}}
                <div x-show="!clienteGuardado"
                     class="flex items-center gap-3 px-4 py-3">
                    <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-400">Cliente</p>
                        <p class="text-xs text-gray-500 italic">Sin datos aún</p>
                    </div>
                    <button @click="abrirModal('datos')"
                            class="text-xs font-semibold px-2.5 py-1 rounded-lg transition-colors flex-shrink-0"
                            style="background:var(--pos-red); color:white;">
                        + Ingresar
                    </button>
                </div>

                {{-- Con cliente --}}
                <div x-show="clienteGuardado" class="px-4 py-3">
                    <div class="flex items-start gap-3">
                        {{-- Avatar con inicial --}}
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-sm text-white uppercase"
                             style="background:var(--pos-red);"
                             x-text="cliente.nombre.trim().charAt(0) || '?'"></div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-bold text-sm leading-tight truncate" x-text="cliente.nombre"></p>
                            <p class="text-gray-400 text-xs mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z"/>
                                </svg>
                                <span x-text="cliente.celular"></span>
                            </p>
                            <p x-show="cliente.correo" class="text-gray-500 text-xs truncate mt-0.5" x-text="cliente.correo"></p>
                        </div>
                        {{-- Editar --}}
                        <button @click="abrirModal('datos')"
                                class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors flex-shrink-0"
                                title="Editar datos">
                            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Indicaciones --}}
                    <div x-show="cliente.complemento"
                         class="mt-2 flex items-start gap-1.5 text-gray-500 text-xs">
                        <svg class="w-3 h-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <span class="italic" x-text="cliente.complemento"></span>
                    </div>
                </div>
            </div>

            {{-- Encabezado panel --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 flex-shrink-0"
                 style="background:#f8fafc;">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" style="color:var(--pos-red)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="font-bold text-gray-800 text-sm">Pedido</h3>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full text-white"
                      style="background:var(--pos-red);"
                      x-text="carrito.length + ' item' + (carrito.length !== 1 ? 's' : '')"></span>
            </div>

            {{-- Lista items --}}
            <div class="flex-1 overflow-y-auto pos-scroll">

                {{-- Empty state --}}
                <div x-show="carrito.length === 0"
                     class="flex flex-col items-center justify-center h-full text-center px-6">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-400">Sin productos</p>
                    <p class="text-xs text-gray-300 mt-1">Selecciona items del menú</p>
                </div>

                {{-- Items --}}
                <div x-show="carrito.length > 0" class="divide-y divide-gray-50">
                    <template x-for="(item, idx) in carrito" :key="idx">
                        <div class="flex items-start gap-2.5 px-4 py-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-white text-xs font-bold"
                                 style="background:var(--pos-red)">
                                <span x-text="item.cantidad"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-900 leading-snug" x-text="item.nombre"></p>
                                <p x-show="item.adicionales.length"
                                   class="text-[11px] text-gray-400 mt-0.5 leading-tight"
                                   x-text="item.adicionales.map(a => a.nombre || a.adicionalnombre).join(' · ')"></p>
                                <p class="text-xs font-bold mt-1" style="color:var(--pos-red)"
                                   x-text="'$' + formatNum(item.total)"></p>
                            </div>
                            <button @click="quitarDelCarrito(idx)"
                                    class="w-6 h-6 flex items-center justify-center text-gray-300 hover:text-red-500 flex-shrink-0 rounded-lg hover:bg-red-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Cupón --}}
                <div x-show="carrito.length > 0" class="px-4 py-3 border-t border-gray-100">
                    <template x-if="!cupon.aplicado">
                        <div>
                            <div class="flex gap-2">
                                <input x-model="cupon.codigo"
                                       @keydown.enter="aplicarCupon()"
                                       type="text" placeholder="Código de cupón"
                                       class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 uppercase focus:outline-none focus:border-[#C62828]"
                                       :disabled="validandoCupon"/>
                                <button @click="aplicarCupon()"
                                        :disabled="validandoCupon || !cupon.codigo.trim()"
                                        class="text-white text-xs font-bold px-3 py-2 rounded-lg transition-colors disabled:opacity-50"
                                        style="background:var(--pos-red);">
                                    <span x-show="!validandoCupon">Aplicar</span>
                                    <span x-show="validandoCupon" class="flex items-center">
                                        <div class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                    </span>
                                </button>
                            </div>
                            <p x-show="cupon.valido === false"
                               class="text-[11px] text-red-500 mt-1"
                               x-text="cupon.mensaje"></p>
                        </div>
                    </template>
                    <template x-if="cupon.aplicado">
                        <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                            <div>
                                <p class="text-[11px] font-bold text-green-700 uppercase" x-text="cupon.codigo"></p>
                                <p class="text-[11px] text-green-600" x-text="cupon.mensaje"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-green-700" x-text="'-$' + formatNum(cupon.descuento)"></span>
                                <button @click="quitarCupon()" class="text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Totales + acción --}}
            <div x-show="carrito.length > 0"
                 class="flex-shrink-0 border-t border-gray-200 px-4 py-4"
                 style="background:#f8fafc;">
                <div class="space-y-1.5 mb-3">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Subtotal</span>
                        <span x-text="'$' + formatNum(totalCarrito)"></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Domicilio</span>
                        <span x-text="'$' + formatNum(valorDomicilio)"></span>
                    </div>
                    <template x-if="cupon.aplicado">
                        <div class="flex justify-between text-xs text-green-600 font-semibold">
                            <span>Descuento cupón</span>
                            <span x-text="'-$' + formatNum(cupon.descuento)"></span>
                        </div>
                    </template>
                    <div class="flex justify-between text-sm font-bold text-gray-900 border-t border-gray-200 pt-2 mt-1">
                        <span>Total</span>
                        <span x-text="'$' + formatNum(totalConDomicilio)"></span>
                    </div>
                </div>
                <button @click="abrirModal('pago')"
                        class="w-full text-white font-bold py-3 rounded-xl text-sm shadow-md hover:opacity-90 active:scale-[0.98] transition-all"
                        style="background:var(--pos-red);">
                    Continuar al pago →
                </button>
            </div>
        </aside>

    </div>{{-- /BODY --}}


    {{-- ══════════ MODAL: Detalle producto (POS layout) ══════════ --}}
    <div x-show="modal === 'producto'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-6"
         @click.self="cerrarModal()">

        <div class="bg-white w-full rounded-2xl shadow-2xl flex flex-col overflow-hidden"
             style="max-width:860px; max-height:88vh;">

            {{-- ── Cuerpo: imagen izquierda + opciones derecha ── --}}
            <div class="flex flex-1 overflow-hidden">

                {{-- COLUMNA IZQUIERDA — imagen + info del producto --}}
                <div class="relative flex-shrink-0 bg-gray-900" style="width:320px;">
                    {{-- Imagen de fondo --}}
                    <template x-if="productoActual.foto">
                        <img :src="productoActual.foto" :alt="productoActual.nombre"
                             class="absolute inset-0 img-cover opacity-90"/>
                    </template>

                    {{-- Oscurecido suave en la zona superior --}}
                    <div class="absolute inset-0 bg-gradient-to-b from-black/30 to-transparent"></div>

                    {{-- Botón cerrar --}}
                    <button @click="cerrarModal()"
                            class="absolute top-3 right-3 w-8 h-8 bg-black/40 hover:bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center transition-colors z-10">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    {{-- Info del producto (sobre la imagen) --}}
                    <div class="absolute bottom-0 left-0 right-0 p-5 z-10 backdrop-blur-sm"
                         style="background: linear-gradient(to top, rgba(0,0,0,0.88) 0%, rgba(0,0,0,0.75) 60%, transparent 100%)">
                        <h3 class="text-white font-bold text-xl leading-snug drop-shadow"
                            x-text="productoActual.nombre"></h3>
                        <p class="text-white/70 text-sm mt-1 leading-relaxed line-clamp-2"
                           x-text="productoActual.descripcion"></p>
                        <p class="text-white font-extrabold text-2xl mt-3 drop-shadow"
                           x-text="'$' + formatNum(productoActual.precio)"></p>

                        {{-- Selector de cantidad --}}
                        <div class="flex items-center gap-3 mt-4">
                            <button @click="cantidad > 1 && cantidad--"
                                    :class="cantidad <= 1 ? 'opacity-40' : 'hover:bg-white/30'"
                                    class="w-9 h-9 bg-white/20 backdrop-blur-sm border border-white/30 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span class="text-white font-bold text-2xl w-8 text-center tabular-nums"
                                  x-text="cantidad"></span>
                            <button @click="cantidad++"
                                    class="w-9 h-9 rounded-full flex items-center justify-center transition-colors hover:opacity-90"
                                    style="background:var(--pos-red);">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                            <span class="text-white/60 text-sm ml-1">unid.</span>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA — opciones y toppings --}}
                <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">

                    {{-- Header derecho --}}
                    <div class="flex-shrink-0 px-5 py-3.5 bg-white border-b border-gray-100 flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Personaliza tu pedido</p>
                        <template x-if="adicionalesProducto.length > 0">
                            <span class="text-xs text-gray-400"
                                  x-text="Object.values(seleccionAdicionales).filter(v=>v!=='').length + ' / ' + adicionalesProducto.length + ' seleccionados'"></span>
                        </template>
                    </div>

                    {{-- Cargando --}}
                    <div x-show="cargandoAdicionales"
                         class="flex-1 flex flex-col items-center justify-center gap-3 text-center">
                        <div class="w-10 h-10 border-3 border-t-transparent rounded-full animate-spin"
                             style="border-color:var(--pos-red); border-top-color:transparent; border-width:3px;"></div>
                        <p class="text-sm text-gray-400">Cargando opciones...</p>
                    </div>

                    {{-- Sin adicionales --}}
                    <div x-show="!cargandoAdicionales && adicionalesProducto.length === 0"
                         class="flex-1 flex flex-col items-center justify-center gap-2 text-center px-6">
                        <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mb-1">
                            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Listo para agregar</p>
                        <p class="text-xs text-gray-400">Este producto no tiene opciones adicionales</p>
                    </div>

                    {{-- Grupos de adicionales --}}
                    <div x-show="!cargandoAdicionales && adicionalesProducto.length > 0"
                         class="flex-1 overflow-y-auto pos-scroll px-4 py-3 space-y-4">
                        <template x-for="grupo in adicionalesProducto" :key="grupo.idcategoria">
                            <div :id="'grupo-' + grupo.idcategoria"
                                 class="bg-white rounded-xl overflow-hidden shadow-sm">

                                {{-- Encabezado grupo --}}
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-5 rounded-full flex-shrink-0"
                                             :style="seleccionAdicionales[grupo.idcategoria]
                                                 ? 'background:#16a34a'
                                                 : 'background:var(--pos-red)'"></div>
                                        <h4 class="font-bold text-gray-800 text-sm" x-text="grupo.nombrecat"></h4>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <template x-if="seleccionAdicionales[grupo.idcategoria]">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </template>
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                              :class="seleccionAdicionales[grupo.idcategoria]
                                                  ? 'bg-green-50 text-green-700'
                                                  : 'bg-red-50 text-red-600'"
                                              x-text="seleccionAdicionales[grupo.idcategoria] ? 'Seleccionado' : 'Requerido'"></span>
                                    </div>
                                </div>

                                {{-- Opciones: grid de botones tipo tile --}}
                                <div class="p-3 grid grid-cols-2 gap-2">
                                    <template x-for="adic in grupo.adicionales" :key="adic.adicionalesid">
                                        <label class="relative flex flex-col cursor-pointer select-none">
                                            <input type="radio"
                                                   :name="'grupo-' + grupo.idcategoria"
                                                   :value="adic.adicionalesid"
                                                   x-model="seleccionAdicionales[grupo.idcategoria]"
                                                   @change="verificarAdicionales(); avanzarGrupo(grupo.idcategoria)"
                                                   class="sr-only"/>
                                            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border-2 transition-all"
                                                 :class="seleccionAdicionales[grupo.idcategoria] == adic.adicionalesid
                                                     ? 'border-[#C62828] bg-[#FFEBEE] shadow-sm'
                                                     : 'border-gray-200 bg-white hover:border-gray-300'">
                                                {{-- Radio visual --}}
                                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                                     :class="seleccionAdicionales[grupo.idcategoria] == adic.adicionalesid
                                                         ? 'border-[#C62828] bg-[#C62828]'
                                                         : 'border-gray-300 bg-white'">
                                                    <template x-if="seleccionAdicionales[grupo.idcategoria] == adic.adicionalesid">
                                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3"/>
                                                        </svg>
                                                    </template>
                                                </div>
                                                {{-- Texto --}}
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-semibold text-gray-800 leading-tight"
                                                       x-text="adic.adicionalnombre"></p>
                                                    <p x-show="parseInt(adic.precio) > 0"
                                                       class="text-[11px] font-bold mt-0.5"
                                                       style="color:var(--pos-red)"
                                                       x-text="'+$' + formatNum(adic.precio)"></p>
                                                    <p x-show="!parseInt(adic.precio)"
                                                       class="text-[11px] text-gray-400 mt-0.5">Incluido</p>
                                                </div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ── Barra inferior: totales + botón ── --}}
            <div class="flex-shrink-0 border-t border-gray-200 px-5 py-4 bg-white flex items-center gap-4">
                {{-- Resumen de precio --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-400 leading-none mb-0.5">Subtotal</p>
                    <p class="text-xl font-extrabold leading-none" style="color:var(--pos-red)"
                       x-text="'$' + formatNum(subtotalActual)"></p>
                    <p class="text-xs text-gray-400 mt-0.5"
                       x-text="cantidad + ' × $' + formatNum(productoActual.precio)"></p>
                </div>

                {{-- Indicador de completitud --}}
                <template x-if="adicionalesProducto.length > 0 && !puedoAgregar">
                    <div class="flex items-center gap-1.5 text-orange-500 bg-orange-50 border border-orange-200 px-3 py-2 rounded-xl flex-shrink-0">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span class="text-xs font-semibold">Completa las opciones</span>
                    </div>
                </template>

                {{-- Botón agregar --}}
                <button @click="agregarAlCarrito()" :disabled="!puedoAgregar"
                        class="flex items-center gap-3 text-white font-bold py-3.5 px-6 rounded-xl text-sm transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:opacity-90 active:scale-[0.98] flex-shrink-0 shadow-lg"
                        style="background:var(--pos-red);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Agregar al pedido
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════ MODAL: Forma de pago ══════════ --}}
    <div x-show="modal === 'pago'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
         @click.self="cerrarModal()">
        <div class="bg-white w-full max-w-md rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="cerrarModal()"
                            class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <h3 class="font-bold text-gray-900">Forma de pago</h3>
                </div>
                <div class="text-right bg-gray-50 rounded-xl px-3 py-1.5">
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="text-sm font-bold text-gray-900" x-text="'$' + formatNum(totalConDomicilio)"></p>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="space-y-2 mb-4">
                    <template x-for="fp in formasPago" :key="fp.valor">
                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all"
                               :class="formaPagoSeleccionada === fp.valor
                                   ? 'border-[#C62828] bg-[#FFEBEE]'
                                   : 'border-gray-100 hover:border-gray-200'">
                            <input type="radio" :value="fp.valor" x-model="formaPagoSeleccionada" class="sr-only"/>
                            <span class="text-2xl" x-text="fp.icono"></span>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 text-sm" x-text="fp.texto"></p>
                                <p class="text-xs text-gray-400" x-text="fp.desc"></p>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                 :class="formaPagoSeleccionada === fp.valor
                                     ? 'border-[#C62828] bg-[#C62828]'
                                     : 'border-gray-300'">
                                <template x-if="formaPagoSeleccionada === fp.valor">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                            </div>
                        </label>
                    </template>
                </div>
                <button @click="enviarPedido()" :disabled="!formaPagoSeleccionada || enviando"
                        class="w-full text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 text-sm disabled:opacity-50 transition-opacity"
                        style="background:var(--pos-red);">
                    <template x-if="enviando">
                        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    </template>
                    <span x-text="enviando ? 'Enviando pedido...' : 'Confirmar pedido'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════ MODAL: Datos del cliente (popup de inicio) ══════════ --}}
    <div x-show="modal === 'datos'"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-6">
        <div class="bg-white w-full rounded-2xl overflow-hidden shadow-2xl flex"
             style="max-width:720px; max-height:90vh;">

            {{-- Franja izquierda decorativa --}}
            <div class="flex-shrink-0 flex flex-col items-center justify-center p-8 text-center"
                 style="width:200px; background:var(--pos-header);">
                <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-white font-bold text-sm leading-snug">Datos del cliente</p>
                <p class="text-gray-400 text-xs mt-2 leading-relaxed">Ingresa la info antes de armar el pedido</p>
                {{-- Dirección --}}
                <div class="mt-6 bg-white/10 rounded-xl p-3 text-left w-full">
                    <p class="text-gray-400 text-[10px] uppercase tracking-wider mb-1">Entrega en</p>
                    <p class="text-white text-xs font-semibold leading-snug"
                       x-text="localStorage.getItem('nombreciudad') || ''"></p>
                    <p class="text-gray-400 text-[11px] mt-1 leading-snug"
                       x-text="localStorage.getItem('direccion') || ''"></p>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="flex-1 flex flex-col overflow-hidden p-4">
                <div class="flex-shrink-0 px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Nuevo pedido</h3>
                        <p class="text-xs text-gray-400 mt-0.5">¿Con quién hablamos?</p>
                    </div>
                    <template x-if="clienteGuardado">
                        <button @click="cerrarModal()"
                                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </template>
                </div>

                <div class="flex-1 overflow-y-auto pos-scroll px-6 py-5 space-y-4">
                    {{-- Nombre --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">
                            Nombre completo <span style="color:var(--pos-red)">*</span>
                        </label>
                        <input x-model="cliente.nombre" @input="validarCliente()" type="text"
                               placeholder="¿Cómo se llama el cliente?"
                               class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:border-[#C62828] transition-colors"
                               x-ref="inputNombre"/>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        {{-- Celular --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">
                                Celular <span style="color:var(--pos-red)">*</span>
                            </label>
                            <input x-model="cliente.celular" @input="validarCliente()" type="tel"
                                   placeholder="3001234567"
                                   class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:border-[#C62828] transition-colors"/>
                        </div>
                        {{-- Correo --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">
                                Correo <span class="text-gray-300 font-normal normal-case">(opcional)</span>
                            </label>
                            <input x-model="cliente.correo" @input="validarCliente()" type="email"
                                   placeholder="correo@ejemplo.com"
                                   class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:border-[#C62828] transition-colors"/>
                        </div>
                    </div>

                    {{-- Indicaciones --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">
                            Indicaciones de entrega
                        </label>
                        <textarea x-model="cliente.complemento" rows="2"
                                  placeholder="Apto, piso, torre, punto de referencia..."
                                  class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 text-sm text-gray-900 resize-none focus:outline-none focus:border-[#C62828] transition-colors"></textarea>
                    </div>
                </div>

                <div class="flex-shrink-0 border-t border-gray-100 px-6 py-4 bg-white">
                    <button @click="guardarCliente()"
                            :disabled="!clienteValido"
                            class="w-full text-white font-bold py-3.5 rounded-xl text-sm disabled:opacity-40 transition-opacity flex items-center justify-center gap-2"
                            style="background:var(--pos-red);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Comenzar a armar el pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ MODAL: Pedido confirmado ══════════ --}}
    <div x-show="modal === 'confirmado'" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">¡Pedido confirmado!</h3>
                <p class="text-gray-400 text-sm mt-2">Tiempo estimado:
                    <span class="font-semibold text-gray-700"
                          x-text="(tienda.tiempoEntrega || '30-45') + ' min'"></span>
                </p>
            </div>

            <template x-if="cuponError">
                <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-4">
                    <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Problema con el cupón</p>
                        <p class="text-xs text-yellow-700 mt-0.5" x-text="cuponError"></p>
                    </div>
                </div>
            </template>

            <button @click="nuevoPedido()"
                    class="w-full text-white font-bold py-3.5 rounded-xl text-sm hover:opacity-90 transition-opacity"
                    style="background:var(--pos-red);">
                Nuevo pedido
            </button>
        </div>
    </div>

    {{-- ══════════ MODAL: Tienda cerrada ══════════ --}}
    <div x-show="modal === 'cerrado'" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl">
            <div class="w-full h-24 flex items-center justify-center" style="background:var(--pos-header);">
                <span class="text-white font-bold text-2xl tracking-widest">SR WOK</span>
            </div>
            <div class="p-6 text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Estamos cerrados</h3>
                <p class="text-sm text-gray-400 mb-1">El punto de venta no está disponible.</p>
                <template x-if="tienda.apertura">
                    <p class="text-sm text-gray-600 mb-5">
                        Horario: <span class="font-bold" x-text="tienda.apertura + ' – ' + tienda.cierre"></span>
                    </p>
                </template>
                <a href="{{ route('pedido.nuevo') }}"
                   class="block w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl text-sm">
                    Volver al inicio
                </a>
            </div>
        </div>
    </div>

</div>{{-- /x-data --}}
@endsection

@push('scripts')
<script>
function menuApp() {
    return {
        cargando: true, cargandoAdicionales: false, modal: null,
        menu: [], categorias: [], categoriaFiltro: '0',
        carrito: [], valorDomicilio: 0, tienda: {}, tiendaAbierta: true,
        productoActual: {}, categoriaActual: {}, cantidad: 1,
        adicionalesProducto: [], seleccionAdicionales: {}, puedoAgregar: false,
        formaPagoSeleccionada: '',
        cupon: { codigo: '', aplicado: false, descuento: 0, porcentaje: 0, mensaje: '', valido: null },
        validandoCupon: false, cuponError: '',
        formasPago: [
            { valor: 'Efectivo',  texto: 'Efectivo',  icono: '💵', desc: 'Paga al recibir tu pedido' },
            { valor: 'Datafono',  texto: 'Datáfono',  icono: '💳', desc: 'Terminal en la entrega' },
        ],
        cliente: { nombre: '', correo: '', celular: '', complemento: '' },
        clienteGuardado: false, clienteValido: false, enviando: false, errorEnvio: '',

        get menuFiltrado() {
            if (this.categoriaFiltro === '0') return this.menu;
            return this.menu.filter(c => String(c.comboid) === String(this.categoriaFiltro));
        },
        get totalCarrito()      { return this.carrito.reduce((s, i) => s + i.total, 0); },
        get totalConDomicilio() { return this.totalCarrito + parseInt(this.valorDomicilio) - this.cupon.descuento; },
        get subtotalActual() {
            const base  = parseInt(this.productoActual.precio || 0) * this.cantidad;
            const adics = Object.values(this.seleccionAdicionales).reduce((s, id) => {
                const a = this.adicionalesProducto.flatMap(g => g.adicionales)
                              .find(x => String(x.adicionalesid) === String(id));
                return s + (a ? parseInt(a.precio || 0) * this.cantidad : 0);
            }, 0);
            return base + adics;
        },

        async iniciar() {
            if (!localStorage.getItem('ciudad')) { window.location.href = '/'; return; }

            // Restaurar estado persistido
            try {
                const c = localStorage.getItem('pos_carrito');
                if (c) this.carrito = JSON.parse(c);
                const cl = localStorage.getItem('pos_cliente');
                if (cl) this.cliente = JSON.parse(cl);
                const cg = localStorage.getItem('pos_cliente_guardado');
                if (cg === '1') { this.clienteGuardado = true; this.validarCliente(); }
                const cu = localStorage.getItem('pos_cupon');
                if (cu) this.cupon = JSON.parse(cu);
            } catch {}

            this.modal = this.clienteGuardado ? null : 'datos';

            const tienda = localStorage.getItem('punto');
            await Promise.all([
                this.cargarMenu(tienda),
                this.cargarCombos(tienda),
                this.cargarAdicionesBase(tienda),
            ]);
            this.cargando = false;
        },

        async cargarMenu(tienda) {
            const res  = await this.apiPost('{{ route("api.menu") }}', { tienda });
            const data = await res.json();
            this.menu      = data;
            this.categorias = data.map(c => ({ combo: c.combo, comboid: c.comboid }));
            if (data.length > 0) {
                const d = data[0];
                this.tienda = {
                    foto: d.foto, nombre: d.tiendanombre, descripcion: d.tiendadescripcion,
                    tiempoEntrega: d.tiendatiempoentrega, apertura: d.tiendaapertura, cierre: d.tiendacierre,
                };
                this.valorDomicilio = parseInt(d.tiendadelivery) || 0;
                localStorage.setItem('valordomicilio', this.valorDomicilio);
                if (parseInt(d.tiendahorario) === 0) this.modal = 'cerrado';
                if (parseInt(d.tiendaestado)  === 0) { this.tiendaAbierta = false; this.modal = 'cerrado'; }
            }
        },

        async cargarCombos(tienda) {
            const res = await this.apiPost('{{ route("api.combos") }}', { tienda });
            localStorage.setItem('combos', await res.text());
        },

        async cargarAdicionesBase(tienda) {
            const res = await this.apiPost('{{ route("api.adiciones") }}', { tienda });
            localStorage.setItem('adiciones', await res.text());
        },

        scrollToCategory(id) {
            const el = document.getElementById('cat-' + id);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        async abrirProducto(prod, cat) {
            this.productoActual      = { ...prod, foto: prod.fotoproducto };
            this.categoriaActual     = cat;
            this.cantidad            = 1;
            this.seleccionAdicionales = {};
            this.adicionalesProducto = [];
            this.puedoAgregar        = false;
            this.cargandoAdicionales = true;
            this.modal               = 'producto';
            try {
                const res      = await this.apiPost('{{ route("api.producto") }}', { producto: prod.id });
                const adicionales = await res.json();
                this.adicionalesProducto = adicionales;
                if (adicionales.length === 0) this.puedoAgregar = true;
            } finally { this.cargandoAdicionales = false; }
        },

        verificarAdicionales() {
            const requeridos  = this.adicionalesProducto.length;
            const completados = Object.values(this.seleccionAdicionales).filter(v => v !== '').length;
            this.puedoAgregar = completados >= requeridos;
        },

        avanzarGrupo(idcategoria) {
            const idx      = this.adicionalesProducto.findIndex(g => g.idcategoria == idcategoria);
            const siguiente = this.adicionalesProducto[idx + 1];
            if (siguiente) {
                setTimeout(() => {
                    const el = document.getElementById('grupo-' + siguiente.idcategoria);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 150);
            }
        },

        agregarAlCarrito() {
            const combos      = JSON.parse(localStorage.getItem('combos')   || '[]');
            const adicionesBase = JSON.parse(localStorage.getItem('adiciones') || '[]');
            const combo = combos.find(c => String(c.ID) === String(this.productoActual.id)) || {
                codintegracion: this.productoActual.id,
                nombre: this.productoActual.nombre,
                precio: this.productoActual.precio,
            };
            const adicionales  = Object.entries(this.seleccionAdicionales)
                .filter(([, v]) => v !== '')
                .map(([, id])   => adicionesBase.find(a => String(a.ID) === String(id)))
                .filter(Boolean);
            const totalAdics   = adicionales.reduce((s, a) => s + parseInt(a.precio || 0), 0);
            const total        = (parseInt(combo.precio || 0) + totalAdics) * this.cantidad;
            const catKey       = this.categoriaActual.combo || 'combos';
            const pedidoGrupo  = {}; pedidoGrupo[catKey] = adicionales;
            this.carrito.push({ nombre: combo.nombre, cantidad: this.cantidad, adicionales, total, cabecera: combo, pedido: pedidoGrupo });
            this.persistir();
            this.modal = null;
        },

        quitarDelCarrito(idx) {
            this.carrito.splice(idx, 1);
            this.persistir();
        },

        async aplicarCupon() {
            if (!this.cupon.codigo.trim()) return;
            this.validandoCupon = true;
            this.cupon.valido   = null;
            try {
                const res  = await this.apiPost('{{ route("api.cupon") }}', {
                    code:   this.cupon.codigo.trim().toUpperCase(),
                    amount: this.totalCarrito + parseInt(this.valorDomicilio),
                    phone:  this.cliente.celular || '',
                });
                const data = await res.json();
                if (data.valid) {
                    this.cupon = {
                        codigo:     this.cupon.codigo.trim().toUpperCase(),
                        aplicado:   true, valido: true,
                        descuento:  parseInt(data.discount_amount) || 0,
                        porcentaje: parseFloat(data.discount_value) || 0,
                        mensaje:    data.message,
                    };
                    this.persistir();
                } else {
                    this.cupon = {
                        codigo: this.cupon.codigo, aplicado: false, valido: false,
                        descuento: 0, porcentaje: 0, mensaje: data.message || 'Cupón no válido.',
                    };
                }
            } catch {
                this.cupon.valido   = false;
                this.cupon.aplicado = false;
                this.cupon.mensaje  = 'Error al validar el cupón.';
            } finally { this.validandoCupon = false; }
        },

        quitarCupon() {
            this.cupon = { codigo: '', aplicado: false, descuento: 0, porcentaje: 0, mensaje: '', valido: null };
            this.persistir();
        },

        async enviarPedido() {
            this.enviando = true; this.errorEnvio = '';
            const payload = {
                pdv: localStorage.getItem('punto'),
                ciudad: localStorage.getItem('ciudad'),
                nombreciudad: localStorage.getItem('nombreciudad') || '',
                direccion: localStorage.getItem('direccion'),
                nombre: this.cliente.nombre, correo: this.cliente.correo,
                celular: this.cliente.celular, complemento: this.cliente.complemento,
                formapago: this.formaPagoSeleccionada,
                cabeceras:  JSON.stringify(this.carrito.map(i => i.cabecera)),
                pedidos:    JSON.stringify(this.carrito.map(i => i.pedido)),
                cantidades: JSON.stringify(this.carrito.map(i => ({ cantidad: i.cantidad }))),
                totales:    JSON.stringify(this.carrito.map(i => ({ total: i.total }))),
                contador: this.carrito.length, total: this.totalConDomicilio,
                valordomicilio: this.valorDomicilio, fcm: localStorage.getItem('fcm') || '',
                cupon_codigo:     this.cupon.aplicado ? this.cupon.codigo     : '',
                cupon_descuento:  this.cupon.aplicado ? this.cupon.descuento  : 0,
                cupon_porcentaje: this.cupon.aplicado ? this.cupon.porcentaje : 0,
            };
            try {
                const res = await this.apiPost('{{ route("api.pedido") }}', payload);
                if (res.ok) {
                    const json = await res.json();
                    this.cuponError = json.cupon_error || '';
                    this.carrito    = [];
                    this.cliente    = { nombre: '', correo: '', celular: '', complemento: '' };
                    this.clienteGuardado = false;
                    this.cupon      = { codigo: '', aplicado: false, descuento: 0, porcentaje: 0, mensaje: '', valido: null };
                    this.modal      = 'confirmado';
                    ['pedidos','contador','cantidades','totales','cabeceras','totalenpedido',
                     'pos_carrito','pos_cliente','pos_cliente_guardado','pos_cupon']
                        .forEach(k => localStorage.removeItem(k));
                } else {
                    const err = await res.json();
                    this.errorEnvio = err.message || 'Error al enviar el pedido.';
                }
            } catch { this.errorEnvio = 'Error de conexión.'; }
            finally  { this.enviando = false; }
        },

        guardarCliente() {
            if (!this.clienteValido) return;
            this.clienteGuardado = true;
            localStorage.setItem('pos_cliente', JSON.stringify(this.cliente));
            localStorage.setItem('pos_cliente_guardado', '1');
            this.modal = null;
        },

        persistir() {
            localStorage.setItem('pos_carrito', JSON.stringify(this.carrito));
            localStorage.setItem('pos_cupon',   JSON.stringify(this.cupon));
        },

        validarCliente() {
            const { nombre, celular } = this.cliente;
            this.clienteValido = nombre.trim().length >= 3 && celular.trim().length >= 7;
        },

        nuevoPedido() {
            ['pos_carrito','pos_cliente','pos_cliente_guardado','pos_cupon']
                .forEach(k => localStorage.removeItem(k));
            window.location.href = '{{ route("pedido.nuevo") }}';
        },

        abrirModal(n) { this.modal = n; },
        cerrarModal()  { this.modal = null; },

        apiPost(url, data) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });
        },

        formatNum(num) {
            if (!num && num !== 0) return '-';
            return Math.floor(Math.abs(Number(num))).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },
    };
}
</script>
@endpush

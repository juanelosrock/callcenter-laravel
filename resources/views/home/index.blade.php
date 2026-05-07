@extends('layouts.app')

@section('title', 'Sr WOK — Nuevo Pedido')

@push('head')
<style>
    :root {
        --pos-red:      #C62828;
        --pos-red-dark: #B71C1C;
        --pos-header:   #16213e;
        --pos-sidebar:  #1e293b;
    }
    html, body { height: 100%; overflow: hidden; }

    .pos-scroll::-webkit-scrollbar { width: 4px; }
    .pos-scroll::-webkit-scrollbar-thumb { background: #475569; border-radius: 2px; }

    .city-btn { transition: background .12s, border-color .12s; }
    .city-active { background: var(--pos-red) !important; border-color: var(--pos-red) !important; color: #fff !important; }
    .city-active svg { color: #fff !important; }

    input:focus, select:focus {
        outline: none;
        border-color: var(--pos-red) !important;
        box-shadow: 0 0 0 3px rgba(198,40,40,.12);
    }
</style>
@endpush

@section('content')
<div x-data="homeApp()" x-init="cargarCiudades()"
     class="flex flex-col"
     style="height:100vh; background:#f0f2f5;">

    {{-- ══════════ HEADER ══════════ --}}
    <header class="flex items-center gap-4 px-5 h-14 flex-shrink-0 shadow-lg z-10"
            style="background:var(--pos-header);">

        {{-- Logo --}}
        <div class="w-9 h-9 rounded-lg flex-shrink-0 border border-white/20 flex items-center justify-center font-bold text-white text-xs"
             style="background:var(--pos-red);">WOK</div>

        <div class="flex-shrink-0 leading-tight">
            <p class="text-white font-bold text-sm">Sr WOK</p>
            <p class="text-gray-400 text-xs">Nuevo pedido</p>
        </div>

        <div class="w-px h-6 bg-white/20"></div>

        <div class="flex items-center gap-1.5 text-gray-300 text-xs">
            <svg class="w-3.5 h-3.5 text-[#C62828]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Paso 1 de 2 — Ciudad y dirección</span>
        </div>

        <div class="flex-1"></div>

        {{-- Volver al dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al inicio
        </a>
    </header>

    {{-- ══════════ CUERPO 2 COLUMNAS ══════════ --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- ── Panel izquierdo: ciudades ── --}}
        <aside class="flex-shrink-0 flex flex-col overflow-hidden border-r border-white/10"
               style="width:280px; background:var(--pos-sidebar);">

            <div class="flex-shrink-0 px-4 py-4 border-b border-white/10">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Selecciona la ciudad</p>
            </div>

            <div class="flex-1 overflow-y-auto pos-scroll p-3 space-y-1">

                {{-- Skeleton --}}
                <template x-if="cargandoCiudades">
                    <div class="space-y-2">
                        <template x-for="i in 10" :key="i">
                            <div class="h-11 bg-white/10 rounded-xl animate-pulse"></div>
                        </template>
                    </div>
                </template>

                <template x-if="!cargandoCiudades">
                    <div class="space-y-1">
                        <template x-for="c in ciudades" :key="c.codintegracion">
                            <button
                                @click="seleccionarCiudad(c)"
                                :class="ciudadSeleccionada === c.codintegracion ? 'city-active' : 'text-gray-300 border-transparent hover:bg-white/10 hover:border-white/20'"
                                class="city-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-left"
                            >
                                <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm font-semibold" x-text="c.nombre"></span>
                                <template x-if="ciudadSeleccionada === c.codintegracion">
                                    <svg class="w-4 h-4 ml-auto flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                            </button>
                        </template>
                        <p x-show="errorCiudad" class="text-red-400 text-xs px-2 pt-1" x-text="errorCiudad"></p>
                    </div>
                </template>
            </div>
        </aside>

        {{-- ── Panel derecho: formulario de dirección ── --}}
        <main class="flex-1 flex items-center justify-center p-8 overflow-y-auto pos-scroll">

            {{-- Estado: sin ciudad seleccionada --}}
            <div x-show="!ciudadSeleccionada"
                 class="text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                     style="background:#1e293b;">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-semibold">Selecciona una ciudad</p>
                <p class="text-gray-600 text-sm mt-1">Elige la ciudad en el panel izquierdo</p>
            </div>

            {{-- Formulario de dirección --}}
            <div x-show="ciudadSeleccionada"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="w-full"
                 style="max-width:560px;">

                {{-- Ciudad seleccionada --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:var(--pos-red);">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Ciudad seleccionada</p>
                        <p class="font-bold text-gray-800" x-text="nombreCiudad"></p>
                    </div>
                </div>

                {{-- Card del formulario --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                    {{-- Título --}}
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2"
                         style="background:#f8fafc;">
                        <svg class="w-4 h-4 flex-shrink-0" style="color:var(--pos-red)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <h2 class="font-bold text-gray-700 text-sm">Dirección de entrega</h2>
                    </div>

                    <div class="p-6 space-y-4">

                        {{-- Preview de dirección --}}
                        <div class="flex items-center gap-3 rounded-xl px-4 py-3 border-2 transition-colors"
                             :class="direccionPreview
                                 ? 'border-[#C62828] bg-[#FFEBEE]'
                                 : 'border-gray-200 bg-gray-50'">
                            <svg class="w-4 h-4 flex-shrink-0"
                                 :class="direccionPreview ? 'text-[#C62828]' : 'text-gray-400'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span class="text-sm font-semibold"
                                  :class="direccionPreview ? 'text-[#C62828]' : 'text-gray-400'"
                                  x-text="direccionPreview || 'La dirección se armará aquí...'"></span>
                        </div>

                        {{-- Fila 1: Tipo de vía + Número --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Tipo de vía</label>
                                <select x-model="dir.tipo" @change="actualizarPreview()"
                                        class="w-full border-2 border-gray-100 rounded-xl px-3 py-2.5 text-sm text-gray-700 bg-white transition-colors">
                                    <option value="">Selecciona</option>
                                    <option value="CLL">Calle</option>
                                    <option value="KRA">Carrera</option>
                                    <option value="TRAN">Transversal</option>
                                    <option value="DIA">Diagonal</option>
                                    <option value="AV">Avenida</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Número</label>
                                <input x-model="dir.num1"
                                       @input="validarCampoDir($event); actualizarPreview()"
                                       type="text" maxlength="6" placeholder="Ej: 15"
                                       class="w-full border-2 border-gray-100 rounded-xl px-3 py-2.5 text-sm text-gray-700 transition-colors"/>
                            </div>
                        </div>

                        {{-- Fila 2: Orientación + Núm. cruce --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Orientación</label>
                                <select x-model="dir.orient1" @change="actualizarPreview()"
                                        class="w-full border-2 border-gray-100 rounded-xl px-3 py-2.5 text-sm text-gray-700 bg-white transition-colors">
                                    <option value="">(ninguna)</option>
                                    <option value="N">Norte</option>
                                    <option value="S">Sur</option>
                                    <option value="OE">Oeste</option>
                                    <option value="ES">Este</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Núm. cruce</label>
                                <input x-model="dir.num2"
                                       @input="validarCampoDir($event); actualizarPreview()"
                                       type="text" maxlength="6" placeholder="Ej: 20"
                                       class="w-full border-2 border-gray-100 rounded-xl px-3 py-2.5 text-sm text-gray-700 transition-colors"/>
                            </div>
                        </div>

                        {{-- Fila 3: Orientación + Núm. casa --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Orientación</label>
                                <select x-model="dir.orient2" @change="actualizarPreview()"
                                        class="w-full border-2 border-gray-100 rounded-xl px-3 py-2.5 text-sm text-gray-700 bg-white transition-colors">
                                    <option value="">(ninguna)</option>
                                    <option value="N">Norte</option>
                                    <option value="S">Sur</option>
                                    <option value="OE">Oeste</option>
                                    <option value="ES">Este</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Núm. casa</label>
                                <input x-model="dir.num3"
                                       @input="validarCampoDir($event); actualizarPreview()"
                                       type="text" maxlength="6" placeholder="Ej: 45"
                                       class="w-full border-2 border-gray-100 rounded-xl px-3 py-2.5 text-sm text-gray-700 transition-colors"/>
                            </div>
                        </div>

                        {{-- Teléfono del cliente --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Teléfono del cliente</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z"/>
                                </svg>
                                <input x-model="telefono" type="tel" placeholder="Ej: 3001234567"
                                       class="w-full border-2 border-gray-100 rounded-xl pl-9 pr-3 py-2.5 text-sm text-gray-700 transition-colors"/>
                            </div>
                        </div>

                        <p x-show="errorDir" class="text-red-500 text-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                            <span x-text="errorDir"></span>
                        </p>

                        {{-- Botón confirmar --}}
                        <button @click="buscarDireccion()"
                                :disabled="buscando || !dir.tipo || !dir.num1"
                                class="w-full text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 text-sm transition-all disabled:opacity-40 hover:opacity-90 active:scale-[0.98] shadow-md"
                                style="background:var(--pos-red);">
                            <template x-if="buscando">
                                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            </template>
                            <template x-if="!buscando">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <span x-text="buscando ? 'Verificando cobertura...' : 'Confirmar dirección y continuar'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- ══════════ MODAL: Sin cobertura ══════════ --}}
    <div x-show="sinCobertura" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white w-full max-w-md rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-6 pt-6 pb-4 text-center">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" style="color:var(--pos-red)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Sin cobertura en esta zona</h3>
                <p class="text-sm text-gray-400 mt-1">Comunica al cliente que puede llamar directamente</p>
            </div>

            <div class="px-6 pb-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Teléfonos por ciudad</p>
                <div class="grid grid-cols-2 gap-1.5">
                    @foreach([['Armenia','6067359868'],['Bogotá','6017444424'],['Cali','6026959570'],['Ibagué','6082771250'],['Manizales','6068918899'],['Medellín','6046044949'],['Palmira','6022868970'],['Pereira','6063400551'],['Popayán','6028368090'],['Tuluá','6022359880']] as [$c,$t])
                    <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2">
                        <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-gray-700">{{ $c }}</p>
                            <p class="text-[11px] text-gray-400">{{ $t }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="px-6 pb-6">
                <button @click="sinCobertura = false"
                        class="w-full border-2 border-gray-200 text-gray-700 font-bold py-3 rounded-xl text-sm hover:bg-gray-50 transition-colors">
                    Intentar con otra dirección
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function homeApp() {
    return {
        ciudades: [], cargandoCiudades: true,
        ciudadSeleccionada: '', nombreCiudad: '', errorCiudad: '',
        dir: { tipo: '', num1: '', orient1: '', num2: '', orient2: '', num3: '' },
        telefono: '',
        direccionPreview: '', buscando: false, sinCobertura: false, errorDir: '',

        limpiarPedidoAnterior() {
            ['pos_carrito','pos_cliente','pos_cliente_guardado','pos_cupon','pos_telefono']
                .forEach(k => localStorage.removeItem(k));
        },

        async cargarCiudades() {
            this.limpiarPedidoAnterior();
            try {
                const res = await fetch('{{ route("api.ciudades") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                });
                this.ciudades = await res.json();
            } catch {
                this.errorCiudad = 'Error cargando ciudades.';
            } finally {
                this.cargandoCiudades = false;
            }
        },

        seleccionarCiudad(c) {
            this.ciudadSeleccionada = c.codintegracion;
            this.nombreCiudad       = c.nombre;
            this.dir                = { tipo: '', num1: '', orient1: '', num2: '', orient2: '', num3: '' };
            this.direccionPreview   = '';
            this.errorDir           = '';
        },

        actualizarPreview() {
            const { tipo, num1, orient1, num2, orient2, num3 } = this.dir;
            this.direccionPreview = [tipo, num1, orient1, num2, orient2, num3].filter(Boolean).join(' ');
        },

        validarCampoDir(e) {
            const val = e.target.value.toUpperCase();
            const orientaciones = ['N','S','O','E','OE','ES','NOR','SUR','OES'];
            if (orientaciones.some(o => val.includes(o) && !val.includes('BIS'))) {
                e.target.value = '';
                this.errorDir = 'Usa los selectores de orientación';
                setTimeout(() => this.errorDir = '', 3000);
            }
        },

        async buscarDireccion() {
            this.errorDir = '';
            const { tipo, num1, orient1, num2, orient2, num3 } = this.dir;
            const partes = [tipo, num1, orient1 || '', num2];
            if (orient2) partes.push(orient2);
            if (num3)    partes.push(num3);
            const direccion = partes.join(' ');
            if (!tipo || !num1) { this.errorDir = 'Ingresa al menos el tipo de vía y número'; return; }
            this.buscando = true;
            try {
                const res = await fetch('{{ route("api.validar-direccion") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ciudad: this.ciudadSeleccionada, direccion })
                });
                const data = await res.json();
                if (parseInt(data) === -1 || data === -1) {
                    this.sinCobertura = true;
                } else {
                    localStorage.setItem('punto',        data);
                    localStorage.setItem('ciudad',       this.ciudadSeleccionada);
                    localStorage.setItem('nombreciudad', this.nombreCiudad);
                    localStorage.setItem('direccion',    direccion);
                    localStorage.setItem('pos_telefono', this.telefono);
                    window.location.href = '{{ route("pedido.menu") }}';
                }
            } catch {
                this.errorDir = 'Error al verificar la dirección.';
            } finally {
                this.buscando = false;
            }
        }
    }
}
</script>
@endpush

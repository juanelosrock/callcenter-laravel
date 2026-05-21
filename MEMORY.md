# MEMORY — Sr WOK CallCenter

Historial de mejoras, correcciones y decisiones técnicas del proyecto.
Actualizar este archivo en cada sesión cuando se haga un cambio relevante.

---

## Correcciones de flujo de trabajo

- **Siempre hacer commit + push al terminar cualquier tarea**, sin esperar que el usuario lo pida.
- **Antes de modificar cualquier archivo de vista**, verificar qué commit representa el estado correcto con `git log --oneline -10 -- <archivo>`. Los reverts frecuentes del historial hacen que el archivo en disco no siempre coincida con lo esperado.
- **No reescribir el diseño general** al aplicar cambios funcionales. Si el usuario dice "mantén el diseño intacto", cambiar SOLO la lógica JS o el bloque específico pedido.
- **Verificar siempre la ruta nombrada** antes de escribir `route("nombre")`. La ruta del menú es `pedido.menu`, no `menu`.
- Cuando el usuario hace una corrección al código o a la forma de trabajo, **guardar la corrección en este archivo** inmediatamente.

---

## Decisiones de arquitectura

### Campo de domicilio
- **Usar `calldelivery`**, NO `tiendadelivery`. El callcenter tiene su propio valor de domicilio.
- El toggle `aplicarDomicilio` (booleano, default `true`) permite al agente activar/desactivar el cobro de domicilio.
- `domicilioEfectivo` = `aplicarDomicilio ? valorDomicilio : 0`

### XML a SIBCO (`OrderController::construirOrdenXml`)
- `ORDEN/VALOR` = total completo del pedido. **NO descontar el domicilio.**
- `ORDEN/RECARGO` = valor del domicilio (puede ser 0).
- `PAGO/VALOR` = mismo valor que `ORDEN/VALOR`.
- `PAGO/DESCUENTO` = porcentaje del cupón (solo si `cupon_porcentaje > 0`).
- El XML se loguea: `\Log::info('[SIBCO] XML enviado: ' . $xml)`.

### Orden de operaciones al confirmar pedido
1. Enviar pedido a SIBCO (XML)
2. Registrar cliente en cupones API (`/customers/register`)
3. Aceptar términos (`/customers/accept-terms`)
4. Redimir cupón si aplica (`/coupons/redeem`)

### API de cupones (URLs derivadas de `CUPONES_API_URL`)
- Validar: `CUPONES_API_URL` tal cual (`/coupons/validate`)
- Redimir: reemplazar `/validate` → `/redeem`
- Clientes: reemplazar `/coupons/validate` → `` + `/customers/register` o `/customers/accept-terms`
- Campos requeridos en registro: `accept_privacy: true`, `department: 'Colombia'`

---

## Diseño del menú (`resources/views/menu/index.blade.php`)

### Layout POS — NO modificar la estructura general
Layout de **3 columnas fijas**:
- Sidebar izquierdo (156px): categorías
- Grilla central: cards de productos
- Panel derecho (300px): carrito + totales + checkout

El layout general (`flex flex-col`, `height:100vh`, sidebar, grilla, panel) es **intocable**.
Cambiar SOLO el bloque específico que el usuario pida.

### Commit de referencia estable: `3ffda4f` (timezone fix)
Si hay dudas sobre el estado correcto del archivo, restaurar con:
```bash
git checkout 3ffda4f -- resources/views/menu/index.blade.php
```

---

## Modal de adicionales — reglas de selección

Los grupos de adicionales tienen los campos de la API:
- `tipo`: `1` = radio (única), `2` = checkbox (múltiple)
- `obligatorio`: `1` = requerido, `0` = opcional
- `minimo`: mínimo de opciones a seleccionar
- `maximo`: máximo de opciones a seleccionar

### Estado `seleccionAdicionales`
- tipo 1 → `string` (`''` cuando vacío)
- tipo 2 → `array` (`[]` cuando vacío)

Inicializar en `abrirProducto()` después de cargar:
```js
adicionales.forEach(g => {
    this.seleccionAdicionales[g.idcategoria] = parseInt(g.tipo) === 2 ? [] : '';
});
this.verificarAdicionales();
```

### `verificarAdicionales()`
Solo bloquea si `obligatorio = 1`, respeta `minimo` para tipo 2:
```js
this.puedoAgregar = this.adicionalesProducto.every(g => {
    if (!parseInt(g.obligatorio)) return true;
    const min = parseInt(g.minimo) || 1;
    if (parseInt(g.tipo) === 2) return (this.seleccionAdicionales[g.idcategoria] || []).length >= min;
    return this.seleccionAdicionales[g.idcategoria] !== '';
});
```

### `toggleCheckbox(idcategoria, adicionalesid, maximo)`
Respetar `maximo`: no agregar si el array ya alcanzó el límite.

### `subtotalActual` y `agregarAlCarrito()`
Usar `flatMap` para aplanar strings y arrays:
```js
Object.values(this.seleccionAdicionales)
    .flatMap(v => Array.isArray(v) ? v : (v !== '' ? [v] : []))
```

### Encabezado del grupo (badge + subtexto)
- Verde "Seleccionado": grupo cumple su mínimo
- Rojo "Requerido": obligatorio, aún no cumple el mínimo
- Gris "Opcional": no obligatorio, sin selección
- Subtexto: "Elige 1 opción" / "Elige N" / "Elige entre X y Y" / "Hasta N" / "Opcional"
- Contador `X/Y` solo para grupos tipo 2

---

## Integraciones externas

### PBX / Socket.IO (`home/index.blade.php`)
- Conecta al socket usando `auth()->user()->extension`.
- Auto-rellena teléfono del cliente al entrar una llamada.
- La extensión se guarda en `users.extension` al hacer login.

### Zona horaria
- `config/app.php`: `'timezone' => 'America/Bogota'` (UTC-5)

---

## Historial de cambios relevantes

| Commit | Descripción |
|--------|-------------|
| `3ffda4f` | Set timezone America/Bogota — **referencia estable** |
| `7eb7bae` | Toggle activar/desactivar domicilio en el pedido |
| `88ad503` | Fix: ORDEN/VALOR es el total completo (sin descontar domicilio) |
| `a3ceb67` | Fix: usar `calldelivery` en lugar de `tiendadelivery` |
| `5eadb47` | Auto-fill ciudad y teléfono desde socket PBX |
| `80eedf5` | Modal tienda cerrada: informativo, agentes pueden continuar |
| `5b1046c` | tipo 1=radio / tipo 2=checkbox en modal de adicionales |
| `ce490b8` | Aplicar minimo, maximo y obligatorio en grupos de adicionales |

---

## Correcciones recibidas del usuario

- `tiendadelivery` era incorrecto para callcenter → siempre usar `calldelivery`.
- `ORDEN/VALOR` no debe descontar el domicilio — debe ser el total pleno.
- El diseño POS del menú se revirtió accidentalmente varias veces. Verificar el estado del archivo antes de editar.
- No modificar el layout general del menú al aplicar cambios en modales o lógica interna.

@extends('layouts.app')

@section('title', 'Registrar Servicio')
@section('header_title', 'Registrar Servicios Médicos & Estéticos')

@section('content')
    <div class="pos-layout">
        <!-- Left: Service List Catalog -->
        <div class="card">
            <div class="card-title">Servicios Disponibles</div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Servicio / Atención</th>
                            <th>Horarios Disponibles</th>
                            <th>Precio Sugerido</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $srv)
                            @php
                                // Suggesting a default price depending on the service type
                                $precioSugerido = 50.00;
                                if (str_contains($srv->tipo, 'Peluquería')) $precioSugerido = 70.00;
                                if (str_contains($srv->tipo, 'Vacunación')) $precioSugerido = 45.00;
                                if (str_contains($srv->tipo, 'Desparasitación')) $precioSugerido = 30.00;
                            @endphp
                            <tr>
                                <td><strong>{{ $srv->tipo }}</strong></td>
                                <td><span style="font-size:0.85rem; color:var(--color-muted);">{{ $srv->horarioAtencion ?? 'Todo el día' }}</span></td>
                                <td>Bs. {{ number_format($precioSugerido, 2) }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="addServiceToCart({{ json_encode($srv) }}, {{ $precioSugerido }})">
                                        Agregar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color:var(--color-muted);">No hay servicios configurados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Cart Info -->
        <div class="pos-cart">
            <div class="card-title">Detalle del Ticket</div>
            
            <div class="pos-cart-items" id="service-cart-container">
                <p id="empty-cart-msg" style="text-align:center; color:var(--color-muted); padding: 2rem 0;">
                    No hay servicios cargados al ticket.
                </p>
            </div>

            <!-- Client select -->
            <div class="form-group" style="margin-top:1rem;">
                <label for="ci_cliente">Cliente (Dueño) *</label>
                <select id="ci_cliente" class="form-control" required onchange="filterMascotas()">
                    <option value="">Seleccione un Cliente</option>
                    @foreach($clientes as $cli)
                        <option value="{{ $cli->ci }}">{{ $cli->nombre }} {{ $cli->paterno }} (CI: {{ $cli->ci }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Pet select (filled dynamically) -->
            <div class="form-group">
                <label for="id_mascota">Mascota Paciente *</label>
                <select id="id_mascota" class="form-control" required>
                    <option value="">Primero seleccione un cliente</option>
                </select>
            </div>

            <!-- Payment selection -->
            <div class="form-group">
                <label for="metodo_pago">Método de Pago *</label>
                <select id="metodo_pago" class="form-control" required>
                    <option value="efectivo">Efectivo</option>
                    <option value="qr">Pago (QR)</option>
                </select>
            </div>

            <!-- Total row -->
            <div class="pos-total-row">
                <span>Total Ticket:</span>
                <span id="grand-total-services">Bs. 0.00</span>
            </div>

            <button class="btn btn-primary" style="width:100%; padding:0.8rem; background-color:#6366f1;" onclick="processServiceCheckout()">
                Registrar Servicio y Pago
            </button>
        </div>
    </div>

    <!-- Payment QR Modal -->
    <div class="modal-overlay" id="qr-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Cobro QR Servicio</h3>
                <button class="modal-close" onclick="closeQRModal()">&times;</button>
            </div>
            <div class="qr-section">
                <p style="text-align:center; font-size:0.9rem; color:var(--color-muted);">
                    Escanee el código QR:
                </p>
                <h2 id="modal-total-text" style="color:var(--accent); font-weight:700;">Bs. 0.00</h2>
                
                  <div class="qr-code-box">
                      <img src="{{ asset('img/PagoQR.jpeg') }}"
                      alt="Código QR:"
                      class="qr-image">
                 </div>

                <div style="display:flex; gap:0.5rem; width:100%; margin-top:1rem;">
                    <button class="btn btn-primary" style="flex:1; background-color:#6366f1;" onclick="submitServiceData()">
                        Confirmar Pago Exitoso
                    </button>
                    <button class="btn btn-secondary" onclick="closeQRModal()">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-pet JSON map -->
    <script>
        const clientesMap = {!! json_encode($clientes) !!};
        let serviceCart = {};

        function filterMascotas() {
            const ci = document.getElementById('ci_cliente').value;
            const selectMascotas = document.getElementById('id_mascota');
            
            selectMascotas.innerHTML = '';
            
            if (!ci) {
                selectMascotas.innerHTML = '<option value="">Primero seleccione un cliente</option>';
                return;
            }

            const cliente = clientesMap.find(c => c.ci == ci);
            if (cliente && cliente.mascotas && cliente.mascotas.length > 0) {
                cliente.mascotas.forEach(pet => {
                    const opt = document.createElement('option');
                    opt.value = pet.id_mascota;
                    opt.innerText = pet.nombre + " (" + (pet.especie || 'N/A') + ")";
                    selectMascotas.appendChild(opt);
                });
            } else {
                selectMascotas.innerHTML = '<option value="">Sin mascotas registradas</option>';
            }
        }

        function addServiceToCart(service, price) {
            if (serviceCart[service.id_servicio]) {
                serviceCart[service.id_servicio].cantidad += 1;
            } else {
                serviceCart[service.id_servicio] = {
                    id_servicio: service.id_servicio,
                    tipo: service.tipo,
                    precio: parseFloat(price),
                    cantidad: 1
                };
            }
            renderServiceCart();
        }

        function updateServiceQty(id, qty) {
            qty = parseInt(qty);
            if (qty <= 0) {
                delete serviceCart[id];
            } else {
                serviceCart[id].cantidad = qty;
            }
            renderServiceCart();
        }

        function updateServicePrice(id, price) {
            price = parseFloat(price);
            if (isNaN(price) || price < 0) price = 0;
            if (serviceCart[id]) {
                serviceCart[id].precio = price;
            }
            renderServiceCart();
        }

        function removeServiceFromCart(id) {
            delete serviceCart[id];
            renderServiceCart();
        }

        function renderServiceCart() {
            const container = document.getElementById('service-cart-container');
            container.innerHTML = '';

            const keys = Object.keys(serviceCart);
            if (keys.length === 0) {
                container.innerHTML = `
                    <p id="empty-cart-msg" style="text-align:center; color:var(--color-muted); padding: 2rem 0;">
                        No hay servicios cargados al ticket.
                    </p>
                `;
                document.getElementById('grand-total-services').innerText = 'Bs. 0.00';
                return;
            }

            let total = 0;
            keys.forEach(key => {
                const item = serviceCart[key];
                const subtotal = item.precio * item.cantidad;
                total += subtotal;

                const row = document.createElement('div');
                row.className = 'pos-item-row';
                row.innerHTML = `
                    <div class="pos-item-info" style="max-width:50%;">
                        <div class="pos-item-name">${item.tipo}</div>
                        <div style="display:flex; align-items:center; gap:0.25rem; margin-top:2px;">
                            <span style="font-size:0.8rem; color:var(--color-muted);">Bs.</span>
                            <input type="number" class="pos-qty-input" style="width:70px; font-size:0.8rem; padding:1px 3px;" min="0" step="0.5" value="${item.precio}" onchange="updateServicePrice(${item.id_servicio}, this.value)">
                        </div>
                    </div>
                    <div class="pos-item-actions">
                        <input type="number" class="pos-qty-input" min="1" value="${item.cantidad}" onchange="updateServiceQty(${item.id_servicio}, this.value)">
                        <button class="btn btn-danger btn-sm btn-icon" onclick="removeServiceFromCart(${item.id_servicio})" style="padding: 0.25rem 0.5rem;">
                            &times;
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });

            document.getElementById('grand-total-services').innerText = 'Bs. ' + total.toFixed(2);
        }

        function processServiceCheckout() {
            const keys = Object.keys(serviceCart);
            if (keys.length === 0) {
                alert("Debe agregar al menos un servicio al ticket.");
                return;
            }

            const ci = document.getElementById('ci_cliente').value;
            if (!ci) {
                alert("Por favor, seleccione un cliente.");
                return;
            }

            const petId = document.getElementById('id_mascota').value;
            if (!petId || petId === "Sin mascotas registradas") {
                alert("Debe seleccionar una mascota válida para este servicio.");
                return;
            }

            const metodo = document.getElementById('metodo_pago').value;

            if (metodo === 'qr') {
                let totalText = document.getElementById('grand-total-services').innerText;
                document.getElementById('modal-total-text').innerText = totalText;
                document.getElementById('qr-modal').classList.add('active');
            } else {
                submitServiceData();
            }
        }

        function closeQRModal() {
            document.getElementById('qr-modal').classList.remove('active');
        }

        function submitServiceData() {
            const ci = document.getElementById('ci_cliente').value;
            const petId = document.getElementById('id_mascota').value;
            const metodo = document.getElementById('metodo_pago').value;

            const payload = {
                ci: ci,
                id_mascota: petId,
                metodo_pago: metodo,
                servicios: Object.values(serviceCart),
                _token: "{{ csrf_token() }}"
            };

            fetch("{{ route('empleado.servicio.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeQRModal();
                    // Redirect to ticket view
                    window.location.href = "/empleado/servicio/ticket/" + data.id_nota;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => {
                alert("Ocurrió un error al registrar el servicio.");
                console.error(err);
            });
        }
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Registrar Venta')
@section('header_title', 'Registrar Venta de Productos')

@section('content')
    <div class="pos-layout">
        <!-- Left: Product List to Select -->
        <div class="card">
            <div class="card-title">Catálogo de Productos</div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Precio Unit.</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $prod)
                            <tr>
                                <td>
                                    <strong>{{ $prod->nombre }}</strong>
                                    <div style="font-size:0.8rem; color:var(--color-muted);">{{ $prod->descripcion ?? '-' }}</div>
                                </td>
                                <td>{{ $prod->categoria->nombre ?? 'N/A' }}</td>
                                <td>
                                    @if($prod->stock <= 0)
                                        <span class="badge badge-stock-none">Agotado</span>
                                    @elseif($prod->stock < 10)
                                        <span class="badge badge-stock-low">{{ $prod->stock }} uds</span>
                                    @else
                                        <span class="badge badge-stock-good">{{ $prod->stock }} uds</span>
                                    @endif
                                </td>
                                <td>Bs. {{ number_format($prod->precio, 2) }}</td>
                                <td>
                                    @if($prod->stock > 0)
                                        <button class="btn btn-primary btn-sm" onclick="addToCart({{ json_encode($prod) }})">
                                            Agregar
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>Agotado</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:var(--color-muted);">No hay productos registrados en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Cart & Checkout Info -->
        <div class="pos-cart">
            <div class="card-title">Carrito de Compras</div>
            
            <div class="pos-cart-items" id="cart-container">
                <!-- Cart items filled via JS -->
                <p id="empty-cart-msg" style="text-align:center; color:var(--color-muted); padding: 2rem 0;">
                    El carrito está vacío. Agregue productos del catálogo.
                </p>
            </div>

            <!-- Client selection -->
            <div class="form-group" style="margin-top: 1rem;">
                <label for="ci_cliente">Cliente (Opcional) *</label>
                <select id="ci_cliente" class="form-control">
                    <option value="">Cliente ocasional</option>
                    @foreach($clientes as $cli)
                        <option value="{{ $cli->ci }}">{{ $cli->nombre }} {{ $cli->paterno }} (CI: {{ $cli->ci }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Payment selection -->
            <div class="form-group">
                <label for="metodo_pago">Método de Pago *</label>
                <select id="metodo_pago" class="form-control" required onchange="handlePaymentChange()">
                    <option value="efectivo">Efectivo</option>
                    <option value="qr">Pago Simple (QR)</option>
                </select>
            </div>

            <!-- Total display -->
            <div class="pos-total-row">
                <span>Total:</span>
                <span id="grand-total">Bs. 0.00</span>
            </div>

            <button class="btn btn-primary" style="width:100%; padding:0.8rem; background-color:#10b981;" onclick="processCheckout()">
                Registrar Venta y Pago
            </button>
        </div>
    </div>

    <!-- Pago de QR-->
    <div class="modal-overlay" id="qr-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Cobro QR </h3>
                <button class="modal-close" onclick="closeQRModal()">&times;</button>
            </div>
            <div class="qr-section">
                <p style="text-align:center; font-size:0.9rem; color:var(--color-muted);">
                    Escanee el código QR:
                </p>
                <h2 id="modal-total-text" style="color:var(--accent); font-weight:700;">Bs. 0.00</h2>
                
                <div class="qr-code-box">
                   <img src="{{ asset('img/PagoQR.jpeg') }}"
                    alt="Código QR de pago"
                   class="qr-image">
                </div>

                <div style="display:flex; gap:0.5rem; width:100%; margin-top:1rem;">
                    <button class="btn btn-primary" style="flex:1;" onclick="submitSalesData()">
                        Confirmar Pago Exitoso
                    </button>
                    <button class="btn btn-secondary" onclick="closeQRModal()">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Cart Logic -->
    <script>
        let cart = {};

        function addToCart(product) {
            if (cart[product.id_producto]) {
                if (cart[product.id_producto].cantidad < product.stock) {
                    cart[product.id_producto].cantidad += 1;
                } else {
                    alert("No puede agregar más de este producto. Stock límite alcanzado (" + product.stock + " uds).");
                }
            } else {
                cart[product.id_producto] = {
                    id_producto: product.id_producto,
                    nombre: product.nombre,
                    precio: parseFloat(product.precio),
                    stock: product.stock,
                    cantidad: 1
                };
            }
            renderCart();
        }

        function updateQty(id, qty) {
            qty = parseInt(qty);
            if (qty <= 0) {
                delete cart[id];
            } else {
                const maxStock = cart[id].stock;
                if (qty > maxStock) {
                    alert("Stock insuficiente. Límite: " + maxStock);
                    cart[id].cantidad = maxStock;
                } else {
                    cart[id].cantidad = qty;
                }
            }
            renderCart();
        }

        function removeFromCart(id) {
            delete cart[id];
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-container');
            container.innerHTML = '';
            
            const keys = Object.keys(cart);
            if (keys.length === 0) {
                container.innerHTML = `
                    <p id="empty-cart-msg" style="text-align:center; color:var(--color-muted); padding: 2rem 0;">
                        El carrito está vacío. Agregue productos del catálogo.
                    </p>
                `;
                document.getElementById('grand-total').innerText = 'Bs. 0.00';
                return;
            }

            let total = 0;
            keys.forEach(key => {
                const item = cart[key];
                const subtotal = item.precio * item.cantidad;
                total += subtotal;

                const row = document.createElement('div');
                row.className = 'pos-item-row';
                row.innerHTML = `
                    <div class="pos-item-info">
                        <div class="pos-item-name">${item.nombre}</div>
                        <div class="pos-item-price">Bs. ${item.precio.toFixed(2)} c/u</div>
                    </div>
                    <div class="pos-item-actions">
                        <input type="number" class="pos-qty-input" min="1" max="${item.stock}" value="${item.cantidad}" onchange="updateQty(${item.id_producto}, this.value)">
                        <button class="btn btn-danger btn-sm btn-icon" onclick="removeFromCart(${item.id_producto})" style="padding: 0.25rem 0.5rem;">
                            &times;
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });

            document.getElementById('grand-total').innerText = 'Bs. ' + total.toFixed(2);
        }

        function handlePaymentChange() {
            // Placeholder logic if needed
        }

        function processCheckout() {
            const keys = Object.keys(cart);
            if (keys.length === 0) {
                alert("Debe agregar al menos un producto al carrito.");
                return;
            }

            const ci = document.getElementById('ci_cliente').value;

            const metodo = document.getElementById('metodo_pago').value;
            
            if (metodo === 'qr') {
                // Show QR code modal
                let totalText = document.getElementById('grand-total').innerText;
                document.getElementById('modal-total-text').innerText = totalText;
                document.getElementById('qr-modal').classList.add('active');
            } else {
                // Cash checkout
                submitSalesData();
            }
        }

        function closeQRModal() {
            document.getElementById('qr-modal').classList.remove('active');
        }

        function submitSalesData() {
            const ci = document.getElementById('ci_cliente').value;
            const metodo = document.getElementById('metodo_pago').value;
            
            const payload = {
                ci: ci,
                metodo_pago: metodo,
                productos: Object.values(cart),
                _token: "{{ csrf_token() }}"
            };

            fetch("{{ route('empleado.venta.store') }}", {
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
                    // Redirect to print receipt
                    window.location.href = "/empleado/venta/ticket/" + data.id_venta;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => {
                alert("Ocurrió un error al procesar la venta.");
                console.error(err);
            });
        }
    </script>
@endsection

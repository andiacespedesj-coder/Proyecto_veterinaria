@extends('layouts.app')

@section('title', 'Generar Reportes')
@section('header_title', 'Reportes del Sistema')

@section('content')
    <div class="card no-print">
        <div class="card-title" style="margin-bottom: 0.5rem;">
            <span>Reportes Disponibles</span>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:2px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Imprimir Reporte Seleccionado
            </button>
        </div>
        <p style="color:var(--color-muted); font-size:0.9rem; margin-bottom:1rem;">
            Seleccione el reporte que desea visualizar y exportar. Al hacer clic en "Imprimir", se adaptará al formato físico.
        </p>

        <!-- Tab headers -->
        <div style="display:flex; gap:0.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            <button class="btn btn-secondary tab-btn active" onclick="switchReport('productos', this)">
                Reporte General de Productos
            </button>
            <button class="btn btn-secondary tab-btn" onclick="switchReport('medicamentos', this)">
                Reporte de Medicamentos
            </button>
            <button class="btn btn-secondary tab-btn" onclick="switchReport('servicios', this)">
                Reporte de Servicios Prestados
            </button>
        </div>
    </div>

    <!-- REPORT 1: PRODUCTOS GENERAL -->
    <div id="report-productos" class="card report-section">
        <div class="report-header" style="display:none; text-align:center; margin-bottom: 1.5rem;">
            <h1 style="color:var(--accent);">PROYECTO_VETERINARIA2</h1>
            <h2>Reporte General de Productos e Inventario</h2>
            <p>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        </div>
        
        <div class="card-title no-print">Reporte General de Productos</div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Stock Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reporteProductos as $p)
                        <tr>
                            <td>#{{ $p['id_producto'] }}</td>
                            <td><strong>{{ $p['nombre'] }}</strong></td>
                            <td>{{ $p['categoria'] }}</td>
                            <td>{{ $p['descripcion'] ?? '-' }}</td>
                            <td>
                                <span style="font-weight:700;">{{ $p['stock'] }} uds</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--color-muted);">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- REPORT 2: MEDICAMENTOS -->
    <div id="report-medicamentos" class="card report-section" style="display:none;">
        <div class="report-header" style="display:none; text-align:center; margin-bottom: 1.5rem;">
            <h1 style="color:var(--accent);">PROYECTO_VETERINARIA2</h1>
            <h2>Reporte de Stock de Medicamentos</h2>
            <p>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        </div>

        <div class="card-title no-print">Reporte de Medicamentos</div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre Medicamento</th>
                        <th>Descripción</th>
                        <th>Stock Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reporteMedicamentos as $m)
                        <tr>
                            <td>#{{ $m['id_producto'] }}</td>
                            <td><strong>{{ $m['nombre'] }}</strong></td>
                            <td>{{ $m['descripcion'] ?? '-' }}</td>
                            <td>
                                <span style="font-weight:700; color:var(--accent);">{{ $m['stock'] }} uds</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:var(--color-muted);">No hay medicamentos registrados en stock.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- REPORT 3: SERVICIOS -->
    <div id="report-servicios" class="card report-section" style="display:none;">
        <div class="report-header" style="display:none; text-align:center; margin-bottom: 1.5rem;">
            <h1 style="color:var(--accent);">PROYECTO_VETERINARIA2</h1>
            <h2>Reporte de Servicios Prestados</h2>
            <p>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        </div>

        <div class="card-title no-print">Reporte de Servicios Prestados</div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo de Servicio</th>
                        <th>Horario de Atención</th>
                        <th>Cantidad de Atenciones</th>
                        <th>Ingresos Totales</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reporteServicios as $s)
                        <tr>
                            <td><strong>{{ $s->tipo }}</strong></td>
                            <td>{{ $s->horarioAtencion ?? 'N/A' }}</td>
                            <td>{{ $s->total_servicios }} atenciones</td>
                            <td>
                                <span style="font-weight:700; color:var(--success);">Bs. {{ number_format($s->total_monto, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:var(--color-muted);">No hay servicios registrados en el sistema.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        /* Tabs active coloring helper */
        .tab-btn.active {
            background-color: var(--accent);
            color: #ffffff;
            border-color: var(--accent);
        }

        /* Print formatting overlay for report sections */
        @media print {
            .report-section {
                display: none !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            /* Show ONLY the selected active report section when printing */
            #report-productos.print-active,
            #report-medicamentos.print-active,
            #report-servicios.print-active {
                display: block !important;
            }
            .report-header {
                display: block !important;
            }
        }
    </style>

    <script>
        let activeReportId = 'productos';

        // Tag active report for print stylesheet
        document.getElementById('report-productos').classList.add('print-active');

        function switchReport(reportName, btn) {
            // Hide all reports
            document.querySelectorAll('.report-section').forEach(section => {
                section.style.display = 'none';
                section.classList.remove('print-active');
            });

            // Show selected report
            const selectedSection = document.getElementById('report-' + reportName);
            selectedSection.style.display = 'block';
            selectedSection.classList.add('print-active');

            // Deactivate all tab buttons
            document.querySelectorAll('.tab-btn').forEach(button => {
                button.classList.remove('active');
            });

            // Activate current tab button
            btn.classList.add('active');
            activeReportId = reportName;
        }
    </script>
@endsection

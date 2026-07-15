<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AuditTrail;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Roles;
use App\Repositories\ReporteRepository;
use App\Services\AuditoriaService;
use App\Services\ExcelXmlExporter;
use App\Services\ReporteService;

final class ReporteController extends Controller
{
    public function index(): void
    {
        $this->requireAdministrator();

        $data = $this->buildService()->dashboard();

        $this->view(
            'reportes/index',
            [
                'title' => 'Reportes y estadísticas',
                'summary' => $data['summary'],
                'categories' => $data['categories'],
            ]
        );
    }

    public function inventory(): void
    {
        $this->requireAdministrator();

        $data = $this->buildService()->inventory($_GET);

        $this->view(
            'reportes/inventory',
            [
                'title' => 'Reporte de inventario',
                ...$data,
            ]
        );
    }

    public function depreciation(): void
    {
        $this->requireAdministrator();

        $data = $this->buildService()->depreciation($_GET);

        $this->view(
            'reportes/depreciation',
            [
                'title' => 'Reporte de depreciación',
                ...$data,
            ]
        );
    }

    public function needs(): void
    {
        $this->requireAdministrator();

        $data = $this->buildService()->needs($_GET);

        $this->view(
            'reportes/needs',
            [
                'title' => 'Presupuesto de necesidades',
                ...$data,
            ]
        );
    }

    public function movements(): void
    {
        $this->requireAdministrator();

        $data = $this->buildService()->movements($_GET);

        $this->view(
            'reportes/movements',
            [
                'title' => 'Movimientos de activos',
                ...$data,
            ]
        );
    }

    public function accesses(): void
    {
        $this->requireAdministrator();

        $data = $this->buildService()->accesses($_GET);

        $this->view(
            'reportes/accesses',
            [
                'title' => 'Historial de accesos',
                ...$data,
            ]
        );
    }

    public function audit(): void
    {
        $this->requireAdministrator();

        $data = $this->buildService()->audit($_GET);
        $integrity = AuditoriaService::build()
            ->verifyIntegrity();

        $this->view(
            'reportes/audit',
            [
                'title' => 'Bitácora de auditoría',
                'integrity' => $integrity,
                ...$data,
            ]
        );
    }

    public function export(): void
    {
        $this->requireAdministrator();

        $type = strtolower(
            trim((string) ($_GET['tipo'] ?? ''))
        );

        [$sheetName, $headers, $rows, $filename] =
            match ($type) {
                'inventario' => $this->inventoryExport(),
                'depreciacion' => $this->depreciationExport(),
                'necesidades' => $this->needsExport(),
                'movimientos' => $this->movementsExport(),
                'accesos' => $this->accessesExport(),
                'auditoria' => $this->auditExport(),
                default => $this->inventoryExport(),
            };

        AuditTrail::recordReadEvent([
            'modulo' => 'REPORTES',
            'accion' => 'EXPORTAR_' . strtoupper($type ?: 'INVENTARIO'),
            'tablaAfectada' => null,
            'idRegistro' => null,
            'descripcion' =>
                'Se exportó un reporte en formato compatible con Excel.',
            'datosNuevos' => [
                'tipo' => $type ?: 'inventario',
                'filtros' => $this->safeQueryData($_GET),
            ],
        ]);

        $content = ExcelXmlExporter::build(
            $sheetName,
            $headers,
            $rows
        );

        header(
            'Content-Type: application/vnd.ms-excel; charset=UTF-8'
        );
        header(
            'Content-Disposition: attachment; filename="'
            . $filename
            . '"'
        );
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        echo $content;

        exit;
    }

    private function inventoryExport(): array
    {
        $data = $this->buildService()->inventory($_GET);

        $rows = array_map(
            static fn (array $row): array => [
                $row['codigoActivo'],
                $row['numeroSerie'] ?? '',
                $row['nombreCategoria'],
                $row['nombreSubcategoria'],
                $row['nombreProducto'],
                trim(
                    ($row['marca'] ?? '')
                    . ' '
                    . ($row['modelo'] ?? '')
                ),
                $row['tipoProducto'],
                $row['nombreEstado'],
                $row['nombreUbicacion'] ?? '',
                $row['nombreColaborador'] ?? '',
                (float) $row['costo'],
                (float) $row['valorResidual'],
                $row['fechaAdquisicion'],
                $row['fechaFinVidaUtil'] ?? '',
            ],
            $data['rows']
        );

        return [
            'Inventario',
            [
                'Código',
                'Serie',
                'Categoría',
                'Subcategoría',
                'Producto',
                'Marca / modelo',
                'Tipo',
                'Estado',
                'Ubicación',
                'Custodio',
                'Costo',
                'Valor residual',
                'Fecha adquisición',
                'Fin de vida útil',
            ],
            $rows,
            'reporte_inventario_' . date('Ymd_His') . '.xls',
        ];
    }

    private function depreciationExport(): array
    {
        $data = $this->buildService()->depreciation($_GET);

        $rows = array_map(
            static fn (array $row): array => [
                $row['codigoActivo'],
                $row['nombreCategoria'],
                $row['nombreProducto'],
                trim(
                    ($row['marca'] ?? '')
                    . ' '
                    . ($row['modelo'] ?? '')
                ),
                $row['nombreEstado'],
                (float) $row['costo'],
                (float) $row['valorResidual'],
                $row['fechaAdquisicion'],
                $row['vidaUtilMesesAplicada'],
                $row['fechaFinVidaUtil'],
                (int) $row['diasRestantes'],
            ],
            $data['rows']
        );

        return [
            'Depreciación',
            [
                'Código',
                'Categoría',
                'Producto',
                'Marca / modelo',
                'Estado',
                'Costo',
                'Valor residual',
                'Fecha adquisición',
                'Vida útil (meses)',
                'Fin de vida útil',
                'Días restantes',
            ],
            $rows,
            'reporte_depreciacion_' . date('Ymd_His') . '.xls',
        ];
    }

    private function needsExport(): array
    {
        $data = $this->buildService()->needs($_GET);

        $rows = array_map(
            static fn (array $row): array => [
                (int) $row['idSolicitud'],
                $row['nombreColaborador'],
                $row['departamento'] ?? '',
                $row['tipoSolicitud'],
                $row['titulo'],
                $row['nombreSubcategoria'] ?? '',
                $row['nombreProducto'] ?? '',
                (int) $row['cantidad'],
                $row['prioridad'],
                $row['periodoNecesidad'],
                $row['anioPresupuestado'] ?? '',
                $row['nombreEstado'],
                $row['costoEstimado'] !== null
                    ? (float) $row['costoEstimado']
                    : '',
                $row['fechaSolicitud'],
            ],
            $data['rows']
        );

        return [
            'Necesidades',
            [
                'Solicitud',
                'Colaborador',
                'Departamento',
                'Tipo',
                'Título',
                'Subcategoría',
                'Producto',
                'Cantidad',
                'Prioridad',
                'Período',
                'Año presupuestado',
                'Estado',
                'Costo unitario estimado',
                'Fecha solicitud',
            ],
            $rows,
            'presupuesto_necesidades_' . date('Ymd_His') . '.xls',
        ];
    }

    private function movementsExport(): array
    {
        $data = $this->buildService()->movements($_GET);

        $rows = array_map(
            static fn (array $row): array => [
                (int) $row['idMovimiento'],
                $row['fechaMovimiento'],
                $row['tipoMovimiento'],
                $row['codigoActivo'],
                $row['nombreProducto'],
                $row['nombreUsuario'],
                $row['estadoAnterior'] ?? '',
                $row['estadoNuevo'] ?? '',
                $row['ubicacionAnterior'] ?? '',
                $row['ubicacionNueva'] ?? '',
                $row['descripcion'] ?? '',
            ],
            $data['rows']
        );

        return [
            'Movimientos',
            [
                'ID',
                'Fecha',
                'Tipo',
                'Código activo',
                'Producto',
                'Usuario',
                'Estado anterior',
                'Estado nuevo',
                'Ubicación anterior',
                'Ubicación nueva',
                'Descripción',
            ],
            $rows,
            'movimientos_activos_' . date('Ymd_His') . '.xls',
        ];
    }

    private function accessesExport(): array
    {
        $data = $this->buildService()->accesses($_GET);

        $rows = array_map(
            static fn (array $row): array => [
                (int) $row['idHistorialLogin'],
                $row['fechaIntento'],
                $row['usuarioIngresado'],
                $row['nombreUsuario'] ?? '',
                $row['direccionIP'],
                (bool) $row['exito'] ? 'Exitoso' : 'Fallido',
                $row['descripcion'] ?? '',
                $row['userAgent'] ?? '',
            ],
            $data['rows']
        );

        return [
            'Accesos',
            [
                'ID',
                'Fecha',
                'Identificador ingresado',
                'Usuario',
                'Dirección IP',
                'Resultado',
                'Descripción',
                'Navegador',
            ],
            $rows,
            'historial_accesos_' . date('Ymd_His') . '.xls',
        ];
    }

    private function auditExport(): array
    {
        $data = $this->buildService()->audit($_GET);

        $rows = array_map(
            static fn (array $row): array => [
                (int) $row['idAuditoria'],
                $row['fecha'],
                $row['nombreUsuario'] ?? 'Sistema',
                $row['usuario'] ?? '',
                $row['modulo'],
                $row['accion'],
                $row['tablaAfectada'] ?? '',
                $row['idRegistro'] ?? '',
                $row['direccionIP'] ?? '',
                $row['descripcion'] ?? '',
                $row['hashAnterior'] ?? '',
                $row['hashRegistro'] ?? '',
                $row['algoritmoFirma'] ?? '',
            ],
            $data['rows']
        );

        return [
            'Auditoría',
            [
                'ID',
                'Fecha',
                'Usuario',
                'Cuenta',
                'Módulo',
                'Acción',
                'Tabla',
                'Registro',
                'IP',
                'Descripción',
                'Hash anterior',
                'Hash del registro',
                'Firma',
            ],
            $rows,
            'bitacora_auditoria_' . date('Ymd_His') . '.xls',
        ];
    }

    private function buildService(): ReporteService
    {
        return new ReporteService(
            new ReporteRepository()
        );
    }

    private function requireAdministrator(): void
    {
        Auth::requireRole(
            Roles::ADMINISTRADOR
        );
    }

    private function safeQueryData(array $query): array
    {
        $safe = [];

        foreach ($query as $key => $value) {
            if (is_array($value)) {
                continue;
            }

            $safe[(string) $key] = mb_substr(
                trim((string) $value),
                0,
                200
            );
        }

        return $safe;
    }
}

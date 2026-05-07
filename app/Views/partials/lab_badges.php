<!--
═══════════════════════════════════════════════════════════════════
Partial: lab_badges.php — v1.3
═══════════════════════════════════════════════════════════════════
Ruta: app/Views/partials/lab_badges.php

Corregido v1.3:
  - Eliminado el semáforo acumulado por jornada (no aplica)
  - Ahora muestra clasificación INDIVIDUAL del beneficiario actual
  - Widget "Estado clínico" en panel derecho con:
    * Indicador de anemia (se actualiza en tiempo real al escribir Hb)
    * Indicadores de riesgo cardiometabólico
  - Todo client-side, sin AJAX
═══════════════════════════════════════════════════════════════════
-->

<style>
    /* ═══ Badges de interpretación (debajo de inputs) ═══ */
    .lab-interp-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: .72rem;
        font-weight: 600;
        margin-top: 3px;
        line-height: 1.4;
    }
    .lab-interp-badge i { font-size: .78rem; }
    .lab-interp-badge-rango { font-weight: 400; opacity: .8; }

    .lab-interp-badge-normal   { background: #ecfdf3; color: #198754; }
    .lab-interp-badge-leve     { background: #fff8e1; color: #b8860b; }
    .lab-interp-badge-moderada { background: #fff3e0; color: #e65100; }
    .lab-interp-badge-severa   { background: #fef2f2; color: #dc3545; }
    .lab-interp-badge-revisar  { background: #f1f5f9; color: #64748b; }

    /* ═══ Panel estado clínico (panel derecho) ═══ */
    .lab-estado-card {
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 16px;
        padding: 16px;
    }
    .lab-estado-titulo {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .82rem;
        font-weight: 800;
        color: #101a61;
        margin-bottom: 12px;
    }
    .lab-estado-titulo i { font-size: 1rem; }

    /* Indicador individual */
    .lab-indicador {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        margin-bottom: 8px;
        transition: all .2s ease;
    }
    .lab-indicador:last-child { margin-bottom: 0; }

    .lab-indicador-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .lab-indicador-info {
        flex: 1;
        min-width: 0;
    }
    .lab-indicador-label {
        font-size: .72rem;
        font-weight: 600;
        color: #64748b;
    }
    .lab-indicador-valor {
        font-size: .82rem;
        font-weight: 800;
    }

    /* Colores de estado */
    .lab-ind-normal   { background: #f0fdf4; }
    .lab-ind-normal .lab-indicador-dot   { background: #16a34a; }
    .lab-ind-normal .lab-indicador-valor { color: #16a34a; }

    .lab-ind-leve     { background: #fffbeb; }
    .lab-ind-leve .lab-indicador-dot     { background: #d97706; }
    .lab-ind-leve .lab-indicador-valor   { color: #d97706; }

    .lab-ind-moderada { background: #fff7ed; }
    .lab-ind-moderada .lab-indicador-dot { background: #ea580c; }
    .lab-ind-moderada .lab-indicador-valor { color: #ea580c; }

    .lab-ind-severa   { background: #fef2f2; }
    .lab-ind-severa .lab-indicador-dot   { background: #dc2626; }
    .lab-ind-severa .lab-indicador-valor { color: #dc2626; }

    .lab-ind-revisar  { background: #f8fafc; }
    .lab-ind-revisar .lab-indicador-dot  { background: #94a3b8; }
    .lab-ind-revisar .lab-indicador-valor { color: #94a3b8; }

    .lab-ind-pending  { background: #f8fafc; }
    .lab-ind-pending .lab-indicador-dot  { background: #cbd5e1; }
    .lab-ind-pending .lab-indicador-valor { color: #94a3b8; font-weight: 400; font-style: italic; }

    /* Separador */
    .lab-separador {
        border: none;
        border-top: 1px dashed #e2e8f0;
        margin: 12px 0;
    }

    /* Tarjeta info beneficiario */
    .lab-info-benef {
        background: #f8fafd;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: .72rem;
        color: #64748b;
        line-height: 1.6;
    }
    .lab-info-benef strong { color: #1a202c; font-weight: 600; }
    .lab-info-benef i { font-size: .76rem; margin-right: 3px; }
</style>

<?php
    $fechaNac = $beneficiario['fecha_nacimiento'] ?? '';
    $edadDias = 0;
    $edadTexto = '—';
    $rangoTexto = '';

    if ($fechaNac) {
        $nacimiento = new \DateTime($fechaNac);
        $hoy        = new \DateTime();
        $diff       = $nacimiento->diff($hoy);
        $edadDias   = (int) $diff->days;

        if ($diff->y > 0) {
            $edadTexto = $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) $edadTexto .= ', ' . $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
        } elseif ($diff->m > 0) {
            $edadTexto = $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
        } else {
            $edadTexto = $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
        }

        if ($edadDias >= 183 && $edadDias <= 1825)      $rangoTexto = 'Rango A (6m–5a)';
        elseif ($edadDias >= 1826 && $edadDias <= 4382)  $rangoTexto = 'Rango B (5–12a)';
        elseif ($edadDias >= 4383 && $edadDias <= 5478)  $rangoTexto = 'Rango C (12–15a)';
        elseif ($edadDias >= 5479)                        $rangoTexto = 'Rango D (15+a)';
        else                                              $rangoTexto = 'Fuera de rango (<6m)';
    }

    $sexoBenef = strtoupper($beneficiario['sexo'] ?? 'M');
?>

<!-- Div oculto con datos del beneficiario para JS -->
<div id="labDatosBeneficiario"
     style="display:none;"
     data-fecha-nacimiento="<?= esc($fechaNac) ?>"
     data-sexo="<?= esc($sexoBenef) ?>"
     data-base-url="<?= base_url('/') ?>"
     data-jornada-id="<?= (int) ($jornadaId ?? 0) ?>"
     data-centro-id="<?= (int) ($centroId ?? 0) ?>">
</div>

<!-- Widget estado clínico individual -->
<div class="lab-estado-card">
    <div class="lab-estado-titulo">
        <i class="bi bi-activity"></i>
        Estado clínico
    </div>

    <!-- Indicador de anemia -->
    <div id="labIndicadorAnemia" class="lab-indicador lab-ind-pending">
        <div class="lab-indicador-dot"></div>
        <div class="lab-indicador-info">
            <div class="lab-indicador-label">Anemia</div>
            <div class="lab-indicador-valor">Pendiente — ingrese hemoglobina</div>
        </div>
    </div>

    <!-- Indicadores cardiometabólicos -->
    <div id="labIndicadorTri" class="lab-indicador lab-ind-pending" style="display:none;">
        <div class="lab-indicador-dot"></div>
        <div class="lab-indicador-info">
            <div class="lab-indicador-label">Triglicéridos</div>
            <div class="lab-indicador-valor">—</div>
        </div>
    </div>

    <div id="labIndicadorHdl" class="lab-indicador lab-ind-pending" style="display:none;">
        <div class="lab-indicador-dot"></div>
        <div class="lab-indicador-info">
            <div class="lab-indicador-label">HDL-Colesterol</div>
            <div class="lab-indicador-valor">—</div>
        </div>
    </div>

    <div id="labIndicadorLdl" class="lab-indicador lab-ind-pending" style="display:none;">
        <div class="lab-indicador-dot"></div>
        <div class="lab-indicador-info">
            <div class="lab-indicador-label">LDL-Colesterol</div>
            <div class="lab-indicador-valor">—</div>
        </div>
    </div>

    <div id="labIndicadorVldl" class="lab-indicador lab-ind-pending" style="display:none;">
        <div class="lab-indicador-dot"></div>
        <div class="lab-indicador-info">
            <div class="lab-indicador-label">VLDL-Colesterol</div>
            <div class="lab-indicador-valor">—</div>
        </div>
    </div>
</div>

<hr class="lab-separador">

<!-- Tarjeta datos del beneficiario -->
<div class="lab-info-benef">
    <i class="bi bi-info-circle"></i>
    <strong>Datos para interpretación:</strong><br>
    Edad: <strong><?= esc($edadTexto) ?></strong>
    (<?= number_format($edadDias) ?> días — <?= esc($rangoTexto) ?>)<br>
    Sexo: <strong><?= $sexoBenef === 'F' ? 'Femenino' : 'Masculino' ?></strong>
    <?php if ($sexoBenef === 'F' && $edadDias >= 5479): ?>
        · Embarazada: <strong id="labInfoEmbarazada">—</strong>
    <?php endif; ?>
</div>

<?php if ($sexoBenef === 'F' && $edadDias >= 5479): ?>
<script>
(function() {
    var sel = document.getElementById('campo_embarazada_lab');
    var span = document.getElementById('labInfoEmbarazada');
    if (!sel || !span) return;
    function actualizar() {
        span.textContent = sel.value === 's' ? 'Sí' : (sel.value === 'n' ? 'No' : '—');
    }
    sel.addEventListener('change', actualizar);
    actualizar();
})();
</script>
<?php endif; ?>
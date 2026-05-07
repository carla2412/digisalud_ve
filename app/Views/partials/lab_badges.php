<!--
═══════════════════════════════════════════════════════════════════
Partial: lab_badges.php
═══════════════════════════════════════════════════════════════════
Ruta: app/Views/evaluaciones/partials/lab_badges.php

Se incluye en formulario.php (panel derecho) SOLO cuando
tipoPesquisaId == 2 (Laboratorio / SANGUINEO_LAB).

Contiene:
  1. CSS para los badges de interpretación clínica
  2. CSS para el widget del semáforo
  3. Div oculto con datos del beneficiario (data-attributes)
  4. Contenedor del widget semáforo
  5. Tarjeta resumen de datos usados para la interpretación

NO modifica ningún estilo existente. Usa clases con prefijo "lab-"
para evitar colisiones.
═══════════════════════════════════════════════════════════════════
-->

<!-- ─── CSS Badges de interpretación ─── -->
<style>
    /* ═══ Badges de interpretación clínica (debajo de inputs) ═══ */
    .lab-badge {
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
    .lab-badge i {
        font-size: .78rem;
    }
    .lab-badge-rango {
        font-weight: 400;
        opacity: .8;
    }

    /* Colores por clasificación */
    .lab-badge-normal {
        background: #ecfdf3;
        color: #198754;
    }
    .lab-badge-leve {
        background: #fff8e1;
        color: #b8860b;
    }
    .lab-badge-moderada {
        background: #fff3e0;
        color: #e65100;
    }
    .lab-badge-severa {
        background: #fef2f2;
        color: #dc3545;
    }
    .lab-badge-revisar {
        background: #f1f5f9;
        color: #64748b;
    }

    /* ═══ Widget semáforo (panel derecho) ═══ */
    .lab-semaforo-header {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .78rem;
        color: #101a61;
        margin-bottom: 8px;
    }
    .lab-semaforo-header i {
        font-size: .88rem;
    }
    .lab-semaforo-total {
        margin-left: auto;
        font-weight: 400;
        font-size: .72rem;
        color: #8896a7;
    }

    .lab-semaforo-grid {
        display: flex;
        gap: 4px;
        margin-bottom: 8px;
    }
    .lab-sem-item {
        flex: 1;
        text-align: center;
        padding: 6px 2px;
        border-radius: 6px;
        min-width: 0;
    }
    .lab-sem-count {
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .lab-sem-label {
        font-size: .62rem;
        font-weight: 600;
        margin-top: 1px;
    }
    .lab-sem-pct {
        font-size: .6rem;
        opacity: .7;
    }

    /* Colores semáforo */
    .lab-sem-verde    { background: #ecfdf3; color: #198754; }
    .lab-sem-amarillo { background: #fff8e1; color: #b8860b; }
    .lab-sem-naranja  { background: #fff3e0; color: #e65100; }
    .lab-sem-rojo     { background: #fef2f2; color: #dc3545; }
    .lab-sem-gris     { background: #f1f5f9; color: #64748b; }

    /* Barra de proporción */
    .lab-semaforo-barra {
        height: 6px;
        border-radius: 3px;
        overflow: hidden;
        display: flex;
        background: #f1f5f9;
        margin-bottom: 6px;
    }
    .lab-barra-seg {
        height: 100%;
        transition: width .3s ease;
    }
    .lab-barra-verde    { background: #198754; }
    .lab-barra-amarillo { background: #ffc107; }
    .lab-barra-naranja  { background: #fd7e14; }
    .lab-barra-rojo     { background: #dc3545; }
    .lab-barra-gris     { background: #cbd5e1; }

    .lab-semaforo-nota {
        font-size: .62rem;
        color: #8896a7;
        line-height: 1.4;
    }

    .lab-semaforo-loading {
        font-size: .76rem;
        color: #8896a7;
        text-align: center;
        padding: 12px 0;
    }
    .lab-semaforo-loading i {
        margin-right: 4px;
    }

    /* ═══ Tarjeta de datos del beneficiario ═══ */
    .lab-info-benef {
        background: #f8fafd;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: .72rem;
        color: #64748b;
        line-height: 1.6;
    }
    .lab-info-benef strong {
        color: #1a202c;
        font-weight: 600;
    }
    .lab-info-benef i {
        font-size: .76rem;
        margin-right: 3px;
    }

    /* ═══ Separador ═══ */
    .lab-separador {
        border: none;
        border-top: 1px dashed #e2e8f0;
        margin: 12px 0;
    }
</style>

<?php
    // ─── Calcular edad en días ───
    $fechaNac = $beneficiario['fecha_nacimiento'] ?? '';
    $edadDias = 0;
    $edadTexto = '—';
    $rangoTexto = '';

    if ($fechaNac) {
        $nacimiento = new \DateTime($fechaNac);
        $hoy        = new \DateTime();
        $diff       = $nacimiento->diff($hoy);
        $edadDias   = (int) $diff->days;

        // Texto legible de edad
        if ($diff->y > 0) {
            $edadTexto = $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) $edadTexto .= ', ' . $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
        } elseif ($diff->m > 0) {
            $edadTexto = $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
        } else {
            $edadTexto = $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
        }

        // Rango de clasificación
        if ($edadDias >= 183 && $edadDias <= 1825)      $rangoTexto = 'Rango A (6m–5a)';
        elseif ($edadDias >= 1826 && $edadDias <= 4382)  $rangoTexto = 'Rango B (5–12a)';
        elseif ($edadDias >= 4383 && $edadDias <= 5478)  $rangoTexto = 'Rango C (12–15a)';
        elseif ($edadDias >= 5479)                        $rangoTexto = 'Rango D (15+a)';
        else                                              $rangoTexto = 'Fuera de rango (<6m)';
    }

    $sexoBenef = strtoupper($beneficiario['sexo'] ?? 'M');
?>

<!-- ─── Div oculto con datos del beneficiario para JS ─── -->
<div id="labDatosBeneficiario"
     style="display:none;"
     data-fecha-nacimiento="<?= esc($fechaNac) ?>"
     data-sexo="<?= esc($sexoBenef) ?>"
     data-base-url="<?= base_url('/') ?>"
     data-jornada-id="<?= (int) ($jornadaId ?? 0) ?>"
     data-centro-id="<?= (int) ($centroId ?? 0) ?>">
</div>

<!-- ─── Separador visual ─── -->
<hr class="lab-separador">

<!-- ─── Widget semáforo ─── -->
<div id="labSemaforoWidget">
    <div class="lab-semaforo-loading">
        <i class="bi bi-hourglass-split"></i> Cargando semáforo...
    </div>
</div>

<!-- ─── Separador visual ─── -->
<hr class="lab-separador">

<!-- ─── Tarjeta con datos del beneficiario usados ─── -->
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
<!-- Script para actualizar el texto de embarazada en la tarjeta -->
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
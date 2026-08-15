<?php

/**
 * Emitting companies used across quotations and administrative documents.
 * These are fixed by business — not editable from the admin panel — but
 * updating this file and clearing config cache reflects the change
 * everywhere.
 *
 * Each entry:
 *   label       — Shown in the selector dropdown.
 *   name        — Legal name printed at the top of the PDF.
 *   tax_id      — RIF (VE) / EIN (US).
 *   tax_id_label— Label shown next to tax_id ("RIF:" | "EIN:").
 *   address     — Street address (full, single line — the PDF handles wrap).
 *   phones      — Free-text phones line (may include multiple).
 */

return [

    've' => [
        'label'        => 'Venezuela (Activación y Servicios, C.A.)',
        'name'         => 'DISTRIBUIDORA BIT DE ACTIVACIÓN Y SERVICIOS, C.A',
        'tax_id'       => 'J402111843',
        'tax_id_label' => 'RIF',
        'address'      => 'Calle Industrial el Coliseo, C.C Coliseo, Nivel 4, Local 160, Sector Potrerito Medio Guadalupe',
        'phones'       => '0212.415.32.82 / 0424.182.64.08',
    ],

    'us' => [
        'label'        => 'United States (Distribuidora Bit Corp)',
        'name'         => 'DISTRIBUIDORA BIT CORP',
        // TODO: Reemplazar con el EIN real cuando el cliente lo proporcione.
        // Mientras esté en null, la línea no se pinta en el PDF.
        'tax_id'       => null,
        'tax_id_label' => 'EIN',
        'address'      => '230 NW 109TH AVE APT 206 MIAMI FL 33172 - 5255',
        // TODO: Reemplazar con el teléfono real cuando el cliente lo proporcione.
        'phones'       => null,
    ],

];

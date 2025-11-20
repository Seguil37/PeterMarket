<?php

namespace App\Support;

/**
 * Define las opciones de delivery disponibles en el checkout.
 */
class Delivery
{
    /**
     * Retorna las opciones de envío disponibles junto a sus costos.
     * El índice representa el valor que se guarda en BD.
     */
    public static function options(): array
    {
        return [
            'standard' => ['label' => 'Delivery estándar', 'cost' => 10.00],
            'express'  => ['label' => 'Delivery rápido',   'cost' => 18.00],
        ];
    }
}

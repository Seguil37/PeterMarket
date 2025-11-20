<?php

namespace App\Support;

/**
 * Define las opciones de delivery disponibles en el checkout.
 */
class Delivery
{
    public const MIN_TOTAL = 35.00;
    public const FREE_FROM = 45.00;
    public const BASE_COST = 5.00;

    /**
     * Retorna las opciones de envío disponibles junto a sus costos.
     * El índice representa el valor que se guarda en BD.
     */
    public static function options(float $cost = self::BASE_COST): array
    {
        return [
            'standard' => ['label' => 'Delivery a domicilio', 'cost' => $cost],
        ];
    }

    /**
     * Evalúa el subtotal de productos y devuelve disponibilidad y costo real de delivery.
     */
    public static function evaluate(float $productsSubtotal): array
    {
        if ($productsSubtotal < self::MIN_TOTAL) {
            return [
                'available' => false,
                'cost' => 0.00,
                'message' => 'El monto mínimo para delivery es S/ 35. Aumenta tu pedido o cambia a recojo en tienda.',
            ];
        }

        $cost = $productsSubtotal >= self::FREE_FROM ? 0.00 : self::BASE_COST;

        return [
            'available' => true,
            'cost' => $cost,
            'message' => $productsSubtotal >= self::FREE_FROM
                ? '¡Felicidades! Obtuviste delivery gratis 🎉'
                : 'Agrega un poco más a tu pedido para obtener delivery gratis ✨ (gratis desde S/ 45)',
        ];
    }

    /**
     * Reglas expuestas para vistas y mensajes.
     */
    public static function settings(): array
    {
        return [
            'min_total' => self::MIN_TOTAL,
            'free_from' => self::FREE_FROM,
            'base_cost' => self::BASE_COST,
        ];
    }
}

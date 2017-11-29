<?php

namespace App\Service;
use Illuminate\Support\Facades\Redis;

/**
 * Ценник
 */
class Price
{
    // шаг уменьшения цены
    const PRICE_STEP = 100;

    // класс => начальная стоимость
    const PRICES = [
        9  => 1550,
        10 => 1550,
        11 => 1700,
    ];

    // пороги, после которого цена начинает снижаться (ед. изм: кол-во занятий)
    const STEPS = [64, 96, 128];

    /**
     * $grade   – [9..11] класс
     * $part    – [1..4] какую часть взять
     * $chunk   – кол-во элементов в одной части (сколько взять)
     */
    public static function parse($grade, $part, $chunk)
    {
        // начальная цена в зависимости от класса
        $price = self::PRICES[$grade];

        $start = ($part - 1) * $chunk + 1;

        // устанавливаем начальную стоимость
        foreach (self::STEPS as $step) {
            if ($start > $step) {
                $price -= self::PRICE_STEP;
            }
        }

        $sum = 0;
        foreach(range($start, ($start + $chunk - 1)) as $step) {
            $sum += $price;
            // \Log::info("Lesson $step: {$price} rub. \t({$sum} rub.)");
            // уменьшаем ценник
            if (in_array($step, self::STEPS)) {
                $price -= self::PRICE_STEP;
            }
        }

        return $sum;
    }
}

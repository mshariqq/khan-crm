<?php

use App\Models\Currency;

if (!function_exists('app_format_money')) {
    function app_format_money($amount, $currency = null)
    {
        $amount = (float) ($amount ?? 0);

        if ($currency instanceof Currency) {
            $symbol = $currency->symbol ?? '$';
            $decimal = $currency->decimal_separator ?? '.';
            $thousand = $currency->thousand_separator ?? ',';
            $placement = $currency->placement ?? 'before';
        } elseif (is_object($currency) && isset($currency->symbol)) {
            $symbol = $currency->symbol ?? '$';
            $decimal = $currency->decimal_separator ?? '.';
            $thousand = $currency->thousand_separator ?? ',';
            $placement = $currency->placement ?? 'before';
        } else {
            $symbol = '$';
            $decimal = '.';
            $thousand = ',';
            $placement = 'before';
        }

        $formatted = number_format($amount, 2, $decimal, $thousand);

        if ($placement === 'after') {
            return $formatted . ' ' . $symbol;
        }

        return $symbol . $formatted;
    }
}

if (!function_exists('app_get_currency_code')) {
    function app_get_currency_code($currency = null)
    {
        if ($currency instanceof Currency) {
            return $currency->name ?? 'USD';
        }
        if (is_object($currency) && isset($currency->name)) {
            return $currency->name ?? 'USD';
        }
        return 'USD';
    }
}

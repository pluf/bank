<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
Pluf::loadFunction('Pluf_Shortcuts_RenderToResponse');

/**
 * یک نمونه جدید از پرداخت ایجاد می‌کند
 *
 * در صورتی که پیش از این نمونه‌ای برای پرداخت ایجاد شده باشد آن را به عنوان
 * نتیجه برمی‌گرداند.
 *
 * @param Bank_Receipt $object
 * @return Bank_Receipt
 */
function Bank_Shortcuts_receiptFactory($object)
{
    if ($object == null || ! isset($object))
        return new Bank_Receipt();
    return $object;
}

/**
 * یک موتور پرداخت را پیدا می‌کند.
 *
 * @param string $type
 * @throws Bank_Exception_EngineNotFound
 * @return Bank_Engine
 */
function Bank_Shortcuts_GetEngineOr404($type)
{
    $items = Bank_Service::engines();
    foreach ($items as $item) {
        if ($item->getType() === $type) {
            return $item;
        }
    }
    throw new Bank_Exception_EngineNotFound("Bank engine not found: " . $type);
}

/**
 *
 * @param unknown $id
 * @throws Pluf_HTTP_Error404
 * @return Bank_Backend
 */
function Bank_Shortcuts_GetBankOr404($id)
{
    $item = new Bank_Backend($id);
    if ((int) $id > 0 && $item->id == $id) {
        return $item;
    }
    throw new Pluf_HTTP_Error404("Backend not found (" . $id . ")");
}

/**
 *
 * @param unknown $id
 * @throws Pluf_HTTP_Error404
 * @return Bank_Receipt
 */
function Bank_Shortcuts_GetReceiptOr404($id)
{
    $item = new Bank_Receipt($id);
    if ((int) $id > 0 && $item->id == $id) {
        return $item;
    }
    throw new Pluf_HTTP_Error404("Receipt not found (" . $id . ")");
}

/**
 * Checks if given currencies are compatible.
 * Two currencies are compatible if both are same or one is 'IRR' and other is 'IRT'.
 *
 * @param string $currency1
 * @param string $currency2
 * @return boolean
 */
function Bank_Shortcuts_IsCurrenciesCompatible($currency1, $currency2)
{
    if ($currency1 === $currency2) {
        return true;
    }
    // The currencies 'IRR' and 'IRT' are compatible
    if (($currency1 === 'IRR' && $currency2 === 'IRT') || ($currency1 === 'IRT' && $currency2 === 'IRR')) {
        return true;
    }
    return false;
}

/**
 * Convert given value from given currency and returns the new amount in the $toCurrency if pissible
 * elese throws an exception. 
 * @param number $amount
 * @param string $fromCurrency
 * @param string $toCurrency
 * @throws \Pluf\Exception_BadRequest
 * @return number
 */
function Bank_Shortcuts_ConvertCurrency($amount, $fromCurrency, $toCurrency)
{
    if ($fromCurrency === $toCurrency) {
        return $amount;
    }
    // The currencies 'IRR' and 'IRT' are compatible
    if ($fromCurrency === 'IRR' && $toCurrency === 'IRT') {
        return $amount / 10.0;
    }
    if ($fromCurrency === 'IRT' && $toCurrency === 'IRR') {
        return $amount * 10;
    }
    throw new \Pluf\Exception_BadRequest('Could not convert amount from ' . $fromCurrency . 'to ' . $toCurrency);
}



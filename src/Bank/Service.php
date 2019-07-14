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

/**
 * سرویس پرداخت‌ها را برای ماژولهای داخلی سیستم ایجاد می کند.
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 *        
 */
class Bank_Service
{

    /**
     * یک پرداخت جدید ایجاد می‌کند
     *
     * روالی که برای ایجاد یک پرداخت دنبال می‌شه می‌تونه خیلی متفاوت باشه
     * و ساختارهای رو برای خودش ایجاد کنه. برای همین ما پارامترهای ارسالی در
     * در خواست رو هم ارسال می‌کنیم.
     *
     * پرداخت ایجاد شده بر اساس اطلاعاتی است که با متغیر $reciptParam ارسال
     * می‌شود. این پارامترها
     * باید به صورت یک آرایه بوده و شامل موارد زیر باشد:
     *
     * <pre><code>
     * $param = array(
     * 'amount' => 1000, // مقدار پرداخت به ریال
     * 'title' => 'payment title',
     * 'description' => 'description',
     * 'email' => 'user@email.address',
     * 'phone' => '0917222222',
     * 'callbackURL' => 'http://.....',
     * 'backend_id' => 2
     * );
     * </code></pre>
     *
     * <ul>
     * <li>*amount: مقدار بر اساس ریال</li>
     * <li>*title: عنوان پرداخت</li>
     * <li>*description: توضیحات</li>
     * <li>email: رایانامه مشتری</li>
     * <li>phone: شماره تماس مشتری</li>
     * <li>callbackURL: آدرسی که بعد از تکمیل باید فراخوانی شود</li>
     * <li>*backend: درگاه پرداخت مورد نظر</li>
     * </ul>
     *
     * در نهایت باید موجودیتی تعیین بشه که این پرداخت رو می‌خواهیم براش ایجاد
     * کنیم.
     *
     * @param array $param
     * @param Pluf_Model $owner
     * @return Bank_Receipt
     */
    public static function create($param, $owner = null, $ownerId = null)
    {
        $form = new Bank_Form_ReceiptNew($param);
        $receipt = $form->save();
        // Replace variables in the callback URL
        $m = new Mustache_Engine();
        $receipt->callbackURL = $m->render($receipt->callbackURL, $receipt->getData());
        // Request to engine to create receipt
        $backend = $receipt->get_backend();
        $engine = $backend->get_engine();
        $engine->create($receipt);
        if ($owner instanceof Pluf_Model) { // Pluf module
            $receipt->owner_class = $owner->getClass();
            $receipt->owner_id = $owner->getId();
        } elseif (! is_null($owner)) { // module
            $receipt->owner_class = $owner;
            $receipt->owner_id = $ownerId;
        }
        $receipt->update();
        return $receipt;
    }

    /**
     * حالت یک پرداخت را به روز می‌کند
     *
     * زمانی که یک پرداخت ایجاد می‌شود نیاز هست که بررسی کنیم که آیا پرداخت در
     * سمت بانک انجام شده است. این فراخوانی این بررسی رو انجام می‌ده و حالت
     * پرداخت رو به روز می‌کنه.
     * 
     * در صورتی که مشکلی در به روزرسانی حالت پرداخت به وجود آید مثلا بک‌اند مربوط به پرداخت 
     * از سیستم حذف شده باشد این تابع پرداخت رو بدون تغییر و به‌روزرسانی برمی‌گرداند.
     *
     * @param Bank_Receipt $receipt
     * @return Bank_Receipt
     */
    public static function update($receipt)
    {
        $backend = $receipt->get_backend();
        if ($backend !== null) {
            $engine = $backend->get_engine();
            if ($engine->update($receipt)) {
                $receipt->update();
            }
        }
        return $receipt;
    }

    /**
     * Finds recepts
     *
     * @param Pluf_Model $owner
     * @param integer $ownerId
     */
    public static function find($owner, $ownerId = null)
    {
        // get class
        if ($owner instanceof Pluf_Model) { // Pluf module
            $ownerClass = $owner->getClass();
            $ownerId = $owner->getId();
        } elseif (! is_null($owner)) { // module
            $ownerClass = $owner;
        }

        // get list
        $receipt = new Bank_Receipt();
        $q = new Pluf_SQL('owner_class=%s AND owner_id=%s', array(
            $ownerClass,
            $ownerId
        ));
        $list = $receipt->getList(array(
            'filter' => $q->gen()
        ));
        return $list;
    }

    /**
     * فهرست متورهای پرداخت موجود را تعیین می‌کند
     *
     * @return Bank_Engine_Mellat[]|Bank_Engine_Zarinpal[]
     */
    public static function engines()
    {
        return array(
            new Bank_Engine_Mellat(),
            new Bank_Engine_Zarinpal(),

            new Bank_Engine_PayPall(),
            new Bank_Engine_BitPay()
        );
    }
}
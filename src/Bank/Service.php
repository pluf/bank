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
        $receipt = $form->save(false);
        if ($owner instanceof Pluf_Model) { // Pluf module
            $receipt->owner_class = $owner->getClass();
            $receipt->owner_id = $owner->getId();
        } elseif (! is_null($owner)) { // module
            $receipt->owner_class = $owner;
            $receipt->owner_id = $ownerId;
        }
        // Replace variables in the callback URL
        $m = new Mustache_Engine();
        $receipt->callbackURL = $m->render($receipt->callbackURL, $receipt->getData());
        // Request to engine to create receipt
        $backend = $receipt->get_backend();
        $engine = $backend->get_engine();
        $engine->create($receipt);
        $receipt->create();
        $receipt->_a['cols']['secure_id']['readable'] = true;
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
    
    /**
     * Finds and returns a wallet with given currency and owned by given user.
     * If there are multiple wallet with given properties it returns first of them as result.
     * @param User_Account $user
     * @param string $currency
     * @return boolean|Bank_Wallet returns matched wallet or false if there is no wallet with given properties
     */
    public static function getWallet($user, $currency){
        $model = new Bank_Wallet();
        $where = new Pluf_SQL('`owner_id`=%s AND `currency`=%s', array(
            $model->_toDb($user->id, 'owner_id'),
            $model->_toDb($currency, 'currency')
        ));
        $wallets = $model->getList(array(
            'filter' => $where->gen()
        ));
        if ($wallets === false or count($wallets) < 1) {
            return false;
        }
        return $wallets[0];
    }
    
    /**
     *
     * @param User_Account $user
     * @param string $currency
     * @return boolean|Bank_Wallet
     */
    /**
     * Finds and returns all wallets with given currency and owned by given user
     * @param User_Account $user
     * @param string $currency
     * @param boolean $includeDeleted determine that deleted wallets should be contained
     * @return ArrayObject returns all wallets matched with given properties
     */
    public static function getWallets($user, $currency, $includeDeleted = FALSE){
        $model = new Bank_Wallet();
        // example: owner_id=1 AND currency='BTC'
        $where = new Pluf_SQL('`owner_id`=%s AND `currency`=%s', array(
            $model->_toDb($user->id, 'owner_id'),
            $model->_toDb($currency, 'currency')
        ));
        if(!$includeDeleted){
            $where->SAnd(new Pluf_SQL('`deleted` = %s', array($model->_toDb(FALSE, 'deleted'))));
        }
        $wallets = $model->getList(array(
            'filter' => $where->gen(),
            'order' => '`id` DESC'
        ));
        if ($wallets === false or count($wallets) < 1) {
            return new ArrayObject();
        }
        return $wallets;
    }
}
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
namespace Pluf\Test\Basic;

use Pluf\Test\TestCase;
use Pluf\Exception;
use Bank_Backend;
use Bank_Engine;
use Bank_Engine_BitPay;
use Bank_Engine_Mellat;
use Bank_Engine_PayIr;
use Bank_Engine_PayPall;
use Bank_Engine_Zarinpal;
use Bank_Monitor;
use Bank_Receipt;
use Bank_Service;
use Bank_Transfer;
use Bank_Wallet;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;


class ApiTest extends TestCase
{

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration();
        $m->install();

        // Test user
        $user = new User_Account();
        $user->login = 'test';
        $user->is_active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        // Credential of user
        $credit = new User_Credential();
        $credit->setFromFormData(array(
            'account_id' => $user->id
        ));
        $credit->setPassword('test');
        if (true !== $credit->create()) {
            throw new Exception();
        }

        $per = User_Role::getFromString('tenant.owner');
        $user->setAssoc($per);
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration();
        $m->unInstall();
    }

    /**
     *
     * @test
     */
    public function testClassInstance()
    {
        $object = new Bank_Backend();
        $this->assertTrue(isset($object), 'Bank_Backend could not be created!');
        $object = new Bank_Engine();
        $this->assertTrue(isset($object), 'Bank_Engine could not be created!');
        $object = new Bank_Engine_BitPay();
        $this->assertTrue(isset($object), 'Bank_Engine_BitPay could not be created!');
        $object = new Bank_Engine_Mellat();
        $this->assertTrue(isset($object), 'Bank_Engine_Mellat could not be created!');
        $object = new Bank_Engine_PayIr();
        $this->assertTrue(isset($object), 'Bank_Engine_PayIr could not be created!');
        $object = new Bank_Engine_PayPall();
        $this->assertTrue(isset($object), 'Bank_Engine_PayPall could not be created!');
        $object = new Bank_Engine_Zarinpal();
        $this->assertTrue(isset($object), 'Bank_Engine_Zarinpal could not be created!');
        $object = new Bank_Monitor();
        $this->assertTrue(isset($object), 'Bank_Monitor could not be created!');
        $object = new Bank_Receipt();
        $this->assertTrue(isset($object), 'Bank_Receipt could not be created!');
        $object = new Bank_Service();
        $this->assertTrue(isset($object), 'Bank_Service could not be created!');
        $object = new Bank_Transfer();
        $this->assertTrue(isset($object), 'Bank_Transfer could not be created!');
        $object = new Bank_Wallet();
        $this->assertTrue(isset($object), 'Bank_Wallet could not be created!');
    }

    /**
     * Create a recept by paypall
     *
     * @test
     */
    public function testCreatePayPallRecept()
    {
        // create backend
        $backendData = new Bank_Backend();
        $backendData->engine = 'paypall';
        $backendData->currency = 'USD';
        $backendData->setMeta('ClientId', 'yyy');
        $backendData->setMeta('ClientSecret', 'xxxx');
        $backendData->deleted = false;
        $backendData->create();

        $rec = Bank_Service::create(array(
            'title' => 'title test',
            'description' => 'description',
            'email' => 'maso@dpq.co.ir',
            'phone' => '+989177374087',
            'callbackURL' => 'http://localhost',
            'backend_id' => $backendData->id,
            'amount' => 100
        ));

        $backend = new Bank_Engine_PayPall();
        $backend->create($rec);

        $rec->delete();
        $backendData->delete();
    }


    /**
     * Execute a recept by paypall
     *
     * @test
     */
    public function testExecutePayPallRecept()
    {
        // create backend
        $backendData = new Bank_Backend();
        $backendData->engine = 'paypall';
        $backendData->currency = 'USD';
        $backendData->setMeta('ClientId', 'yyy');
        $backendData->setMeta('ClientSecret', 'xxxx');
        $backendData->deleted = false;
        $backendData->create();

        $rec = Bank_Service::create(array(
            'title' => 'title test',
            'description' => 'description',
            'email' => 'maso@dpq.co.ir',
            'phone' => '+989177374087',
            'callbackURL' => 'http://localhost',
            'backend_id' => $backendData->id,
            'amount' => 100
        ));

        $backend = new Bank_Engine_PayPall();
        $backend->create($rec);
        $rec->update();

        $backend = new Bank_Engine_PayPall();
        $backend->create($rec);
        $rec->update();

        $rec->delete();
        $backendData->delete();
    }
}


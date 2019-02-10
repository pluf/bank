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
use PHPUnit\Framework\TestCase;

require_once 'Pluf.php';

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class Bank_ApiTest extends TestCase
{

    /**
     * @before
     */
    public function setUp ()
    {
        Pluf::start(__DIR__. '/../conf/config.php');
    }

    /**
     * @test
     */
    public function testClassInstance ()
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
}


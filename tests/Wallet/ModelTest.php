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
use PHPUnit\Framework\IncompleteTestError;

require_once 'Pluf.php';

/**
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class Wallet_ModelTest extends TestCase
{

    var $user = null;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->install();
        $m->init();

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
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     *
     * @before
     */
    public function init()
    {
        $this->user = User_Account::getUser('test');
    }

    /**
     *
     * @test
     */
    public function shouldPossibleCreateNew()
    {
        $wallet = new Bank_Wallet();
        $wallet->title = 'wallet-' . rand();
        $wallet->currency = 'IRR';
        $wallet->description = 'It is my wallet description';
        $wallet->owner_id = $this->user;
        Test_Assert::assertTrue($wallet->create(), 'Impossible to create wallet');
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToGetTransfers()
    {
        $wallet = new Bank_Wallet();
        $wallet->title = 'wallet-' . rand();
        $wallet->currency = 'IRR';
        $wallet->description = 'It is my wallet description';
        $wallet->owner_id = $this->user;
        Test_Assert::assertTrue($wallet->create(), 'Impossible to create wallet');

        $wallet = new Bank_Wallet($wallet->id);
        // The transfer has two foreign key to the wallet
        $transfers = $wallet->get_withdrawals_list();
        Test_Assert::assertEquals(0, $transfers->count());
        $transfers = $wallet->get_deposits_list();
        Test_Assert::assertEquals(0, $transfers->count());
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToGetOwner()
    {
        $wallet = new Bank_Wallet();
        $wallet->title = 'wallet-' . rand();
        $wallet->currency = 'IRR';
        $wallet->description = 'It is my wallet description';
        $wallet->owner_id = $this->user;
        Test_Assert::assertTrue($wallet->create(), 'Impossible to create wallet');

        $wallet = new Bank_Wallet($wallet->id);
        $owner = $wallet->get_owner();
        Test_Assert::assertNotEquals(null, $owner);
        Test_Assert::assertEquals($this->user->id, $owner->id);
    }
}


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
namespace Pluf\Test\Transfer;

use Pluf\Exception;
use Pluf\Test\TestCase;
use Bank_Backend;
use Bank_Service;
use Bank_Transfer;
use Bank_Wallet;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class Transfer_ModelTest extends TestCase
{

    var $user = null;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration();
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
        $m = new Pluf_Migration();
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
    public function createWalletToWalletTransfer()
    {
        $wallet1 = new Bank_Wallet();
        $wallet1->title = 'wallet-' . rand();
        $wallet1->currency = 'IRR';
        $wallet1->description = 'It is my wallet description';
        $wallet1->owner_id = $this->user;
        $this->assertTrue($wallet1->create(), 'Impossible to create wallet');

        $wallet2 = new Bank_Wallet();
        $wallet2->title = 'wallet-' . rand();
        $wallet2->currency = 'IRR';
        $wallet2->description = 'It is my wallet description';
        $wallet2->owner_id = $this->user;
        $this->assertTrue($wallet2->create(), 'Impossible to create wallet');

        $transfer = new Bank_Transfer();
        $transfer->amount = rand();
        $transfer->description = 'It is my transfer description';
        $transfer->acting_id = $this->user;
        $transfer->from_wallet_id = $wallet1;
        $transfer->to_wallet_id = $wallet2;
        $this->assertTrue($transfer->create(), 'Impossible to create wallet-to-wallet transfer');
        return $transfer;
    }

    /**
     *
     * @test
     */
    public function createReceiptToWalletTransfer()
    {
        $wallet1 = new Bank_Wallet();
        $wallet1->title = 'wallet-' . rand();
        $wallet1->currency = 'IRR';
        $wallet1->description = 'It is my wallet description';
        $wallet1->owner_id = $this->user;
        $this->assertTrue($wallet1->create(), 'Impossible to create transfer');

        $backend = new Bank_Backend();
        $backend->title = 'test backend';
        $backend->home = 'test.pluf.ir';
        $backend->redirect = 'test.pluf.ir';
        $backend->engine = 'zarinpal';
        $backend->currency = 'IRR';
        $this->assertTrue($backend->create(), 'Impossible to create wallet');
        
        $param = array(
            'amount' => rand(),
            'title' => 'my receipt',
            'description' => 'my receipt description',
            'callbackURL' => 'test.pluf.ir',
            'backend_id' => $backend->id
        );
        $receipt = Bank_Service::create($param, 'user-account', $this->user->id);
        $this->assertTrue(!$receipt->isAnonymous(), 'Impossible to create receipt');

        $transfer = new Bank_Transfer();
        $transfer->amount = rand();
        $transfer->description = 'It is my transfer description';
        $transfer->acting_id = $this->user;
        $transfer->receipt_id = $receipt;
        $transfer->to_wallet_id = $wallet1;
        $this->assertTrue($transfer->create(), 'Impossible to create receipt-to-wallet transfer');
        return $transfer;
    }

    /**
     *
     * @test
     */
    public function getDetailsAboutTransfer()
    {
        $transfer = $this->createWalletToWalletTransfer();
        // The transfer has two foreign key to the wallet
        $actor = $transfer->get_acting();
        $this->assertEquals($this->user->id, $actor->id);
        $wallet = $transfer->get_from_wallet();
        $this->assertNotEquals(0, $wallet->id);
        $wallet = $transfer->get_to_wallet();
        $this->assertNotEquals(0, $wallet->id);
        
        $transfer = $this->createReceiptToWalletTransfer();
        // The transfer has foreign key to a wallet and a receipt
        $actor = $transfer->get_acting();
        $this->assertEquals($this->user->id, $actor->id);
        $wallet = $transfer->get_to_wallet();
        $this->assertNotEquals(0, $wallet->id);
        $receipt = $transfer->get_receipt();
        $this->assertNotEquals(0, $receipt->id);
    }

}



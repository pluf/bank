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
use Pluf\Test\Client;
use Pluf\Test\TestCase;
use Bank_Backend;
use Bank_Service;
use Bank_Transfer;
use Bank_Wallet;
use Pluf;
use Pluf_Exception_Unauthorized;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class AnonymousRestTest extends TestCase
{

    var $client;

    var $user;
    var $wallet1;
    var $wallet2;
    var $backend;
    var $receipt;

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
        $this->client = new Client();
        // User
        $this->user = User_Account::getUser('test');
        
        // Wallets
        $this->wallet1 = new Bank_Wallet();
        $this->wallet1->title = 'wallet-' . rand();
        $this->wallet1->currency = 'IRR';
        $this->wallet1->description = 'It is my wallet description';
        $this->wallet1->owner_id = $this->user;
        $this->assertTrue($this->wallet1->create(), 'Impossible to create transfer');
        
        $this->wallet2 = new Bank_Wallet();
        $this->wallet2->title = 'wallet-' . rand();
        $this->wallet2->currency = 'IRR';
        $this->wallet2->description = 'It is my wallet description';
        $this->wallet2->owner_id = $this->user;
        $this->assertTrue($this->wallet2->create(), 'Impossible to create transfer');
        
        // Bank Backend
        $this->backend = new Bank_Backend();
        $this->backend->title = 'test backend';
        $this->backend->home = 'test.pluf.ir';
        $this->backend->redirect = 'test.pluf.ir';
        $this->backend->engine = 'zarinpal';
        $this->backend->currency = 'IRR';
        $this->assertTrue($this->backend->create(), 'Impossible to create wallet');
        
        // Receipt
        $param = array(
            'amount' => rand(),
            'title' => 'my receipt',
            'description' => 'my receipt description',
            'callbackURL' => 'test.pluf.ir',
            'backend_id' => $this->backend->id
        );
        $this->receipt = Bank_Service::create($param, 'user-account', $this->user->id);
        $this->assertTrue(!$this->receipt->isAnonymous(), 'Impossible to create receipt');
    }

    /**
     *
     * @test
     */
    public function anonymousShouldNotCreateTransfer()
    {
        $this->expectException(Pluf_Exception_Unauthorized::class);
        // Create transfer
        $form = array(
            'to_wallet_id' => $this->wallet2->id,
            'amount' => rand(),
            'description' => 'Move all balance of wallet-1 to wallet-2'
        );
        $response = $this->client->post('/bank/wallets/' . $this->wallet1->id . '/transfers', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }

    /**
     *
     * @test
     */
    public function anonymousShouldNotGetTransfer()
    {
        $transfer = new Bank_Transfer();
        $transfer->amount = rand();
        $transfer->description = 'It is my transfer description';
        $transfer->acting_id = $this->user;
        $transfer->from_wallet_id = $this->wallet1;
        $transfer->to_wallet_id = $this->wallet2;
        $this->assertTrue($transfer->create(), 'Impossible to create wallet-to-wallet transfer');
        $this->expectException(Pluf_Exception_Unauthorized::class);
        $response = $this->client->get('/bank/wallets/' . $this->wallet1->id . '/transfers/' . $transfer->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }

    /**
     *
     * @test
     */
    public function anonymousShouldNotGetListOfTransfers()
    {
        $this->expectException(Pluf_Exception_Unauthorized::class);
        $response = $this->client->get('/bank/wallets/' . $this->wallet1->id . '/transfers');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }
}




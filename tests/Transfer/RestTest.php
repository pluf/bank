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
class Transfer_RestTest extends TestCase
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
        $this->client = new Test_Client(array(
            array(
                'app' => 'Bank',
                'regex' => '#^/bank#',
                'base' => '',
                'sub' => include 'Bank/urls.php'
            ),
            array(
                'app' => 'User',
                'regex' => '#^/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        // login
        $this->client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        // User
        $this->user = User_Account::getUser('test');

        // Wallets
        $this->wallet1 = new Bank_Wallet();
        $this->wallet1->title = 'wallet-' . rand();
        $this->wallet1->currency = 'IRR';
        $this->wallet1->description = 'It is my wallet description';
        $this->wallet1->owner_id = $this->user;
        Test_Assert::assertTrue($this->wallet1->create(), 'Impossible to create transfer');

        $this->wallet2 = new Bank_Wallet();
        $this->wallet2->title = 'wallet-' . rand();
        $this->wallet2->currency = 'IRR';
        $this->wallet2->description = 'It is my wallet description';
        $this->wallet2->owner_id = $this->user;
        Test_Assert::assertTrue($this->wallet2->create(), 'Impossible to create transfer');

        // Bank Backend
        $this->backend = new Bank_Backend();
        $this->backend->title = 'test backend';
        $this->backend->home = 'test.pluf.ir';
        $this->backend->redirect = 'test.pluf.ir';
        $this->backend->engine = 'zarinpal';
        $this->backend->currency = 'IRR';
        Test_Assert::assertTrue($this->backend->create(), 'Impossible to create bank backend');

        // Receipt
        $param = array(
            'amount' => rand(),
            'title' => 'my receipt',
            'description' => 'my receipt description',
            'callbackURL' => 'test.pluf.ir',
            'backend_id' => $this->backend->id
        );
        $this->receipt = Bank_Service::create($param, 'user-account', $this->user->id);
        Test_Assert::assertTrue(! $this->receipt->isAnonymous(), 'Impossible to create receipt');
    }

    /**
     *
     * @test
     */
    public function createRestTest()
    {
        $amount = rand();
        // Reset wallets balances
        $this->wallet1->total_deposit = $amount;
        $this->wallet1->total_withdraw = 0.0;
        $this->wallet1->update();

        $this->wallet2->total_deposit = 0.0;
        $this->wallet2->total_withdraw = 0.0;
        $this->wallet2->update();

        // Create transfer
        $form = array(
            'to_wallet_id' => $this->wallet2->id,
            'amount' => $amount,
            'description' => 'Move all balance of wallet-1 to wallet-2'
        );
        $response = $this->client->post('/bank/wallets/' . $this->wallet1->id . '/transfers', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        // Check balances
        $wallet1 = new Bank_Wallet($this->wallet1->id);
        $this->assertEquals(0.0, $wallet1->total_deposit - $wallet1->total_withdraw);
        $wallet2 = new Bank_Wallet($this->wallet2->id);
        $this->assertEquals($amount, $wallet2->total_deposit - $wallet2->total_withdraw);
    }

    /**
     *
     * @test
     */
    public function getRestTest()
    {
        $transfer = new Bank_Transfer();
        $transfer->amount = rand();
        $transfer->description = 'It is my transfer description';
        $transfer->acting_id = $this->user;
        $transfer->from_wallet_id = $this->wallet1;
        $transfer->to_wallet_id = $this->wallet2;
        Test_Assert::assertTrue($transfer->create(), 'Impossible to create wallet-to-wallet transfer');
        // Get Transfer from wallet 1
        $response = $this->client->get('/bank/wallets/' . $this->wallet1->id . '/transfers/' . $transfer->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        // Get transfer from wallet 2
        $response = $this->client->get('/bank/wallets/' . $this->wallet2->id . '/transfers/' . $transfer->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function updateRestTest()
    {
        $transfer = new Bank_Transfer();
        $transfer->amount = rand();
        $transfer->description = 'It is my transfer description';
        $transfer->acting_id = $this->user;
        $transfer->from_wallet_id = $this->wallet1;
        $transfer->to_wallet_id = $this->wallet2;
        Test_Assert::assertTrue($transfer->create(), 'Impossible to create wallet-to-wallet transfer');
        // Should not update a transfer
        $this->expectException(Pluf_HTTP_Error404::class);
        $form = array(
            'description' => 'updated description'
        );
        $response = $this->client->post('/bank/wallets/' . $this->wallet1->id . '/transfers/' . $transfer->id, $form);
        $this->assertEquals($response->status_code, 404);
    }

    /**
     *
     * @test
     */
    public function deleteRestTest()
    {
        $transfer = new Bank_Transfer();
        $transfer->amount = rand();
        $transfer->description = 'It is my transfer description';
        $transfer->acting_id = $this->user;
        $transfer->from_wallet_id = $this->wallet1;
        $transfer->to_wallet_id = $this->wallet2;
        Test_Assert::assertTrue($transfer->create(), 'Impossible to create wallet-to-wallet transfer');
        // Should not update a transfer
        $this->expectException(Pluf_HTTP_Error404::class);
        $response = $this->client->delete('/bank/wallets/' . $this->wallet1->id . '/transfers/' . $transfer->id);
        $this->assertEquals($response->status_code, 404);
    }

    /**
     *
     * @test
     */
    public function findRestTest()
    {
        $response = $this->client->get('/bank/wallets/' . $this->wallet1->id . '/transfers');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     *
     * @test
     */
    public function chargeWalletTest()
    {
        $amount = rand();
        // Reset wallets balances
        $this->wallet1->total_deposit = 0.0;
        $this->wallet1->total_withdraw = 0.0;
        $this->wallet1->update();

        // Create transfer
        $form = array(
            'amount' => $amount,
            'backend' => $this->backend->id,
            'callback' => 'test.pluf.ir',
            'description' => 'Charge wallet-1'
        );
        $response = $this->client->post('/bank/wallets/' . $this->wallet1->id . '/payments', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $receipt = json_decode($response->content, true);
        Test_Assert::assertNotNull($receipt['id']);

        // Get the payment (it updates balance of the wallet)
        $response = $this->client->get('/bank/wallets/' . $this->wallet1->id . '/payments/' . $receipt['id']);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // Check balances
        $wallet1 = new Bank_Wallet($this->wallet1->id);
        $this->assertEquals($amount, $wallet1->total_deposit - $wallet1->total_withdraw);
    }
    
    /**
     *
     * @test
     */
    public function findPaymentsOfWalletTest()
    {
        $response = $this->client->get('/bank/wallets/' . $this->wallet1->id . '/payments');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        Test_Assert::assertResponsePaginateList($response, 'Payments list of wallet is not JSON paginated list');
    }
}




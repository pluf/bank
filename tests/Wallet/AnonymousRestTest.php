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
class Wallet_AnonymousRestTest extends TestCase
{

    var $client;
    var $user;

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
        $this->user = User_Account::getUser('test');
    }

    /**
     *
     * @test
     */
    public function anonymousShouldNotCreateWallet()
    {
        $this->expectException(Pluf_Exception_Unauthorized::class);
        $form = array(
            'title' => 'wallet-' . rand(),
            'currency' => 'IRR',
            'description' => 'It is my wallet description',
        );
        $response = $this->client->post('/bank/wallets', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }
    
    /**
     *
     * @test
     */
    public function anonymousShouldNotGetWallet()
    {
        $this->expectException(Pluf_Exception_Unauthorized::class);
        $item = new Bank_Wallet();
        $item->title = 'wallet-' . rand();
        $item->currency = 'IRR';
        $item->description = 'It is my wallet description';
        $item->owner_id = $this->user;
        $item->create();
        Test_Assert::assertFalse($item->isAnonymous(), 'Could not create Bank_Wallet');
        // Get item
        $response = $this->client->get('/bank/wallets/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }

    /**
     *
     * @test
     */
    public function anonymousShouldNotUpdateWallet()
    {
        $this->expectException(Pluf_Exception_Unauthorized::class);
        $item = new Bank_Wallet();
        $item->title = 'wallet-' . rand();
        $item->currency = 'IRR';
        $item->description = 'It is my wallet description';
        $item->owner_id = $this->user;
        $item->create();
        Test_Assert::assertFalse($item->isAnonymous(), 'Could not create Bank_Wallet');
        // Update item
        $form = array(
            'title' => 'new title' . rand(),
            'description' => 'updated description'
        );
        $response = $this->client->post('/bank/wallets/' . $item->id, $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }

    /**
     *
     * @test
     */
    public function anonymousShouldNotDeleteWallet()
    {
        $this->expectException(Pluf_Exception_Unauthorized::class);
        $item = new Bank_Wallet();
        $item->title = 'wallet-' . rand();
        $item->currency = 'IRR';
        $item->description = 'It is my wallet description';
        $item->owner_id = $this->user;
        $item->create();
        Test_Assert::assertFalse($item->isAnonymous(), 'Could not create Bank_Wallet');

        // delete
        $response = $this->client->delete('/bank/wallets/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }

    /**
     *
     * @test
     */
    public function anonymousShouldNotGetListOfWallets()
    {
        $this->expectException(Pluf_Exception_Unauthorized::class);
        $response = $this->client->get('/bank/wallets');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }

}




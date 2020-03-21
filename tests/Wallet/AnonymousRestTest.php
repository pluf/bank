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
namespace Pluf\Test\Wallet;

use Pluf\Exception;
use Pluf\Test\Client;
use Pluf\Test\TestCase;
use Bank_Wallet;
use Pluf;
use \Pluf\Exception_Unauthorized;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class AnonymousRestTest extends TestCase
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
        $m->uninstall();
    }

    /**
     *
     * @before
     */
    public function init()
    {
        $this->client = new Client();
        $this->user = User_Account::getUser('test');
    }

    /**
     *
     * @test
     */
    public function anonymousShouldNotCreateWallet()
    {
        $this->expectException(\Pluf\Exception_Unauthorized::class);
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
        $this->expectException(\Pluf\Exception_Unauthorized::class);
        $item = new Bank_Wallet();
        $item->title = 'wallet-' . rand();
        $item->currency = 'IRR';
        $item->description = 'It is my wallet description';
        $item->owner_id = $this->user;
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create Bank_Wallet');
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
        $this->expectException(\Pluf\Exception_Unauthorized::class);
        $item = new Bank_Wallet();
        $item->title = 'wallet-' . rand();
        $item->currency = 'IRR';
        $item->description = 'It is my wallet description';
        $item->owner_id = $this->user;
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create Bank_Wallet');
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
        $this->expectException(\Pluf\Exception_Unauthorized::class);
        $item = new Bank_Wallet();
        $item->title = 'wallet-' . rand();
        $item->currency = 'IRR';
        $item->description = 'It is my wallet description';
        $item->owner_id = $this->user;
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create Bank_Wallet');

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
        $this->expectException(\Pluf\Exception_Unauthorized::class);
        $response = $this->client->get('/bank/wallets');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 401);
    }

}




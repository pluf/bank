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
namespace Pluf\Test\Backend;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Pluf\Exception;
use Bank_Service;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class BasicTest extends TestCase
{

    private static $client = null;

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

        self::$client = new Client();
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
     * Geting list of engines
     *
     * @test
     */
    public function shouldAnonymousGetListOfEngines()
    {
        $response = self::$client->get('/bank/engines');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     * Geting an engine info
     *
     * @test
     */
    public function shouldAnonymousGetAnEngine()
    {
        $engs = Bank_Service::engines();
        foreach ($engs as $eng) {
            $response = self::$client->get('/bank/engines/' . $eng->getType());
            $this->assertResponseNotNull($response, 'Find result is empty');
            $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        }
    }

    /**
     * Geting list of engines
     *
     * @test
     */
    public function shouldAnonymousGetListOfBackend()
    {
        $response = self::$client->get('/bank/backends');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }
}
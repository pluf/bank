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
class Bank_REST_BasicTest extends TestCase
{

    private static $client = null;

    private static $user = null;

    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/mysql.conf.php');
        $m = new Pluf_Migration(array(
            'Pluf',
            'User',
            'Role',
            'Group',
            'Bank'
        ));
        $m->install();
        // Test user
        self::$user = new User();
        self::$user->login = 'test';
        self::$user->first_name = 'test';
        self::$user->last_name = 'test';
        self::$user->email = 'toto@example.com';
        self::$user->setPassword('test');
        self::$user->active = true;
        self::$user->administrator = true;
        if (true !== self::$user->create()) {
            throw new Pluf_Exception();
        }
        
        $role = Role::getFromString('Pluf.owner');
        self::$user->setAssoc($role);
        
        self::$client = new Test_Client(array(
            array(
                'app' => 'Bank',
                'regex' => '#^/api/bank#',
                'base' => '',
                'sub' => include 'Bank/urls.php'
            ),
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            ),
            array(
                'app' => 'Role',
                'regex' => '#^/api/role#',
                'base' => '',
                'sub' => include 'Role/urls.php'
            ),
            array(
                'app' => 'Group',
                'regex' => '#^/api/group#',
                'base' => '',
                'sub' => include 'Group/urls.php'
            )
        ));
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(array(
            'Pluf',
            'User',
            'Role',
            'Group',
            'Bank'
        ));
        $m->unInstall();
    }

    /**
     * Geting list of engines
     *
     * @test
     */
    public function shouldAnonymousGetListOfEngines()
    {
        $response = self::$client->get('/api/bank/engine/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
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
            $response = self::$client->get('/api/bank/engine/' . $eng->getType());
            Test_Assert::assertResponseNotNull($response, 'Find result is empty');
            Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        }
    }
    
    /**
     * Geting list of engines
     *
     * @test
     */
    public function shouldAnonymousGetListOfBackend()
    {
        $response = self::$client->get('/api/bank/backend/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }
}
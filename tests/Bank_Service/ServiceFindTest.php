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
class ServiceFindTest extends TestCase
{

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps', array()));
        $m->install();
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
     * @test
     */
    public function testCrateForModel()
    {
        $backend = new Bank_Backend();
        $backend->title = 'title';
        $backend->description = 'des';
        $backend->symbol = 'symbo.';
        $backend->home = '';
        $backend->redirect = '';
        $backend->engine = 'zarinpal';
        $backend->create();

        $owner = $backend;

        $res = Bank_Service::create(array(
            'amount' => 1000, // مقدار پرداخت به ریال
            'title' => 'payment title',
            'description' => 'description',
            'email' => 'user@email.address',
            'phone' => '0917222222',
            'callbackURL' => 'http://.....',
            'backend_id' => $backend->id
        ), $owner);
        $this->assertNotNull($res);
        $this->assertEquals($owner->getClass(), $res->owner_class);
        $this->assertEquals($owner->id, $res->owner_id);

        $list = Bank_Service::find($owner);
        $this->assertNotNull($list);
        $this->assertEquals(1, $list->count());
    }

    /**
     *
     * @test
     */
    public function testCrateForCustomModel()
    {
        $backend = new Bank_Backend();
        $backend->title = 'title';
        $backend->description = 'des';
        $backend->symbol = 'symbo.';
        $backend->home = '';
        $backend->redirect = '';
        $backend->engine = 'zarinpal';
        $backend->create();

        $ownerClass = 'owner';
        $ownerId = rand();

        $res = Bank_Service::create(array(
            'amount' => 1000, // مقدار پرداخت به ریال
            'title' => 'payment title',
            'description' => 'description',
            'email' => 'user@email.address',
            'phone' => '0917222222',
            'callbackURL' => 'http://.....',
            'backend_id' => $backend->id
        ), $ownerClass, $ownerId);
        $this->assertNotNull($res);
        $this->assertEquals($ownerClass, $res->owner_class);
        $this->assertEquals($ownerId, $res->owner_id);

        $list = Bank_Service::find($ownerClass, $ownerId);
        $this->assertNotNull($list);
        $this->assertEquals(1, $list->count());
    }
}


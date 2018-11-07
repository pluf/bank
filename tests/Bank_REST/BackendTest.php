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
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class Bank_REST_BackendTest extends TestCase {
	private static $client = null;

	/**
	 *
	 * @beforeClass
	 */
	public static function createDataBase() {
		Pluf::start ( __DIR__ . '/../conf/config.php' );
		$m = new Pluf_Migration ( Pluf::f ( 'installed_apps', array () ) );
		$m->install ();

		// Test user
		$user = new User_Account ();
		$user->login = 'test';
		$user->is_active = true;
		if (true !== $user->create ()) {
			throw new Exception ();
		}
		// Credential of user
		$credit = new User_Credential ();
		$credit->setFromFormData ( array (
				'account_id' => $user->id
		) );
		$credit->setPassword ( 'test' );
		if (true !== $credit->create ()) {
			throw new Exception ();
		}

		$per = User_Role::getFromString ( 'tenant.owner' );
		$user->setAssoc ( $per );

		self::$client = new Test_Client ( array (
				array (
						'app' => 'Bank',
						'regex' => '#^/bank#',
						'base' => '',
						'sub' => include 'Bank/urls.php'
				),
				array (
						'app' => 'User',
						'regex' => '#^/user#',
						'base' => '',
						'sub' => include 'User/urls.php'
				)
		) );
	}

	/**
	 *
	 * @afterClass
	 */
	public static function removeDatabses() {
		$m = new Pluf_Migration ( Pluf::f ( 'installed_apps' ) );
		$m->unInstall ();
	}

	/**
	 * Geting list of engines
	 *
	 * @test
	 */
	public function shouldOwnerCanCreateBackend() {
		// Login
		$response = self::$client->post ( '/user/login', array (
				'login' => 'test',
				'password' => 'test'
		) );
		Test_Assert::assertResponseStatusCode ( $response, 200, 'Fail to login' );

		// Create a backend
		$response = self::$client->post ( '/bank/backends', array (
				'type' => 'zarinpal',
				'MerchantID' => 'xxx',
				'title' => 'title',
				'description' => 'Description',
				'symbol' => 'Symbole'
		) );
		Test_Assert::assertResponseNotNull ( $response, 'Find result is empty' );
		Test_Assert::assertResponseStatusCode ( $response, 200, 'Find status code is not 200' );

		$backend = new Bank_Backend ();
		$list = $backend->getList ();
		Test_Assert::assertTrue ( sizeof ( $list ) >= 1, 'No backend is created' );
		foreach ( $list as $b ) {
			$b->delete ();
		}
	}
}
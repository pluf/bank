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
return array (
	/*
	 * ********************************************
	 * Engines
	 * ********************************************
	 */
	array(
        'regex' => '#^/engines$#',
        'model' => 'Bank_Views_Engine',
        'method' => 'find',
        'http-method' => array(
            'GET'
        )
    ),
    array(
        'regex' => '#^/engines/(?P<type>.+)$#',
        'model' => 'Bank_Views_Engine',
        'method' => 'get',
        'http-method' => array(
            'GET'
        )
    ),
	/*
	 * ********************************************
	 * Backends
	 * ********************************************
	 */
	array(
        'regex' => '#^/backends$#',
        'model' => 'Bank_Views_Backend',
        'method' => 'find',
        'http-method' => array(
            'GET'
        )
    ),
    // array(
    // 'regex' => '#^/backends/parameters$#',
    // 'model' => 'Bank_Views_Backend',
    // 'method' => 'createParameter',
    // 'http-method' => array(
    // 'GET'
    // )
    // ),
    array(
        'regex' => '#^/backends$#',
        'model' => 'Bank_Views_Backend',
        'method' => 'create',
        'http-method' => array(
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/backends/(?P<id>\d+)$#',
        'model' => 'Bank_Views_Backend',
        'method' => 'get',
        'http-method' => array(
            'GET'
        )
    ),
    array(
        'regex' => '#^/backends/(?P<id>\d+)$#',
        'model' => 'Bank_Views_Backend',
        'method' => 'update',
        'http-method' => array(
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/backends/(?P<id>\d+)$#',
        'model' => 'Bank_Views_Backend',
        'method' => 'delete',
        'http-method' => array(
            'DELETE'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
	/*
	 * ********************************************
	 * receipt
	 * ********************************************
	 */
    array(
        'regex' => '#^/receipts$#',
        'model' => 'Bank_Views_Receipt',
        'method' => 'find',
        'http-method' => array(
            'GET'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/receipts$#',
        'model' => 'Bank_Views_Receipt',
        'method' => 'create',
        'http-method' => array(
            'POST'
        )
    ),
    array(
        'regex' => '#^/receipts/(?P<id>\d+)$#',
        'model' => 'Bank_Views_Receipt',
        'method' => 'get',
        'http-method' => array(
            'GET'
        ),
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array(
        'regex' => '#^/receipts/(?P<secure_id>.+)$#',
        'model' => 'Bank_Views_Receipt',
        'method' => 'getBySecureId',
        'http-method' => array(
            'GET'
        )
    ),
    array(
        'regex' => '#^/receipts/(?P<id>\d+)$#',
        'model' => 'Bank_Views_Receipt',
        'method' => 'update',
        'http-method' => array(
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/receipts/(?P<secure_id>.+)$#',
        'model' => 'Bank_Views_Receipt',
        'method' => 'updateBySecureId',
        'http-method' => array(
            'POST'
        )
    ),
    array(
        'regex' => '#^/receipts/(?P<id>.+)$#',
        'model' => 'Bank_Views_Receipt',
        'method' => 'delete',
        'http-method' => array(
            'DELETE'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),

    // /wallets/{walletId}/transfers:
    // /wallets/{walletId}/transfers/{transferId}:
    /*
     * ********************************************
     * wallet
     * ********************************************
     */
    array( // Create
        'regex' => '#^/wallets$#',
        'model' => 'Bank_Views_Wallet',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/wallets$#',
        'model' => 'Bank_Views_Wallet',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read
        'regex' => '#^/wallets/(?P<modelId>\d+)$#',
        'model' => 'Bank_Views_Wallet',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Update
        'regex' => '#^/wallets/(?P<modelId>\d+)$#',
        'model' => 'Bank_Views_Wallet',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/wallets/(?P<modelId>\d+)$#',
        'model' => 'Bank_Views_Wallet',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    /*
     * ********************************************
     * transfers of wallet
     * ********************************************
     */
    array( // Create
        'regex' => '#^/wallets/(?P<parentId>\d+)/transfers$#',
        'model' => 'Bank_Views_Transfer',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/wallets/(?P<parentId>\d+)/transfers$#',
        'model' => 'Bank_Views_Transfer',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read
        'regex' => '#^/wallets/(?P<parentId>\d+)/transfers/(?P<modelId>\d+)$#',
        'model' => 'Bank_Views_Transfer',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    /*
     * ********************************************
     * payments of wallet
     * ********************************************
     */
    array( // Create
        'regex' => '#^/wallets/(?P<parentId>\d+)/payments$#',
        'model' => 'Bank_Views_Transfer',
        'method' => 'createPayment',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/wallets/(?P<parentId>\d+)/payments$#',
        'model' => 'Bank_Views_Transfer',
        'method' => 'findPayments',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read
        'regex' => '#^/wallets/(?P<parentId>\d+)/payments/(?P<modelId>\d+)$#',
        'model' => 'Bank_Views_Transfer',
        'method' => 'getPayment',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
);



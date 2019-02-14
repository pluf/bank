<?php
return array(
    // ************************************************************* Schema
    array(
        'regex' => '#^/wallets/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'Bank_Wallet'
        )
    ),
    array(
        'regex' => '#^/wallets/(?P<parentId>\d+)/transfers/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'Bank_Transfer'
        )
    ),
    array(
        'regex' => '#^/wallets/(?P<parentId>\d+)/payments/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'Bank_Receipt'
        )
    ),
    // ************************************************************* Wallet
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
    // ************************************************************* Transfers of Wallet
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
    // ************************************************************* Payments of Wallet
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
    )
);



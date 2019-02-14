<?php
return array(
    // ************************************************************* Schema
    array(
        'regex' => '#^/receipts/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'Bank_Receipt'
        )
    ),
    // ************************************************************* Receipt
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
    )
);
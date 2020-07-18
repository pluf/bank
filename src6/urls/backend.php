<?php
return array(
    // ************************************************************* Schema
    array(
        'regex' => '#^/backends/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'Bank_Backend'
        )
    ),
    // ************************************************************* Backends
    array(
        'regex' => '#^/backends$#',
        'model' => 'Pluf_Views',
        'method' => 'findObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'Bank_Backend',
            'sql' => new Pluf_SQL('deleted=false')
        )
    ),
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
        'http-method' => 'GET'
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
        'regex' => '#^/backends/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'deleteObject',
        'http-method' => 'DELETE',
        'params' => array(
            'model' => 'Bank_Backend',
            'permanently' => false
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
);
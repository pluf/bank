<?php
return array(
    /*
     * ********************************************
     * Engines
     * ********************************************
     */
    array(
        'regex' => '#^/engines$#',
        'model' => 'Bank_Views_Engine',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/engines/(?P<type>.+)$#',
        'model' => 'Bank_Views_Engine',
        'method' => 'get',
        'http-method' => 'GET'
    )
);
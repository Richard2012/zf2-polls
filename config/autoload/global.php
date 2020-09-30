<?php
/**
 * This file is located in config/autoload/ directory
 *
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
/*
 The commented code can be used if you work with more then one database

    'db' => array(
        'adapters'=>array(
            'adapterPoll' => array(
                'driver'         => 'Pdo',
                'dsn'            => 'mysql:dbname=polls;host=localhost',
                'driver_options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ),
            ),
        )
    ),
*/
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=polls;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' =>
            'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);

/* 
   I hope this might helpful for you

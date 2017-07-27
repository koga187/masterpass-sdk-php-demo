<?php

return array(
    'rootLogger' => array(
        'appenders' => array('default'),
    ),
    'appenders' => array(
        'default' => array(
            'class' => 'LoggerAppenderFile',
            'layout' => array(
                'class' => 'LoggerLayoutSimple'
            ),
            'params' => array(
                //'file' => '/var/www/html/masterpass-sdk-php-demo/var/log/my.log',
                'file' => 'D:\\public_html\masterpass-sdk-php-demo\var\log\my.log',
                'append' => true
            )
        )
    )
);
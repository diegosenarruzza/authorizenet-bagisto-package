<?php

return [
    'name'         => 'Custom Authorize.Net Integration for Bagisto',
    'version'      => '1.0.0',
    'author'       => 'Diego Senarruzza',
    'description'  => 'This package provides a custom Authorize.Net payment integration for Bagisto 2.x.',
    'license'      => 'MIT',
    'dependencies' => [
        'webkul/core',
        'webkul/payment',
        'authorizenet/authorizenet',
    ],
    'support'     => [
        'source'   => 'https://github.com/diegosenarruzza/authorizenet-bagisto-package',
        'docs'     => 'https://github.com/diegosenarruzza/authorizenet-bagisto-package/blob/main/Readme.md',
    ],
];

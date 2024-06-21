<?php

return [
    [
        'key'    => 'sales.payment_methods.authorizenet',
        'name'   => 'authorizenet::app.admin.system.authorize',
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.title',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'description',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.description',
                'type'          => 'textarea',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'image',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.logo',
                'type'          => 'image',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'channel_based' => false,
                'locale_based'  => false,
                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp',
            ],
            [
                'name'          => 'client_key',
                'title'         => 'authorizenet::app.admin.system.client_key',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,true',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'api_login_id',
                'title'         => 'authorizenet::app.admin.system.api_login_ID',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,true',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'transaction_key',
                'title'         => 'authorizenet::app.admin.system.transaction_key',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,true',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'active',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.status',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'sandbox',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.sandbox',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'    => 'sort',
                'title'   => 'authorizenet::app.admin.system.sort_order',
                'type'    => 'select',
                'options' => [
                    [
                        'title' => '1',
                        'value' => 1,
                    ],
                    [
                        'title' => '2',
                        'value' => 2,
                    ],
                    [
                        'title' => '3',
                        'value' => 3,
                    ],
                    [
                        'title' => '4',
                        'value' => 4,
                    ],
                ],
            ],
        ],
    ],
];

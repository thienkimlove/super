<?php

return [
    1 => [
        'label' => 'Admin',
        'permission' => [
            'HomeController@index',
            'HomeController@control',
            'HomeController@clearlead',
            'HomeController@statistic',

            //only admin can access to edit users.

            'UsersController@index',
            'UsersController@edit',
            'UsersController@store',
            'UsersController@update',
            'UsersController@create',
            'UsersController@destroy',

            'OffersController@index',
            'OffersController@edit',
            'OffersController@store',
            'OffersController@update',
            'OffersController@create',
            'OffersController@destroy',

            'GroupsController@index',
            'GroupsController@edit',
            'GroupsController@store',
            'GroupsController@update',
            'GroupsController@create',
            'GroupsController@destroy',
        ]
    ],

    2 => [
        'label' => 'Editor',
        'permission' => [
            'HomeController@index',
        ]
    ],

    3 => [
        'label' => 'User',
        'permission' => [
            'HomeController@index',

            'OffersController@index',
        ]
    ]
];
<?php
return [
    'settings' => [
        'displayErrorDetails' => true,

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        // API
        'api' => [
            'version' => 'v1',
            'base_url' => 'http://localhost',
        ],

        // Database
        'db' => [
            'dsn' => 'sqlite',
            'database' => __DIR__ . '/todo.db',
        ],
    ],
];

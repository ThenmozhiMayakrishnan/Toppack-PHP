<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'toppack',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/dev.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'pdo' => [
          'engine' => 'mysql',
          'host' => 'localhost',
          'username' => 'root',
          'password' => 'Thenmozhi@1',
          'database' => 'toppack',
          'options' => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true
          ]
        ]
    ],
];

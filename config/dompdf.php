<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false,   // Throw an Exception on warnings from dompdf

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | Array of options to pass to dompdf. For example:
    |
    | 'options' => [
    |     'enable_php' => true,  // Enable PHP embedding
    |     'isHtml5ParserEnabled' => true,  // Enable HTML5 parsing
    |     'isRemoteEnabled' => true,  // Allow remote images
    | ],
    |
    */
    'options' => [
        'enable_php' => true,
        'enable_javascript' => true,
        'enable_remote' => true,
        'temp_dir' => storage_path('app/dompdf'),
        'font_dir' => storage_path('app/dompdf/fonts/'),
        'font_cache' => storage_path('app/dompdf/fonts/'),
        'chroot' => storage_path('app/dompdf'),
        'allowed_protocols' => [
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        'log_output_file' => storage_path('logs/dompdf.log'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Canvas
    |--------------------------------------------------------------------------
    |
    | Set the dompdf canvas class
    |
    */
    'canvas_class' => 'Dompdf\\Canvas',

    /*
    |--------------------------------------------------------------------------
    | Font Metrics
    |--------------------------------------------------------------------------
    |
    | Set the dompdf font metrics class
    |
    */
    'font_metrics_class' => 'Dompdf\\FontMetrics',

    /*
    |--------------------------------------------------------------------------
    | HTML Parser
    |--------------------------------------------------------------------------
    |
    | Set the dompdf HTML parser class
    |
    */
    'html_parser_class' => 'Dompdf\\Html',

    /*
    |--------------------------------------------------------------------------
    | Chroot
    |--------------------------------------------------------------------------
    |
    | Prevent dompdf from accessing system files or other files on the web server.
    | A chroot directory must be absolute path that is also accessible by the
    | webserver process.
    |
    */
    'chroot' => storage_path('app/dompdf'),

    /*
    |--------------------------------------------------------------------------
    | Log Output File
    |--------------------------------------------------------------------------
    |
    | File for dompdf's internal logs
    |
    */
    'log_output_file' => storage_path('logs/dompdf.log'),

    /*
    |--------------------------------------------------------------------------
    | Temporary Directory
    |--------------------------------------------------------------------------
    |
    | Directory used for temporary files like images
    |
    */
    'temp_dir' => storage_path('app/dompdf'),
]; 
<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('../temp/'),

    'font_path' => public_path('fonts/'),
	'font_data' => [
    'hse' => [
        'R'  => 'IRANSans_Medium.ttf',    // regular font
        'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
        'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese

    ]
]
];

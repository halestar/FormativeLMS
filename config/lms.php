<?php

return [
	'internal_email_suffix' => env('INTERNAL_EMAIL_SUFFIX', '@kalinec.net'),
	'date_format' => env('DATE_FORMAT', 'm/d/Y'),
	'datetime_format' => env('DATETIME_FORMAT', 'm/d/Y h:i A'),
	'default_country' => env('DEFAULT_COUNTRY', 'US'),
	'image_max_height' => env('IMG_MAX_HEIGHT', 720),
	'thumb_max_height' => env('THUMB_MAX_HEIGHT', 150),
	'rubric_max_points' => env('RUBRIC_MAX_POINTS', 6),
	'school_id_length' => env('SCHOOL_ID_LENGTH', 10),
	'max_file_ul_size' => env('MAX_FILE_UPLOAD_SIZE', 1024 * 2), //in bytes
	'school_id_elements' =>
		[
			\App\Classes\IdCard\CustomText::class,
			\App\Classes\IdCard\ParentChildren::class,
			\App\Classes\IdCard\PersonName::class,
			\App\Classes\IdCard\PersonPicture::class,
			\App\Classes\IdCard\SchoolId::class,
			\App\Classes\IdCard\SchoolIdBarcode::class,
			\App\Classes\IdCard\SchoolYear::class,
			\App\Classes\IdCard\StudentGrade::class,
		],
    'prefs_default' =>
    [
        'communications' =>
        [
            'send_email' => true,
            'send_sms' => false,
            'send_push' => true,
            'sms_phone_id' => null,
            'services' => [],
        ],
    ],
	'fonts' =>
		[
			'Arial',
			'Brush Script MT',
			'Courier New',
			'Garamond',
			'Georgia',
			'monospace',
			'Tahoma',
			'Times New Roman',
			'Trebuchet MS',
			'Verdana',
		],
	'id_sizes' =>
		[
			'sm' => 400,
			'md' => 600,
			'lg' => 800,
		],
	'auth_code_length' => 6,
	'auth_code_timeout' => 10,
	'storage' =>
		[
			'documents' => 'local-document-storage',
			'work' => 'local-work-storage',
		],
	'vault' =>
		[
			'disk' => 'private',
			'path' => 'vault',
		],
	'temp-filer-expiration' => 10, //in minutes
];

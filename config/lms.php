<?php

return [
    'internal_email_suffix' => env('INTERNAL_EMAIL_SUFFIX', '@kalinec.net'),
    'superadmin_email' => env('ADMIN_USERNAME', 'admin@kalinec.net'),
    'superadmin_password' => env('ADMIN_PASSWORD', 'admin'),
	'superadmin_auth' => env('ADMIN_AUTH', 'local'),
    'google' =>
        [
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'storage_bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'google_cloud_storage_url' => "https://storage.googleapis.com/",
            'secrets_location' => env('GOOGLE_SECRETS_FILE', storage_path('app/google_cloud.json')),
            'cms_storage_path' => env('GOOGLE_CLOUD_CMS_STORAGE_PREFIX', 'cms/'),
        ],
    'viewable_classes' => [],
    'date_format' => env('DATE_FORMAT', 'm/d/Y'),
    'datetime_format' => env('DATETIME_FORMAT', 'm/d/Y h:i A'),
    'default_country' => env('DEFAULT_COUNTRY', 'US'),
    'profile_pics_disk' => env('PROFILE_PICS_DISK', 'idpics'),
    'thumb_max_height' => env('THUMB_MAX_SIZE', 64),
    'profile_thumbs_path' => env('PROFILE_THUMBS_PATH', 'thmb'),
    'campus_img_width' => env('CAMPUS_IMG_WIDTH', 400),
    'rubric_max_points' => env('RUBRIC_MAX_POINTS', 3),
    'school_id_length' => env('SCHOOL_ID_LENGTH', 10),
    'prefs' =>
        [
            'defaults' =>
                [

                ],
        ],
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
];

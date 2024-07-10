<?php

return [
    'internal_email_suffix' => env('INTERNAL_EMAIL_SUFFIX', '@kalinec.net'),
    'superadmin_email' => env('ADMIN_USERNAME', 'admin@kalinec.net'),
    'superadmin_password' => env('ADMIN_PASSWORD', 'admin'),
    'google' =>
        [
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'storage_bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'google_cloud_storage_url' => "https://storage.googleapis.com/",
            'secrets_location' => env('GOOGLE_SECRETS_FILE', storage_path('app/google_cloud.json')),
            'cms_storage_path' => env('GOOGLE_CLOUD_CMS_STORAGE_PREFIX', 'cms/'),
        ],
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | This file contains the credentials and configuration for Firebase services.
    | You will need to replace the placeholder values with your actual Firebase
    | project credentials.
    |
    */

    'credentials_path' => storage_path('app/firebase/firebase-credentials.json'),
    
    'database_url' => env('FIREBASE_DATABASE_URL', 'https://your-project-id.firebaseio.com'),
    
    'project_id' => env('FIREBASE_PROJECT_ID', 'your-project-id'),
    
    'api_key' => env('FIREBASE_API_KEY', 'your-api-key'),
    
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'your-project-id.firebaseapp.com'),
    
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'your-project-id.appspot.com'),
    
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', 'your-messaging-sender-id'),
    
    'app_id' => env('FIREBASE_APP_ID', 'your-app-id'),
    
    'measurement_id' => env('FIREBASE_MEASUREMENT_ID', 'your-measurement-id'),
];

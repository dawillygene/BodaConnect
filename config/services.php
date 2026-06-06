<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'monitoring' => [
        'elasticsearch_url' => env('ELASTICSEARCH_URL', 'http://elasticsearch:9200'),
        'kibana_url' => env('KIBANA_URL', 'http://kibana:5601'),
        'metricbeat_index' => env('METRICBEAT_INDEX', 'metricbeat-*'),
        'application_metrics_index' => env('APPLICATION_METRICS_INDEX', 'bodaconnect-admin-metrics'),
        'database_metric_module' => env('MONITORING_DATABASE_METRIC_MODULE', 'mysql'),
    ],

    'mqtt' => [
        'enabled' => env('MQTT_ENABLED', true),
        'host' => env('MQTT_HOST', '127.0.0.1'),
        'port' => env('MQTT_PORT', 1883),
        'client_id_prefix' => env('MQTT_CLIENT_ID_PREFIX', 'bodaconnect-backend'),
        'connect_timeout' => env('MQTT_CONNECT_TIMEOUT', 1),
        'socket_timeout' => env('MQTT_SOCKET_TIMEOUT', 1),
        'keep_alive_interval' => env('MQTT_KEEP_ALIVE_INTERVAL', 60),
        'topics' => [
            'ride_status' => env('MQTT_TOPIC_RIDE_STATUS', 'ride/status'),
            'admin_ride_status' => env('MQTT_TOPIC_ADMIN_RIDE_STATUS', 'ride/status/admin'),
            'rider_ride_status' => env('MQTT_TOPIC_RIDER_RIDE_STATUS', 'ride/status/rider'),
        ],
    ],

];

<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use Throwable;

class RideStatusPublisher
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function publish(string $topic, array $payload): void
    {
        if (! (bool) config('services.mqtt.enabled', true)) {
            return;
        }

        try {
            $client = new MqttClient(
                (string) config('services.mqtt.host', '127.0.0.1'),
                (int) config('services.mqtt.port', 1883),
                (string) Str::of((string) config('services.mqtt.client_id_prefix', 'bodaconnect-backend'))
                    ->append('-', Str::ulid()),
            );

            $connectionSettings = (new ConnectionSettings)
                ->setConnectTimeout((int) config('services.mqtt.connect_timeout', 1))
                ->setKeepAliveInterval((int) config('services.mqtt.keep_alive_interval', 60))
                ->setSocketTimeout((int) config('services.mqtt.socket_timeout', 1));

            $client->connect($connectionSettings, true);
            $client->publish($topic, json_encode($payload, JSON_THROW_ON_ERROR), MqttClient::QOS_AT_MOST_ONCE, false);
            $client->disconnect();
        } catch (Throwable $exception) {
            report($exception);

            Log::warning('Unable to publish MQTT message.', [
                'topic' => $topic,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}

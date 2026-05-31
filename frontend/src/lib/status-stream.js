import mqtt from 'mqtt';

const rideStatusPath = import.meta.env.VITE_MQTT_PATH ?? '/mqtt';

function buildWebSocketUrl(path) {
  const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
  const normalizedPath = path.startsWith('/') ? path : `/${path}`;

  return `${protocol}//${window.location.host}${normalizedPath}`;
}

export function rideStatusTopic(customerId) {
  return `ride/status/${customerId}`;
}

export function adminRideStatusTopic() {
  return 'ride/status/admin';
}

export function subscribeToRideStatusUpdates(customerId, onStatusUpdate) {
  if (!customerId) {
    return () => {};
  }

  return subscribeToTopic(rideStatusTopic(customerId), onStatusUpdate);
}

export function subscribeToAdminRideStatusUpdates(onStatusUpdate) {
  return subscribeToTopic(adminRideStatusTopic(), onStatusUpdate);
}

function subscribeToTopic(topic, onStatusUpdate) {
  const client = mqtt.connect(buildWebSocketUrl(rideStatusPath), {
    clean: true,
    connectTimeout: 1000,
    reconnectPeriod: 1000,
  });

  client.on('connect', () => {
    client.subscribe(topic);
  });

  client.on('message', (incomingTopic, payload) => {
    if (incomingTopic !== topic) {
      return;
    }

    try {
      onStatusUpdate(JSON.parse(payload.toString()));
    } catch (error) {
      console.error('Unable to parse MQTT ride status payload.', error);
    }
  });

  client.on('error', (error) => {
    console.error('MQTT connection error.', error);
  });

  return () => {
    client.end(true);
  };
}

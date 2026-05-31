const mqtt = require('mqtt');

const brokerUrl = process.env.MQTT_BROKER_URL ?? 'mqtt://127.0.0.1:1883';
const customerId = process.env.MQTT_CUSTOMER_ID ?? '1';
const topic = `ride/status/${customerId}`;

const client = mqtt.connect(brokerUrl);

client.on('connect', () => {
  client.subscribe(topic);
});

client.on('message', (incomingTopic, payload) => {
  if (incomingTopic !== topic) {
    return;
  }

  console.log(payload.toString());
});

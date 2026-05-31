const mqtt = require('mqtt');

const brokerUrl = process.env.MQTT_BROKER_URL ?? 'mqtt://127.0.0.1:1883';
const customerId = process.env.MQTT_CUSTOMER_ID ?? '1';
const topic = `ride/status/${customerId}`;

const updates = [
  { ride_id: 101, customer_id: Number(customerId), rider_id: 7, status: 'Accepted' },
  { ride_id: 101, customer_id: Number(customerId), rider_id: 7, status: 'In Progress' },
  { ride_id: 101, customer_id: Number(customerId), rider_id: 7, status: 'Completed' },
];

const client = mqtt.connect(brokerUrl);

client.on('connect', () => {
  let index = 0;

  const publishNext = () => {
    if (index >= updates.length) {
      client.end(true);
      return;
    }

    client.publish(topic, JSON.stringify(updates[index]), () => {
      index += 1;
      setTimeout(publishNext, 1000);
    });
  };

  publishNext();
});

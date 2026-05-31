# BodaConnect

BodaConnect is a role-based ride request and dispatch platform built with Laravel and React. It supports customer ride booking, admin dispatch operations, rider trip execution, monitoring with Elastic, and live ride status updates over MQTT.

## Stack

- Backend: Laravel 13, PHP 8.4, Sanctum, MySQL
- Frontend: React 19, Vite, React Router
- Realtime: Mosquitto MQTT over TCP and WebSockets
- Monitoring: Elasticsearch, Kibana, Metricbeat
- Containers: Docker Compose

## Main features

- Customers can create rides, cancel pending rides, and review ride history.
- Admins can monitor demand, view ride activity, manage riders, and assign pending rides.
- Riders can accept, start, and complete assigned trips.
- Customer and admin dashboards receive live ride updates through MQTT.
- Monitoring dashboards are available through Kibana.

## Roles

### Customer

- Create ride requests
- View recent rides and ride history
- Cancel pending rides
- Receive live status updates when rides are assigned or progress changes

### Admin

- View dashboard metrics and recent ride activity
- Review all ride requests
- Assign riders to pending rides
- Manage rider and customer accounts
- Receive live updates when new rides are created or existing rides change

### Rider

- View assigned and active trips
- Accept assigned rides
- Start rides
- Complete rides

## Local development

### Backend

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Laravel runs on:

```text
http://127.0.0.1:8000
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

Vite runs on:

```text
http://127.0.0.1:5173
```

The frontend proxies API traffic to Laravel on port `8000` and MQTT WebSocket traffic to `/mqtt`.

## Docker stack

Start the local container stack with:

```bash
docker compose up -d --build
```

Services:

- Backend API: `http://localhost:8080`
- Frontend: `http://localhost:3000`
- MySQL: `127.0.0.1:3307`
- Elasticsearch: `http://localhost:9200`
- Kibana: `http://localhost:5601`

The stack includes:

- `app`
- `frontend`
- `db`
- `mqtt-broker`
- `elasticsearch`
- `kibana`
- `metricbeat`

## MQTT live updates

BodaConnect uses MQTT for live ride status synchronization.

Customer updates are published to:

```text
ride/status/{customerId}
```

Admin updates are published to:

```text
ride/status/admin
```

Default MQTT settings are defined in `.env.example`:

```env
MQTT_ENABLED=true
MQTT_HOST=127.0.0.1
MQTT_PORT=1883
MQTT_CLIENT_ID_PREFIX=bodaconnect-backend
MQTT_CONNECT_TIMEOUT=1
MQTT_SOCKET_TIMEOUT=1
MQTT_KEEP_ALIVE_INTERVAL=60
MQTT_TOPIC_RIDE_STATUS=ride/status
MQTT_TOPIC_ADMIN_RIDE_STATUS=ride/status/admin
```

The local broker also exposes WebSockets on port `9001`.

## Monitoring

This project includes an Elastic monitoring stack for container and host metrics:

- `elasticsearch` stores metrics
- `kibana` provides dashboards
- `metricbeat` collects host, container, and MySQL metrics
- `mqtt-broker` is included in the runtime stack alongside the app services

Open Kibana at:

```text
http://localhost:5601
```

Then check:

```text
Analytics -> Dashboards
```

or:

```text
Observability -> Infrastructure
```

Filter by container names such as:

- `bodaconnect-app`
- `bodaconnect-frontend`
- `bodaconnect-db`
- `bodaconnect-mqtt`

## API summary

Important API areas:

- Auth: `/api/auth/*`
- Customer rides: `/api/rides`
- Rider rides: `/api/rider/rides`
- Admin rides: `/api/admin/rides`
- Dashboard: `/api/dashboard`

## Testing

Run the Laravel test suite with:

```bash
php artisan test --compact
```

Run the frontend production build with:

```bash
cd frontend
npm run build
```

## Deployment notes

- Run `php artisan optimize` during production deployment.
- Run `php artisan event:cache` in production to cache discovered event listeners.
- Restart long-running queue workers after deployment if queue processing is enabled.

## Default seeded admin

After seeding, the default admin account is:

- Email: `admin@bodaconnect.test`
- Password: `password`

 docker build --pull --no-cache -t bodaconnect:latest . && \
 docker run -d \
 --name bodaconnect \
 -p 8080:80 \
 --env-file .env \
 -v "$(pwd)/storage:/var/www/html/storage:Z" \
 -v "$(pwd)/database:/var/www/html/database:Z" \
 bodaconnect:latest
 
 
 
 docker run -d \
 --name bodaconnection \
 -p 8081:80 \
 --env-file .env \
 -v "$(pwd)/storage:/var/www/html/storage:Z" \
 -v "$(pwd)/database:/var/www/html/database:Z" \
 bodaconnection:latest



 docker run -d \
 --name bodaconnect \
 -p 8080:80 \
 --env-file .env \
 bodaconnect:latest




  docker rm -f bodaconnect
  docker run -d \
    --name bodaconnect \
    -p 8080:80 \
    --env-file .env \
    -v "$(pwd)/storage:/var/www/html/storage:Z" \
    -v "$(pwd)/database:/var/www/html/database:Z" \
    bodaconnect:latest






docker network create bodaconnect-net

docker rm -f bodaconnect-db bodaconnect-app 2>/dev/null

docker run -d \
--name bodaconnect-db \
--network bodaconnect-net \
--network-alias db \
-e MYSQL_DATABASE=bodaconnect \
-e MYSQL_USER=bodaconnect \
-e MYSQL_PASSWORD=secret \
-e MYSQL_ROOT_PASSWORD=rootsecret \
-v bodaconnect_mysql_data:/var/lib/mysql \
docker.io/library/mysql:8.4

docker run -d \
--name bodaconnect-app \
--network bodaconnect-net \
-p 8080:80 \
--env-file .env \
-v "$(pwd)/storage:/var/www/html/storage" \
-v "$(pwd)/database:/var/www/html/database" \
bodaconnect-app:latest

Then wait a few seconds and run:

docker exec -it bodaconnect-app php artisan migrate

If you want to confirm the DB is ready before migrating:






• I added the monitoring stack to your app. Kibana alone is not enough for CPU, RAM, and storage metrics, so I
configured the full minimal setup: elasticsearch + kibana + metricbeat.


The main changes are in docker-compose.yml:1, where I added:

- elasticsearch on port 9200
- kibana on port 5601
- metricbeat to collect host and Docker container metrics
- labels on app so Metricbeat can discover the app container

I also added the Metricbeat config in monitoring/metricbeat.yml:1 and usage notes in README.md:61.

I validated the compose file with docker compose config, but I could not start the containers here because the
container runtime is not running:
Cannot connect to the Docker daemon at unix:///run/user/1000/podman/podman.sock

Once Docker or Podman is running on your machine, start everything with:

docker compose up -d

Then open http://localhost:5601 and check:

- Observability -> Infrastructure
- Analytics -> Dashboards


kibana http://localhost:5601


 

 in order to perfom monitoring of my app to check cpu ram and storage of the my app i need to create another container for monitoring my app so please create another container for me "kibana" and configure witth my app so that i can perfom that task now










1. On the left panel, there is a field list.
2. Search for each field name there.
3. Click the field.
4. Choose Add field as column.

Try these first because they are the most likely to exist:

@timestamp
container.name
event.module
host.name
docker.container.name
docker.container.image

Then try these if present:






- system module: CPU, load, memory, network, process, filesystem, disk I/O for the host.
- docker module: container CPU, memory, network, health, disk I/O.








Useful Commands:


docker network create bodaconnect-net

docker compose up -d --force-recreate app metricbeat




docker stop $(docker ps -q)


docker rm -f $(docker ps -q)










build and push to the docker 
$docker build --target app -t confidehub/bodaconnect-app:latest .

docker tag confidehub/bodaconnect-app:latest confidehub/bodaconnect-app:v1




cd ~/apps/bodaconnect/production && export COMPOSE_PROJECT_NAME='bodaconnect-production' && docker compose -f docker-compose.deploy.yml exec -T app php artisan db:seed --force

cd ~/apps/bodaconnect/staging && export COMPOSE_PROJECT_NAME='bodaconnect-staging' && docker compose -f docker-compose.deploy.yml exec -T app php artisan db:seed --force
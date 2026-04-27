 docker build --pull --no-cache -t bodaconnect:latest . && \
 docker run -d \
 --name bodaconnect \
 -p 8080:80 \
 --env-file .env \
 -v "$(pwd)/storage:/var/www/html/storage:Z" \
 -v "$(pwd)/database:/var/www/html/database:Z" \
 bodaconnect:latest
 
 
 
 
 
 docker run -d \
 --name bodaconnect \
 -p 8081:80 \
 --env-file .env \
 -v "$(pwd)/storage:/var/www/html/storage:Z" \
 -v "$(pwd)/database:/var/www/html/database:Z" \
 bodaconnection:latest

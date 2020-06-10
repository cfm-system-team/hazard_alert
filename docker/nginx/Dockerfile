ARG NGINX_IMAGE_VERSION
FROM nginx:${NGINX_IMAGE_VERSION}

# Issue self certificate
RUN apt-get update \
  && apt-get install -y openssl \
  && openssl genrsa 2048 > server.key \
  && openssl req -new -key server.key -subj "/C=JP/ST=Tokyo/L=Shinjyuku-ku/O=Example, inc./OU=web/CN=localhost" > server.csr \
  && openssl x509 -in server.csr -days 3650 -req -signkey server.key > server.crt \
  && mv server.crt /etc/nginx/server.crt && mv server.key /etc/nginx/server.key \
  && mkdir -p /var/www/html && chmod 755 -R /var/www/html \
  && chmod 400 /etc/nginx/server.key \
  # Clear cache
  && apt-get clean && rm -rf /var/lib/apt/lists/*

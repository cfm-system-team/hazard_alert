#!/bin/sh

# env path
PROJECT_ROOT=/var/www/html
MAIN_ENV_PATH=$PROJECT_ROOT/.env
DUSK_ENV_PATH=$PROJECT_ROOT/.env.dusk.testing
TESTING_ENV_PATH=$PROJECT_ROOT/.env.testing

cp $DUSK_ENV_PATH $MAIN_ENV_PATH
cp $DUSK_ENV_PATH $TESTING_ENV_PATH

# .env database connection settings override.
sed -i -e "s/DB_HOST=.*/DB_HOST=db/g" $MAIN_ENV_PATH
sed -i -e "s/DB_PORT=.*/DB_PORT=3306/g" $MAIN_ENV_PATH
sed -i -e "s/DB_DATABASE=.*/DB_DATABASE=$DATABASE/g" $MAIN_ENV_PATH
sed -i -e "s/DB_USERNAME=.*/DB_USERNAME=$DATABASE_USER/g" $MAIN_ENV_PATH
sed -i -e "s/DB_PASSWORD=.*/DB_PASSWORD=$DATABASE_PASSWORD/g" $MAIN_ENV_PATH

# .env.testing database connection settings override.
sed -i -e 's/APP_ENV=.*/APP_ENV=testing/g' $TESTING_ENV_PATH
sed -i -e 's/APP_URL=.*/APP_URL=http:\/\/nginx:80/g' $TESTING_ENV_PATH

sed -i -e "s/DB_HOST=.*/DB_HOST=db/g" $TESTING_ENV_PATH
sed -i -e "s/DB_PORT=.*/DB_PORT=3306/g" $TESTING_ENV_PATH
sed -i -e "s/DB_DATABASE=.*/DB_DATABASE=$TESTING_DATABASE/g" $TESTING_ENV_PATH
sed -i -e "s/DB_USERNAME=.*/DB_USERNAME=$DATABASE_USER/g" $TESTING_ENV_PATH
sed -i -e "s/DB_PASSWORD=.*/DB_PASSWORD=$DATABASE_PASSWORD/g" $TESTING_ENV_PATH

php artisan migrate

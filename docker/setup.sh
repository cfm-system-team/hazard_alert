#!/bin/sh

# env path
PROJECT_ROOT=/var/www/html
MAIN_ENV_PATH=$PROJECT_ROOT/.env
TESTING_ENV_PATH=$MAIN_ENV_PATH.testing
DUSK_ENV_PATH=$MAIN_ENV_PATH.dusk.testing
DUSK_DEV_ENV_PATH=$MAIN_ENV_PATH.dusk.develop

cp $DUSK_ENV_PATH $MAIN_ENV_PATH
cp $DUSK_ENV_PATH $TESTING_ENV_PATH
cp $DUSK_ENV_PATH $DUSK_DEV_ENV_PATH


# .env database connection settings override.
sed -i -e "s/DB_HOST=.*/DB_HOST=db/g" $MAIN_ENV_PATH
sed -i -e "s/DB_PORT=.*/DB_PORT=3306/g" $MAIN_ENV_PATH
sed -i -e "s/DB_DATABASE=.*/DB_DATABASE=$DATABASE/g" $MAIN_ENV_PATH
sed -i -e "s/DB_USERNAME=.*/DB_USERNAME=$DATABASE_USER/g" $MAIN_ENV_PATH
sed -i -e "s/DB_PASSWORD=.*/DB_PASSWORD=$DATABASE_PASSWORD/g" $MAIN_ENV_PATH

# .env.testing database connection settings override.
sed -i -e "s/DB_HOST=.*/DB_HOST=db/g" $TESTING_ENV_PATH
sed -i -e "s/DB_PORT=.*/DB_PORT=3306/g" $TESTING_ENV_PATH
sed -i -e "s/DB_DATABASE=.*/DB_DATABASE=$TESTING_DATABASE/g" $TESTING_ENV_PATH
sed -i -e "s/DB_USERNAME=.*/DB_USERNAME=$DATABASE_USER/g" $TESTING_ENV_PATH
sed -i -e "s/DB_PASSWORD=.*/DB_PASSWORD=$DATABASE_PASSWORD/g" $TESTING_ENV_PATH

# .env.dusk.testing database connection settings override.
sed -i -e 's/APP_URL=.*/APP_URL=http:\/\/nginx:80/g' $DUSK_DEV_ENV_PATH

sed -i -e "s/DB_HOST=.*/DB_HOST=db/g" $DUSK_DEV_ENV_PATH
sed -i -e "s/DB_PORT=.*/DB_PORT=3306/g" $DUSK_DEV_ENV_PATH
sed -i -e "s/DB_DATABASE=.*/DB_DATABASE=$TESTING_DATABASE/g" $DUSK_DEV_ENV_PATH
sed -i -e "s/DB_USERNAME=.*/DB_USERNAME=$DATABASE_USER/g" $DUSK_DEV_ENV_PATH
sed -i -e "s/DB_PASSWORD=.*/DB_PASSWORD=$DATABASE_PASSWORD/g" $DUSK_DEV_ENV_PATH

composer install
npm i
npm run dev

php artisan key:generate
php artisan key:generate --env=testing
php artisan key:generate --env=dusk.develop
php artisan migrate

php artisan dusk:chrome-driver
php artisan dusk --env=develop
$PROJECT_ROOT/vendor/bin/phpunit --testdox

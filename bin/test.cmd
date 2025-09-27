docker compose run --rm composer install
docker compose run --rm composer dump-autoload
docker compose run --rm composer vendor/bin/phpunit --colors=always --testdox tests
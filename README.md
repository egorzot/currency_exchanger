# Money Exchanger

#### Start
```
git clone git@github.com:egorzot/currency_exchanger.git

cd currency_exchanger/docker 

docker-compose up  

docker-compose run php-fpm bin/console doctrine:migrations:migrate

docker-compose run php-fpm bin/console doctrine:fixtures:load
```

Open [http://localhost/](http://localhost/)


#### Tests

```
docker-compose run php-fpm  ./bin/phpunit
```

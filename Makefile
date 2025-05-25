.PHONY: up build down test shell logs

vendor:
	cd analytics
	composer install

build:
	docker-compose up --build -d

up:
	docker-compose up -d

test:
	docker exec twitch-analytics ./analytics/vendor/bin/phpunit --configuration=./analytics/phpunit.xml

down:
	docker-compose down

.env=
	DB_CONNECTION=mysql
	DB_HOST=db
	DB_PORT=3306
	DB_DATABASE=twitch
	DB_USERNAME=twitchuser
	DB_PASSWORD=twitchpass
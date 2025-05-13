.PHONY: up build down test shell logs

build:
	docker-compose up --build -d

up:
	docker-compose up -d

down:
	docker-compose down

test:
	docker exec twitch-analytics ./analytics/vendor/bin/phpunit --configuration=./analytics/phpunit.xml


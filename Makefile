.PHONY : main build-image build-container start test shell stop clean
main: build-image build-container

build-image:
	docker build -t twitch-analytics .

build-container:
	docker run -dt --name twitch-analytics -v .:/analytics twitch-analytics
	docker exec twitch-analytics composer install

start:
	docker start twitch-analytics

test: start
	docker exec twitch-analytics ./analytics/vendor/bin/phpunit --configuration=./analytics/phpunit.xml

shell: start
	docker exec -it twitch-analytics /bin/bash

stop:
	docker stop twitch-analytics

clean: stop
	docker rm twitch-analytics
	rm -rf vendor
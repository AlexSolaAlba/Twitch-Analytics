
.PHONY : main build-image build-container start test shell stop clean
main: build-image build-container

build-image:
	docker build -t twitch-analytics .

build-container:
	docker run -dt --name twitch-analytics -v .:/TwitchAnalytics twitch-analytics
	docker exec twitch-analytics composer install --working-dir=/TwitchAnalytics/docker/lumen

start:
	docker start twitch-analytics

test: start
	docker exec twitch-analytics /TwitchAnalytics/docker/lumen/vendor/bin/phpunit --configuration=/TwitchAnalytics/docker/lumen/phpunit.xml

shell: start
	docker exec -it twitch-analytics  /bin/bash

stop:
	docker stop twitch-analytics

clean: stop
	docker rm twitch-analytics
	rm -rf vendor

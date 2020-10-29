FIG=docker-compose
RUN=$(FIG) run --rm
EXEC=$(FIG) exec

.PHONY: start stop reset install build vendor up

start: build up

install: start vendor

stop:
	$(FIG) stop && $(FIG) rm -f

reset: stop start

build:
	$(FIG) build

up:
	$(FIG) up -d

cc:
	$(RUN) php php bin/console cache:clear

vendor:
	$(RUN) -w /app php composer install
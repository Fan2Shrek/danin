docker := $(shell which docker)
compose := $(docker) compose

up:
	$(compose) up -d

install:
	$(compose) run --rm php composer install
	$(compose) run --rm front npm install

dev: install up

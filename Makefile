TIMESTAMP := $(shell /bin/date "+%s")
RANDOM_NAME := $(shell rig | awk 'NR==1')
RANDOM_PHONE := $(shell rig | awk 'NR==4')
RANDOM_EMAIL := $(shell echo $(RANDOM_NAME) | tr '[:upper:]' '[:lower:]' | tr " " ".")

start:
	docker-compose up -d

stop:
	docker-compose down

docker-rm-volumes:
	docker volume rm $(docker volume ls -q)

app-sh:
	docker exec -it appserver /bin/bash

mysql-sh:
	docker exec -it dbserver /bin/bash

doPost:
	curl -X POST http://localhost:8080/customers-data/create -H 'Content-Type: application/json' -d '{"name":"$(RANDOM_NAME)","email":"$(RANDOM_EMAIL)@gmail.com","phone":"$(RANDOM_PHONE)"}'

doPut:
	curl -X PUT http://localhost:8080/customers-data/update/1 -H 'Content-Type: application/json' -d '{"name":"$(RANDOM_NAME)","email":"$(RANDOM_EMAIL)@gmail.com","phone":"$(RANDOM_PHONE)"}'

doPostFormData:
	curl -X POST http://localhost:8080/api/v1/create -H 'Content-Type: application/json' -d '{"name":"Ricardo Silveira","description":"Lorem ipsum","born_at":"1987-04-21","gender":"M"}'

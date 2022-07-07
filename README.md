# checklists

Работа с чек-листами

Установка
1) Необходим установленный docker, docker-compose, php
2) Клонировать репозиторий, перейти в склонированную папку, переименовать .env.local в .env
3) Выполнить make composer-install
4) Выполнить последовательно make build и make up для создания контейнеров
5) Выполнить последовательно make migrate, make rbac-migrate, make rbac-init для проведения миграций
6) Зайти на http://localhost:8081/ (при необходимости изменить права на папку проекта)
7) Пользователь admin, пароль admin, токен доступа - YWRtaW46JDJ5JDEzJDF3bklOb0syLlZWUkJnalFMVmhCb3UwTVhGNlNxLlIuNUdjUjR4NEJuWnZUbHMzMXBEeExh
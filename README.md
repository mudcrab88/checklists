# checklists

Николай Кузнецов

Тестовое задание на PHP

Работа с чек-листами

Установка
1) Необходим установленный docker, docker-compose, php
2) Клонировать репозиторий, перейти в склонированную папку, переименовать .env.local в .env
3) Выполнить последовательно make build и make up для создания контейнеров
4) Выполнить последовательно make migrate, make rbac-migrate, make rbac-init для проведения миграций
5) Выполнить make composer-install
6) Зайти на http://localhost:8081/
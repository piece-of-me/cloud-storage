# Требования

* Установленный [node-js](https://nodejs.org/en/download/).
* Установленный [composer](https://getcomposer.org/download/).
* Установленный [Docker](https://docs.docker.com/engine/install/).

# Установка

### Клонирование репозитория
* Клонировать репозиторий `git clone https://github.com/piece-of-me/cloud-storage.git`;
* Перейти в папку storage `cd cloud-storage`;

### Установка пакетов и зависимостей
* Установить зависимости с помощью `npm install` и `composer install`;

### Запуск приложения
- Конвертируйте `.env.example` файл в Unix-формат с помощью `dos2unix .env.example`;
- Скопировать переменные окружения `cat .env.example > .env`;
- Создать и запустить контейнеры с помощью `docker-compose up  -d`;
- Запустить оболочку `bash` в контейнер `app` с помощью `docker exec -it app bash`;
- Выполнить миграцию и запустить сидеры с помощью `php artisan migrate --seed`;
- Запустить сервер разработки с помощью `npm run dev`;
- При необходимости сгенерируйте новый ключ с помощью `php artisan key:generate`;

# Дополнительно
- Существует тестовый пользователь: `email: test_user0@mail.com`, `password: 8JSkbBn0SlQQ`

# Используемые технологии

- MySQL;
- Laravel 9;
- Vue 3;
- Mailtrap для отправки сообщений.
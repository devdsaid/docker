На сервере создаем папку src
проваливаемся в нее , поднимаем composer 
docker-compose run composer create-project laravel/laravel . 
Данная команда установит последнюю версию , в соответствии версий PHP , в моем случае 8.3, а значит Laravel 12

После этого проваливаемся в /src , меняем .env подключение , не забудьте что имя хоста подключения, это имя контейнера.

Далее поднимаем artisan (автоматом упадет после выполнение работы)
docker-compose run artisan key:generate
docker-compose run artisan migrate
docker-compose run artisan cache:clear
docker-compose run artisan optimize:clear

Ну вот и все)

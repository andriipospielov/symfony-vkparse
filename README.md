**Приложение для полученя альбомов и фото указанного пользователя в ВК.
Использует Symfony 3.2, RabbitMQ и два третесторонних бандла: для связи с апи ВК, и для связи з Rabbit'ом**.


**Перед запуском:** <br>

`composer update`<br>
`php bin/console doctrine:schema:update --force`<br>
Для запуска компонента-консьюмера:<br>
`bin/console rabbit:consumer download_picture`


**Работа приложения:** <br>

Пропарсить страницу по id: <br>
`bin/console vk:get --id=1`<br>
По csv:<br> 
`bin/console vk:get --csv=/home/andrii/test.csv`<br>
Отобразить сохраненные профили:<br>
`bin/console vk:show`<br>
Отобразить сохраненные альбомы для профиля:<br>
`bin/console vk:show --id=1`

**Изображения будут сохранены в `~/vkpics/%ид_пользователя%/%ид_альбома%/`**

# GeolocationWeatherTelegramBot-PHP

<div align="center">
  <img alt="GitHub top language" src="https://img.shields.io/github/languages/top/SuvorIgo/GeolocationWeatherTelegramBot-PHP">
  <img alt="Relative date" src="https://img.shields.io/date/1666549781">
</div>

***
<div align="center"><a href="https://ibb.co/ZJFY5QB"><img src="https://i.ibb.co/n0Yn9X7/geo.jpg" alt="geo" border="0"></a></div>

## Описание
Данный бот предназначен для определения погоды по переданным пользоветелем Telegram'a данным о своей геолокации.

## Способ получения и отправка данных (API Telegram)

Программа получает данные с помощью запроса:
```
https://api.telegram.org/bot<токен бота, выданный отцом ботов>/getUpdates?<параметры>
```
Запрос, позволяющий отправить информацию юзеру через бота
```
https://api.telegram.org/bot<токен>/sendMessage
```

## Как это работает?

User отправляет свою геолокацию в чат с ботом. Далее происходит получение update'а посредством __getUpdates__ и его парсинг, получение долготы и широты. После, обработанная информация вносится в запрос с получением данных о выявлении наименования населенного пункта с помощью __Yandex Geocoder API__.
Примерный запрос:
```
https://geocode-maps.yandex.ru/1.x/?apikey=<ключ api Yandex geocoder>&format=json&geocode=<долгота>,<широта>
```
Получаю json message, парсю, по переданной геолокации получаю наименование населенного пункта.
Использовав второе API от яндекса (__Yandex Weather API__), я получил информацию о температуре, влажности воздуха и некоторым другим параметрам.
Нюанс: GET запрос будет работать только с добавление заголовка такого характера:
>X-Yandex-API-Key: <ключ api Yandex weather>

Примерный запрос:
```
https://api.weather.yandex.ru/v2/informers?lat=<значени широты>&lon=<значение долготы>
```
Отправка производится методом API Telegram'a __sendMessage__

#### [Переход к боту](https://t.me/geolocweather_bot)

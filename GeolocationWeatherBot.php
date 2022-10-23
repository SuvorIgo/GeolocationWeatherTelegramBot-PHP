<?php
require_once(__DIR__.'/config.php');

function sendRequestOfTelegram($method, $params) : array {
    $url = APIBOTBASEURL.$method.'?'.http_build_query($params);
    return json_decode(file_get_contents($url), true);
}

$updates = sendRequestOfTelegram('getUpdates',['offset' => -1]);
foreach($updates['result'] as $update){
    $lat = $update['message']['location']['latitude'];
    $lon = $update['message']['location']['longitude'];
}

function sendRequestToTheGeocoderAPI() : array {
    $url = APIYANDEXLOCATORBASEURL.$GLOBALS['lon'].",".$GLOBALS['lat']."&kind=locality&results=1&lang=ru_RU";
    return json_decode(file_get_contents($url), true);
}

function collectingInformationFromGeocoder() : string{
    $result = sendRequestToTheGeocoderAPI();
    return $locality = $result['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];
}

function sendRequestToTheWeatherAPI() : array {
    $opts = array(
        'http'=>array(
          'method'=>"GET",
          'header'=>"X-Yandex-API-Key: ".KEYAPIYANDEXWEATHER.""
        )
      );

    $context = stream_context_create($opts);
      
    $url = APIYANDEXWEATHERBASEURL.'lat='.$GLOBALS['lat'].'&lon='.$GLOBALS['lon'];

    return json_decode(file_get_contents($url, false, $context), true);
}

function collectingInformationFromWeather() : array {
    $result = sendRequestToTheWeatherAPI();

    $arrresult[] = $temp = $result['fact']['temp'];
    $arrresult[] = $condition = $result['fact']['condition'];
    $arrresult[] = $wind_speed = $result['fact']['wind_speed'];
    $arrresult[] = $humidity = $result['fact']['humidity'];

    return $arrresult;
}

$locality = collectingInformationFromGeocoder();
$messageWeather = collectingInformationFromWeather();


$message = "$locality: температура: ".$messageWeather[0]." °C; описание: ".getCondition($messageWeather[1])."; скорость ветра: ".$messageWeather[2]." м/с; влажность воздуха: ".$messageWeather[3]." %";

foreach($updates['result'] as $update){
    $chat_id = $update['message']['chat']['id'];
    sendRequestOfTelegram('sendMessage',['chat_id' => $chat_id, 'text' => $message]);
}

function getCondition($condition) : string {
    switch($condition){
        case 'clear': return 'ясно'; break;
        case 'partly-cloudy': return 'малооблачно'; break;
        case 'cloudy': return 'облачно с прояснениями'; break;
        case 'overcast' : return 'пасмурно'; break;
        case 'drizzle': return 'морось'; break;
        case 'light-rain': return 'небольшой дождь'; break;
        case 'rain': return 'дождь'; break;
        case 'moderate-rain': return 'умеренно сильный дождь'; break;
        case 'heavy-rain': return 'сильный дождь'; break;
        case 'continuous-heavy-rain': return 'длительный сильный дождь'; break;
        case 'showers': return 'ливень'; break;
        case 'wet-snow': return 'дождь со снегом'; break;
        case 'light-snow': return 'небольшой снег'; break;
        case 'snow': return 'снег'; break;
        case 'snow-showers': return 'снегопад'; break;
        case 'hail': return 'град'; break;
        case 'thunderstorm': return 'гроза'; break;
        case 'thunderstorm-with-rain': return 'дождь с грозой'; break;
        case 'thunderstorm-with-hail': return 'гроза с градом'; break;
        default: return 'null';
    }
}
?>
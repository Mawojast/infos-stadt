<?php

namespace App\Http\Controllers;

use App\classes\WeatherManager;
use App\Traits\DirectoryHelper;
use Cache;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\City;
use App\Classes\Article;
use App\Classes\Weather;
use App\Classes\ArticleManager;
use App\Classes\UrlApi;
use App\Classes\OpenWeatherMap;

class HomeController extends Controller
{
    use DirectoryHelper;
    public $substitutedArticleImage = [];

    public function home(){

        $backgroundImagePaths = $this->getPublicFilePaths('images/home/');
        $backgroundImagePath = $this->getRandomFilePath($backgroundImagePaths);
        return view('home', compact('backgroundImagePath'));
    }

    public function search(Request $request){

        $weather = $this->getWeather($request->stadt);

        if(!empty($weather->code)){

            $city = City::where('name', $weather->city)->where('latitude', $weather->lat)->where('longitude', $weather->lon)->get();

            if($city->isEmpty()){
                $cityDescription = $this->getCityDescription($weather->city, $weather->lat, $weather->lon);

                City::insert([
                    'name' => $weather->city,
                    'longitude' => $weather->lon,
                    'latitude' => $weather->lat,
                    'country' => $weather->country,
                    'description' => $cityDescription,
                    'timezone' => $weather->timezone,
                    'created_at' => Carbon::now(),
                ]);
            }else{
                $cityDescription = $city[0]->description;
            }
            $weatherManager = new WeatherManager($weather);

            $dateTime = $weatherManager->getTime();
            $is_day = $weatherManager->is_day();
            $titleIcons = $weatherManager->getTitleIcons(app_path('Data/weather_codes.json'));
            $weatherIconPath = $weatherManager->getWeatherImagePath(app_path('Data/weather_codes.json'), 'images/weather/');
            $weatherBackgroundImagePath = $weatherManager->getWeatherImagePath(app_path('Data/weather_codes.json'), 'images/background/');

            if(Cache::has($request->getRequestUri())){
                echo "getRequest";
                $articles = Cache::get($request->getRequestUri());
            }else{
                $articles = $this->getMediaStack($weather->city);
                Cache::put($request->getRequestUri(), $articles, 60);
            }
            return view('city', compact('weather', 'weatherIconPath','weatherBackgroundImagePath', 'articles', 'is_day', 'dateTime', 'cityDescription', 'articles', 'titleIcons'));
        }else{
            return view('city_not_found');
        }

    }
    public function city($city, Request $request){

        $weather = $this->getWeather($city);
        if( !empty($weather->code)){
            $city = City::where('name', $weather->city)->where('latitude', $weather->lat)->where('longitude', $weather->lon)->get();

            if($city->isEmpty()){
                $cityDescription = $this->getChat($weather);
                City::insert([
                    'name' => $weather->city,
                    'longitude' => $weather->lon,
                    'latitude' => $weather->lat,
                    'country' => $weather->country,
                    'description' => $cityDescription,
                    'timezone' => $weather->timezone,
                    'created_at' => Carbon::now(),
                ]);
            }else{
                $cityDescription = $city[0]->description;
            }

            $weatherManager = new WeatherManager($weather);

            $dateTime = $weatherManager->getTime();
            $is_day = $weatherManager->is_day();
            $titleIcons = $weatherManager->getTitleIcons(app_path('Data/weather_codes.json'));
            $weatherIconPath = $weatherManager->getWeatherImagePath(app_path('Data/weather_codes.json'), 'images/weather/');
            $weatherBackgroundImagePath = $weatherManager->getWeatherImagePath(app_path('Data/weather_codes.json'), 'images/background/');

            if(Cache::has($request->getRequestUri())){
                echo "getRequest";
                $articles = Cache::get($request->getRequestUri());
            }else{
                $articles = $this->getMediaStack($weather->city);
                Cache::put($request->getRequestUri(), $articles, 60);
            }

            return view('city', compact('weather', 'weatherIconPath','weatherBackgroundImagePath', 'articles', 'is_day', 'dateTime', 'cityDescription', 'articles', 'titleIcons'));
        }else{
            return view('city_not_found');
        }
    }

    public function list(){

        $cities = City::select('name')->orderBy('name')->get();

        return view('list', compact('cities'));
    }

    public function listByLetter(Request $request){

        $letter = $request->buchstabe;
        $cities = City::where('name', 'like', $letter.'%')->get();

        return view('list_letter', compact('cities', 'letter'));
    }

    public function privacyPolicy(){

        return view('privacy_policy');
    }

    public function imprint(){

        return view('imprint');
    }

    public function getWeather($city): Weather{

        try{
            $OpenWeatherMapApi = new OpenWeatherMap();

            $OpenWeatherMapApi->setKey(env('OPENWEATHERMAP_API_KEY'));
            $OpenWeatherMapApi->addParam('q', $city);
            $OpenWeatherMapApi->addParam('units', 'metric');
            $OpenWeatherMapApi->addParam('lang', 'de');;
            $OpenWeatherMapApi->request();
            $apiResult = $OpenWeatherMapApi->getResult();

            if($apiResult['cod'] === 200){
                $Weather = new Weather(
                    $apiResult['weather'][0]['id'],
                    $apiResult['sys']['country'],
                    $apiResult['name'],
                    $apiResult['coord']['lat'],
                    $apiResult['coord']['lon'],
                    $apiResult['weather'][0]['description'],
                    $apiResult['main']['temp'],
                    $apiResult['wind']['speed'],
                    $apiResult['timezone']);

                return $Weather;
            }

        }catch(Exception $e){
            return new Weather();
        }

        return new Weather();
    }

    public function getNews($city){

        try{

            $NewsApi = new UrlApi(env('NEWS_API_HOST'));

            $NewsApi->addParam('apiKey',env('NEWS_API_KEY'));
            $NewsApi->addParam('q', $city);
            $NewsApi->addParam('language', 'de');
            $NewsApi->addParam('sortBy', 'publishedAt');
            $NewsApi->addParam('pageSize', 12);
            $NewsApi->request();
            $result = $NewsApi->getResult();

            $ArticleManager = New ArticleManager();

            foreach($result['articles'] as $article){
                $ArticleManager->addArticle(new Article($article['title'], $article['description'], $article['published_at'],$article['url'], $article['author'], $article['image']));
            }

            $ArticleManager->removeDoubleArticles();
            $ArticleManager->setEmptyArticleImages();


            return $ArticleManager->articles;

        }catch(Exception $e){

            return [];
        }
    }

    public function getMediaStack($city){

        try{

            $MediaStackApi = new UrlApi(env('MEDIASTACK_API_HOST'));

            $MediaStackApi->addParam('access_key',env('MEDIASTACK_API_KEY'));
            $MediaStackApi->addParam('keywords', $city);
            $MediaStackApi->addParam('sort', 'published_desc');
            $MediaStackApi->addParam('languages', 'de');
            $MediaStackApi->addParam('limit', 20);
            $MediaStackApi->request();

            $result = $MediaStackApi->getResult();
            $ArticleManager = New ArticleManager();

            foreach($result['data'] as $article){
                $ArticleManager->addArticle(new Article(
                    $article['title'],
                    $article['description'],
                    $article['published_at'],
                    $article['url'],
                    $article['source'],
                    $article['image'])
                );
            }

            $ArticleManager->removeDoubleArticles();
            $ArticleManager->setEmptyArticleImages();
            return $ArticleManager->articles;

        }catch(Exception $e){
            return [];
        }
    }

    public function getCityDescription($name, $lat, $lon){
        try{
            $random = rand(1, 2);
            $promptName = 'PROMPT_'.$random;
            $prompt = str_replace(['\'.$name.\'', '\'.$lat.\'', '\'.$lon.\''], [$name, $lat, $lon], env($promptName));

            $result = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'max_tokens' => 1500,
                'prompt' => $prompt,
            ]);
            return $result['choices'][0]['text'];
        }catch(Exception $e){
            return '';
        }
    }

    public function is_day($hour, $minute){

        $currentTime = Carbon::createFromTime($hour, $minute);
        $startTime = Carbon::createFromTime(5, 0, 0);
        $endTime = Carbon::createFromTime(22, 0, 0);

        return ($currentTime->greaterThanOrEqualTo($startTime) && $currentTime->lessThanOrEqualTo($endTime));
    }

    public function getTitleIcons(int $id, bool $is_day){

        $icons = [];
        $weatherCode = [
            "clear" => [800],
            "fewClouds" => [801],
            "moderateClouds" => [802, 803],
            "overcastClouds" => [804],
            "moderateRain" => [500, 501, 502, 503, 504],
            "heavyRain" => [511, 520, 521, 522, 531],
            "thunderstorm" => [200, 201, 202, 210, 211, 212, 221, 230, 231, 232],
            "drizzle" =>[300, 301, 302, 310, 311, 312, 313, 314, 321],
            "mist" => [701, 711, 721, 731, 741, 751, 761, 762, 771, 781]
        ];

        if(in_array($id, $weatherCode['clear'])){
            if($is_day){
                $icons[] = '&#9728;';
            }else{
                $icons[] ='&#9790;';
            }
        }
        if(in_array($id, $weatherCode["fewClouds"])){
            if($is_day){
                $icons[] = '&#9728;';
                $icons[] = '&#9729;';
            }else{
                $icons[] ='&#9790;';
                $icons[] = '&#9729;';
            }
        }
        if(in_array($id, $weatherCode["moderateClouds"])){
            if($is_day){
                $icons[] = '&#9728;';
                $icons[] = '&#9729;';
            }
        }
        if(in_array($id, $weatherCode["overcastClouds"])){
            if($is_day){
                $icons[] = '&#9729;';
            }else{
                $icons[] ='&#x1F312;';
                $icons[] = '&#9729;';
            }
        }
        if(in_array($id, $weatherCode["moderateRain"])){
            if($is_day){
                $icons[] = '&#9730;';
            }else{
                $icons[] ='&#x1F312;';
                $icons[] = '&#9730;';
            }
        }
        if(in_array($id, $weatherCode["heavyRain"])){
            if($is_day){
                $icons[] = '&#9730;';
            }else{
                $icons[] ='&#x1F312;';
                $icons[] = '&#9730;';
            }
        }
        if(in_array($id, $weatherCode["thunderstorm"])){
            if($is_day){
                $icons[] = '&#9730;';
                $icons[] = '&#9888;';
            }else{
                $icons[] ='&#x1F312;';
                $icons[] = '&#9888;';
            }

        }
        if(in_array($id, $weatherCode["drizzle"])){
            if($is_day){
                $icons[] = '&#9730;';
            }else{
                $icons[] = '&#9730;';
            }
        }

        if(in_array($id, $weatherCode["mist"])){
            if($is_day){
                $icons[] = '&#127787;';
            }else{
                $icons[] = '&#127787;';
            }
        }

        return $icons;
    }
    public function getWeatherImagePath($id, $is_day, $type){

        $rootPath = '';
        if($type == 'page_icon'){
            $rootPath = 'images/weather/';
        }
        elseif($type == 'background_image'){
            $rootPath = 'images/background/';
        }
        $folderPath = '';
        $weatherCode = [
            "clear" => [800],
            "fewClouds" => [801],
            "moderateClouds" => [802, 803],
            "overcastClouds" => [804],
            "moderateRain" => [500, 501, 502, 503, 504],
            "heavyRain" => [511, 520, 521, 522, 531],
            "thunderstorm" => [200, 201, 202, 210, 211, 212, 221, 230, 231, 232],
            "drizzle" =>[300, 301, 302, 310, 311, 312, 313, 314, 321],
            "mist" => [701, 711, 721, 731, 741, 751, 761, 762, 771, 781]
        ];

        if(in_array($id, $weatherCode['clear'])){
            $folderPath = $rootPath.'clear/';
        }
        if(in_array($id, $weatherCode["fewClouds"])){
            $folderPath = $rootPath.'few_clouds/';
        }
        if(in_array($id, $weatherCode["moderateClouds"])){
            $folderPath = $rootPath.'few_clouds/';
        }
        if(in_array($id, $weatherCode["overcastClouds"])){
            $folderPath = $rootPath.'overcast_clouds/';
        }
        if(in_array($id, $weatherCode["moderateRain"])){
            $folderPath = $rootPath.'moderate_rain/';
        }
        if(in_array($id, $weatherCode["heavyRain"])){
            $folderPath = $rootPath.'heavy_rain/';
        }
        if(in_array($id, $weatherCode["thunderstorm"])){
            $folderPath = $rootPath.'thunderstorm/';
        }
        if(in_array($id, $weatherCode["drizzle"])){
            $folderPath = $rootPath.'drizzle/';
        }
        if(in_array($id, $weatherCode["mist"])){
            $folderPath = $rootPath.'mist/';
        }

        if( $is_day){
            $filePath = $this->getRandomFilePath($this->getPublicFilePaths($folderPath.'day/'));
        }else{
            $filePath = $this->getRandomFilePath($this->getPublicFilePaths($folderPath.'night/'));
        }

        return $filePath;
    }

    public function getCountryTimezone($countryCode){

        $timezone_identifiers = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode);
        if (count($timezone_identifiers) > 0) {
            return new DateTimeZone($timezone_identifiers[array_key_last($timezone_identifiers)]);
        } else {
            return null;
        }
    }

    public function getCountryTime($timezone){
        date_default_timezone_set($timezone);
        return date('H:i:s');
    }

}

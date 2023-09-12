<?php

namespace App\Http\Controllers;

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
use App\Classes\ArticleManager;
use App\Classes\UrlApi;
use App\Classes\OpenWeatherMap;

class HomeController extends Controller
{
    use DirectoryHelper;
    public $substitutedArticleImage = [];

    public function home(){
        $backgroundImagePaths = $this->getFilePaths('images/home/');
        $backgroundImagePath = $this->getRandomFilePath($backgroundImagePaths);
        return view('home', compact('backgroundImagePath'));
    }

    public function search(Request $request){

        $weather = $this->getWeather($request->stadt);

        if(isset($weather['cod']) && $weather['cod'] === 200){

            $city = City::where('name', $weather['name'])->where('latitude', $weather['coord']['lat'])->where('longitude', $weather['coord']['lon'])->get();

            if($city->isEmpty()){
                $cityDescription = $this->getCityDescription($weather['name'], $weather['coord']['lat'], $weather['coord']['lon']);

                City::insert([
                    'name' => $weather['name'],
                    'longitude' => $weather['coord']['lon'],
                    'latitude' => $weather['coord']['lat'],
                    'country' => $weather['sys']['country'],
                    'description' => $cityDescription,
                    'timezone' => $weather['timezone'],
                    'created_at' => Carbon::now(),
                ]);
            }else{
                $cityDescription = $city[0]->description;
            }
            $dateTime = gmdate("H:i", time() + $weather['timezone']);
            $time = new DateTime($dateTime);
            $hour = date('H', $time->getTimestamp());
            $minute = date('i', $time->getTimestamp());
            $is_day = $this->is_day($hour, $minute);

            $titleIcons = $this->getTitleIcons($weather['weather'][0]['id'], $is_day);
            $weatherIconPath = $this->getWeatherImagePath($weather['weather'][0]['id'], $is_day, 'page_icon');
            $weatherBackgroundImagePath = $this->getWeatherImagePath($weather['weather'][0]['id'], $is_day, 'background_image');

            Cache::clear();
            if(Cache::has($request->getRequestUri())){
                echo "getRequest";
                $articles = Cache::get($request->getRequestUri());
            }else{
                $articles = $this->getMediaStack($weather['name']);
                Cache::put($request->getRequestUri(), $articles, 60);
            }
            return view('city', compact('weather', 'weatherIconPath','weatherBackgroundImagePath', 'articles', 'is_day', 'dateTime', 'cityDescription', 'articles', 'titleIcons'));
        }else{
            return view('city_not_found');
        }

    }
    public function city($city, Request $request){

        $weather = $this->getWeather($city);
        if( isset($weather['cod']) && $weather['cod'] === 200){
            $city = City::where('name', $weather['name'])->where('latitude', $weather['coord']['lat'])->where('longitude', $weather['coord']['lon'])->get();

            if($city->isEmpty()){
                $cityDescription = $this->getChat($weather);
                City::insert([
                    'name' => $weather['name'],
                    'longitude' => $weather['coord']['lon'],
                    'latitude' => $weather['coord']['lat'],
                    'country' => $weather['sys']['country'],
                    'description' => $cityDescription,
                    'timezone' => $weather['timezone'],
                    'created_at' => Carbon::now(),
                ]);
            }else{
                $cityDescription = $city[0]->description;
            }
            $dateTime = gmdate("H:i", time() + $weather['timezone']);
            $time = new DateTime($dateTime);
            $hour = date('H', $time->getTimestamp());
            $minute = date('i', $time->getTimestamp());
            $is_day = $this->is_day($hour, $minute);

            $titleIcons = $this->getTitleIcons($weather['weather'][0]['id'], $is_day);
            $weatherIconPath = $this->getWeatherImagePath($weather['weather'][0]['id'], $is_day, 'page_icon');
            $weatherBackgroundImagePath = $this->getWeatherImagePath($weather['weather'][0]['id'], $is_day, 'background_image');

            Cache::clear();
            if(Cache::has($request->getRequestUri())){
                echo "getRequest";
                $articles = Cache::get($request->getRequestUri());
            }else{
                $articles = $this->getMediaStack($weather['name']);
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

    public function privacy_policy(){

        return view('privacy_policy');
    }

    public function imprint(){

        return view('imprint');
    }

    public function getWeather($city): array{

        try{
            $OpenWeatherMapApi = new OpenWeatherMap();

            $OpenWeatherMapApi->setKey(env('OPENWEATHERMAP_API_KEY'));
            $OpenWeatherMapApi->addParam('q', $city);
            $OpenWeatherMapApi->addParam('units', 'metric');
            $OpenWeatherMapApi->addParam('lang', 'de');;
            $OpenWeatherMapApi->request();
            $apiResult = $OpenWeatherMapApi->getResult();

        }catch(Exception $e){
            $apiResult =[];
        }

        return $apiResult;
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
                    $article['author'],
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
        elseif($type == 'title_icon'){

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
            $filePath = $this->getRandomFilePath($this->getFilePaths($folderPath.'day/'));
        }else{
            $filePath = $this->getRandomFilePath($this->getFilePaths($folderPath.'night/'));
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

<?php

namespace App\Http\Controllers;

use App\Classes\WeatherManager;
use App\Traits\DirectoryHelper;
use Cache;
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

        if(!isset($request->stadt)){
            abort(404);
        }
        $weather = $this->getWeather($request->stadt);

        if(!empty($weather->code)){

            $cityDescription = $this->getCityDiscription($weather);

            $weatherManager = new WeatherManager($weather);

            $dateTime = $weatherManager->getTime();
            $is_day = $weatherManager->is_day();
            $titleIcons = $weatherManager->getTitleIcons(app_path('Data/weather_codes.json'));
            $weatherIconPath = $weatherManager->getWeatherImagePath(app_path('Data/weather_codes.json'), 'images/weather/');
            $weatherBackgroundImagePath = $weatherManager->getWeatherImagePath(app_path('Data/weather_codes.json'), 'images/background/');
            $countryName = $weatherManager->getCountryNameByCode(app_path('Data/country_codes.json'));

            if(Cache::has($request->getRequestUri())){
                $articles = Cache::get($request->getRequestUri());
            }else{
                $articles = $this->getNews($weather->city);
                Cache::put($request->getRequestUri(), $articles, 60);
            }
            return view('city', compact('weather', 'weatherIconPath','weatherBackgroundImagePath', 'articles', 'is_day', 'dateTime', 'cityDescription', 'articles', 'titleIcons', 'countryName'));
        }else{
            return view('city_not_found');
        }

    }
    public function city(string $city, Request $request){

        $weather = $this->getWeather($city);
        if( !empty($weather->code)){
            $cityDescription = $this->getCityDiscription($weather);


            $weatherManager = new WeatherManager($weather);

            $dateTime = $weatherManager->getTime();
            $is_day = $weatherManager->is_day();
            $titleIcons = $weatherManager->getTitleIcons(app_path('Data/weather_codes.json'));
            $weatherIconPath = $weatherManager->getWeatherImagePath(app_path('Data/weather_codes.json'), 'images/weather/');
            $weatherBackgroundImagePath = $weatherManager->getWeatherImagePath(app_path('Data/weather_codes.json'), 'images/background/');
            $countryName = $weatherManager->getCountryNameByCode(app_path('Data/country_codes.json'));

            if(Cache::has($request->getRequestUri())){
                $articles = Cache::get($request->getRequestUri());
            }else{
                $articles = $this->getNews($weather->city);
                Cache::put($request->getRequestUri(), $articles, 60);
            }

            return view('city', compact('weather', 'weatherIconPath','weatherBackgroundImagePath', 'articles', 'is_day', 'dateTime', 'cityDescription', 'articles', 'titleIcons', 'countryName'));
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

    public function getWeather(string $city): Weather{

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
                    var_dump($Weather);
                return $Weather;
            }

        }catch(Exception $e){
            error_log("Weather API error - ".$e->getMessage(), 0);
            return new Weather();
        }

        return new Weather();
    }

    public function getNews(string $city): array{

        try{

            $NewsApi = new UrlApi(env('NEWS_API_HOST'));

            $NewsApi->addParam('apiKey',env('NEWS_API_KEY'));
            $NewsApi->addParam('q', $city);
            //$NewsApi->addParam('category','general');
            $NewsApi->addParam('language', 'de');
            $NewsApi->addParam('sortBy', 'publishedAt');
            $NewsApi->addParam('pageSize', 12);
            $NewsApi->request();
            $result = $NewsApi->getResult();

            $ArticleManager = New ArticleManager();
            foreach($result['articles'] as $article){
                $ArticleManager->addArticle(new Article(
                    $article['title'],
                    $article['description'],
                    $article['publishedAt'],
                    $article['url'],
                    $article['source']['name'],
                    $article['urlToImage']));
            }
            $ArticleManager->removeDoubleArticles();
            $ArticleManager->setEmptyArticleImages();


            return $ArticleManager->articles;

        }catch(Exception $e){
            error_log("Article API error - ".$e->getMessage(),0);
            return [];
        }
    }

    public function getMediaStack(string $city): array{

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
            error_log("Article API error - ".$e->getMessage(), 0);
            return [];
        }
    }

    public function getCityDiscription(Weather $weather): string{

        $cityDescription = '';

        try{
            $city = City::where('name', $weather->city)->where('latitude', $weather->lat)->where('longitude', $weather->lon)->get();

            if($city->isEmpty()){
                $cityDescription = $this->generateCityDescription($weather->city, $weather->lat, $weather->lon);
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
        }catch(Exception $e){
            error_log('Database error - '.$e->getMessage(), 0);
        }

        return $cityDescription;
    }
    public function generateCityDescription(string $name, float $lat, float $lon): string{
        try{
            $random = rand(1, 2);
            $promptName = 'PROMPT_'.$random;
            $prompt = str_replace(['\'.$name.\'', '\'.$lat.\'', '\'.$lon.\''], [$name, $lat, $lon], env($promptName));

            $result = OpenAI::completions()->create([
                'model' => 'gpt-3.5-turbo-instruct',
                'max_tokens' => 1500,
                'prompt' => $prompt,
            ]);

            return $result['choices'][0]['text'];
        }catch(Exception $e){
            error_log("City description error - ".$e->getMessage(),0);
            return '';
        }
    }
}

<?php
namespace App\classes;
use App\Classes\Weather;
use DateTime;
use Carbon\Carbon;
use App\Traits\DirectoryHelper;
final class WeatherManager{

    use DirectoryHelper;

    private string $time = '';
    private bool $is_day = true;
    private array $icons = [];
    private string $backgroundImage = '';
    private string $pageIcon = '';



    public function __construct(
        public Weather $weather
    ){}

    public function getTime(): string{

        $time = '';
        if(!empty($this->weather->timezone)){
            $time = gmdate("H:i", time() + $this->weather->timezone);
        }

        return $time;

        // $dateTime = new DateTime($this->time);
        // $hour = date('H', $dateTime->getTimestamp());
        // $minute = date('i', $dateTime->getTimestamp());
        // $this->is_day = $this->is_day($hour, $minute);
    }

    public function is_day(): bool{

        $is_day = true;
        $time = $this->getTime();
        if(!empty($time)){
            $time = new DateTime($time);
            $hour = date('H', $time->getTimestamp());
            $minute = date('i', $time->getTimestamp());

            $currentTime = Carbon::createFromTime($hour, $minute);
            $startTime = Carbon::createFromTime(5, 0, 0);
            $endTime = Carbon::createFromTime(22, 0, 0);

            $is_day = ($currentTime->greaterThanOrEqualTo($startTime) && $currentTime->lessThanOrEqualTo($endTime));
        }

        return $is_day;
    }

    public function getTitleIcons(string $weatherCodes): array{

        $json = file_get_contents($weatherCodes);
        $weatherCodes = json_decode($json, true);
        $icons = [];

        if(in_array($this->weather->code, $weatherCodes['clear'])){
            if($this->is_day()){
                $icons[] = '&#9728;';
            }else{
                $icons[] ='&#9790;';
            }
        }
        elseif(in_array($this->weather->code, $weatherCodes["fewClouds"])){
            if($this->is_day()){
                $icons[] = '&#9728;';
                $icons[] = '&#9729;';
            }else{
                $icons[] ='&#9790;';
                $icons[] = '&#9729;';
            }
        }
        elseif(in_array($this->weather->code, $weatherCodes["moderateClouds"])){
            if($this->is_day()){
                $icons[] = '&#9728;';
                $icons[] = '&#9729;';
            }
        }
        elseif(in_array($this->weather->code, $weatherCodes["overcastClouds"])){
            if($this->is_day()){
                $icons[] = '&#9729;';
            }else{
                $icons[] ='&#x1F312;';
                $icons[] = '&#9729;';
            }
        }
        elseif(in_array($this->weather->code, $weatherCodes["moderateRain"])){
            if($this->is_day()){
                $icons[] = '&#9730;';
            }else{
                $icons[] ='&#x1F312;';
                $icons[] = '&#9730;';
            }
        }
        elseif(in_array($this->weather->code, $weatherCodes["heavyRain"])){
            if($this->is_day()){
                $icons[] = '&#9730;';
            }else{
                $icons[] ='&#x1F312;';
                $icons[] = '&#9730;';
            }
        }
        elseif(in_array($this->weather->code, $weatherCodes["thunderstorm"])){
            if($this->is_day()){
                $icons[] = '&#9730;';
                $icons[] = '&#9888;';
            }else{
                $icons[] ='&#x1F312;';
                $icons[] = '&#9888;';
            }

        }
        elseif(in_array($this->weather->code, $weatherCodes["drizzle"])){
            if($this->is_day()){
                $icons[] = '&#9730;';
            }else{
                $icons[] = '&#9730;';
            }
        }
        elseif(in_array($this->weather->code, $weatherCodes["mist"])){
            if($this->is_day()){
                $icons[] = '&#127787;';
            }else{
                $icons[] = '&#127787;';
            }
        }
        else{
            if($this->is_day()){
                $icons[] = '&#9728;';
            }else{
                $icons[] ='&#9790;';
            }
        }

        return $icons;
    }

    public function getWeatherImagePath(string $weatherCodes, string $rootPath): string{

        $json = file_get_contents($weatherCodes);
        if(!$json) return "";

        $weatherCodes = json_decode($json, true);
        $folderPath = '';

        if(in_array($this->weather->code, $weatherCodes['clear'])){
            $folderPath = $rootPath.'clear/';
        }
        elseif(in_array($this->weather->code, $weatherCodes["fewClouds"])){
            $folderPath = $rootPath.'few_clouds/';
        }
        elseif(in_array($this->weather->code, $weatherCodes["moderateClouds"])){
            $folderPath = $rootPath.'few_clouds/';
        }
        elseif(in_array($this->weather->code, $weatherCodes["overcastClouds"])){
            $folderPath = $rootPath.'overcast_clouds/';
        }
        elseif(in_array($this->weather->code, $weatherCodes["moderateRain"])){
            $folderPath = $rootPath.'moderate_rain/';
        }
        elseif(in_array($this->weather->code, $weatherCodes["heavyRain"])){
            $folderPath = $rootPath.'heavy_rain/';
        }
        elseif(in_array($this->weather->code, $weatherCodes["thunderstorm"])){
            $folderPath = $rootPath.'thunderstorm/';
        }
        elseif(in_array($this->weather->code, $weatherCodes["drizzle"])){
            $folderPath = $rootPath.'drizzle/';
        }
        elseif(in_array($this->weather->code, $weatherCodes["mist"])){
            $folderPath = $rootPath.'mist/';
        }
        else{
            $folderPath = $rootPath.'clear/';
        }

        if( $this->is_day()){
            $filePath = $this->getRandomFilePath($this->getPublicFilePaths($folderPath.'day/'));
        }else{
            $filePath = $this->getRandomFilePath($this->getPublicFilePaths($folderPath.'night/'));
        }

        return $filePath;
    }

    public function getCountryNameByCode(string $countryCodes): string {

        $json = file_get_contents($countryCodes);
        if(!$json) return "";

        $countryCodes = json_decode($json, true);

        return array_key_exists(strtoupper($this->weather->country), $countryCodes) ? $countryCodes[strtoupper($this->weather->country)] : "";
    }
}
?>

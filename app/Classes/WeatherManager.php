<?php

declare(strict_types=1);

namespace App\classes;

use App\Classes\Weather;
use DateTime;
use Carbon\Carbon;
use App\Traits\DirectoryHelper;

final class WeatherManager
{
    use DirectoryHelper;

    private string $time = '';
    private bool $is_day = true;
    private array $icons = [];
    private string $backgroundImage = '';
    private string $pageIcon = '';



    public function __construct(
        public Weather $weather
    ){}

    public function getTime(): string
    {
        $time = '';
        if (!empty($this->weather->timezone)) {
            $time = gmdate("H:i", time() + $this->weather->timezone);
        }

        return $time;

        // $dateTime = new DateTime($this->time);
        // $hour = date('H', $dateTime->getTimestamp());
        // $minute = date('i', $dateTime->getTimestamp());
        // $this->is_day = $this->is_day($hour, $minute);
    }

    public function is_day(): bool
    {
        $is_day = true;
        $time = $this->getTime();

        if (!empty($time)) {
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

    public function getTitleIcons(string $weatherCodes): array
    {
        $json = file_get_contents($weatherCodes);
        $weatherCodes = json_decode($json, true);
        $icons = [];

        if (in_array($this->weather->code, $weatherCodes['clear'])) {

            $icons[] = $this->is_day() ? '&#9728;' : '&#9790;';

        } elseif (in_array($this->weather->code, $weatherCodes["fewClouds"])) {

            if ($this->is_day()) {
                $icons[] = '&#9728;';
                $icons[] = '&#9729;';
            } else {
                $icons[] ='&#9790;';
                $icons[] = '&#9729;';
            }

        } elseif(in_array($this->weather->code, $weatherCodes["moderateClouds"])) {

            if ($this->is_day()) {
                $icons[] = '&#9728;';
                $icons[] = '&#9729;';
            }

        } elseif(in_array($this->weather->code, $weatherCodes["overcastClouds"])) {

            if ($this->is_day()) {
                $icons[] = '&#9729;';
            } else {
                $icons[] ='&#x1F312;';
                $icons[] = '&#9729;';
            }
        } elseif (in_array($this->weather->code, $weatherCodes["moderateRain"])) {

            if ($this->is_day()) {
                $icons[] = '&#9730;';
            } else {
                $icons[] ='&#x1F312;';
                $icons[] = '&#9730;';
            }

        } elseif (in_array($this->weather->code, $weatherCodes["heavyRain"])) {

            if ($this->is_day()) {
                $icons[] = '&#9730;';
            } else {
                $icons[] ='&#x1F312;';
                $icons[] = '&#9730;';
            }

        } elseif (in_array($this->weather->code, $weatherCodes["thunderstorm"])) {

            if ($this->is_day()) {
                $icons[] = '&#9730;';
                $icons[] = '&#9888;';
            } else {
                $icons[] ='&#x1F312;';
                $icons[] = '&#9888;';
            }

        } elseif (in_array($this->weather->code, $weatherCodes["drizzle"])) {

            $icons[] = $this->is_day() ? '&#9730;' : '&#9730;';

        } elseif(in_array($this->weather->code, $weatherCodes["mist"])) {

            $icons[] = $this->is_day() ? '&#127787;' : '&#127787;';

        } else{

            $icons[] = $this->is_day() ? '&#9728;' : '&#9790;';
        }

        return $icons;
    }

    public function getWeatherImagePath(string $weatherCodes, string $rootPath): string
    {
        $json = file_get_contents($weatherCodes);
        if (!$json) return "";

        $weatherCodes = json_decode($json, true);
        $folderPath = '';

        $map = [
            'clear'             => 'clear/',
            'fewClouds'         => 'few_clouds/',
            'moderateClouds'    => 'few_clouds/',
            'overcastClouds'    => 'overcast_clouds/',
            'moderateRain'      => 'moderate_rain/',
            'heavyRain'         => 'heavy_rain/',
            'thunderstorm'      => 'thunderstorm/',
            'drizzle'           => 'drizzle/',
            'mist'              => 'mist/',
        ];

        $folderPath = $rootPath . 'clear/';

        foreach ($map as $key => $folder) {
            if (in_array($this->weather->code, $weatherCodes[$key], true)) {
                $folderPath = $rootPath . $folder;
                break;
            }
        }

        $filePath = $this->is_day()
            ? $this->getRandomFilePath($this->getPublicFilePaths($folderPath.'day/'))
            : $this->getRandomFilePath($this->getPublicFilePaths($folderPath.'night/'));


        return $filePath;
    }

    public function getCountryNameByCode(string $countryCodes): string
    {
        $json = file_get_contents($countryCodes);
        if (!$json) return "";

        $countryCodes = json_decode($json, true);

        return array_key_exists(strtoupper($this->weather->country), $countryCodes) ? $countryCodes[strtoupper($this->weather->country)] : "";
    }
}
?>

<?php
namespace App\Classes;
class Weather{

    public function __construct(
        public string $temperature,
        public string $windSpeed,
        public string $description,
        public string $icon,
        public string $name,
        public string $lat,
        public string $lon,
        public string $timezone,
        public string $country,
    ){}
}

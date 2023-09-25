<?php
namespace App\Classes;
class Weather{

    public function __construct(
        public string $code = '',
        public string $country = '',
        public string $city = '',
        public string $lat = '',
        public string $lon = '',
        public string $description = '',
        public string $temperature = '',
        public string $windSpeed = '',
        public int $timezone = 0
    ){}
}

<?php
namespace App\classes;
use App\Classes\Api;

class OpenWeatherMap implements Api{

    private array $queries = [];
    private array|null $result = [];

    public function setKey(string $key): void{

        $this->queries['appid'] = $key;
    }

    public function addParam(string $key, string $value): void{

        $this->queries[$key] = $value;
    }

    public function request(): void{

        $httpQuery = http_build_query($this->queries);
        $curl = curl_init(sprintf('%s?%s', 'https://api.openweathermap.org/data/2.5/weather', $httpQuery));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'User-Agent: ' . 'localhost', // Setzen Sie den User-Agent-Header
        ]);
        $json = curl_exec($curl);
        curl_close($curl);

        $this->result = json_decode($json, true);
    }

    public function getResult(): array{

        return $this->result;
    }
}

?>

<?php
namespace App\classes;
use App\Classes\Api;
use Exception;
final class UrlApi implements Api{

    private string $host = '';
    private array $queries = [];
    private array $result = [];
    private array $substitutedArticleImage = [];

    public function __construct(string $host){

        $this->host = $host;
    }
    public function addParam(string $key, string $value): void{

        $this->queries[$key] = $value;
    }

    public function request(): void{

        try{

            $httpQuery = http_build_query($this->queries);
            $curl = curl_init(sprintf('%s?%s', $this->host, $httpQuery));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'User-Agent: ' . 'localhost',
            ]);
            $json = curl_exec($curl);
            curl_close($curl);

            $this->result = json_decode($json, true);

        }catch(Exception $e){

            $this->result = [];
        }
    }

    public function getResult(): array{

        return $this->result;
    }
}

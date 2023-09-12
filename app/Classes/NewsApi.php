<?php
namespace App\classes;
use App\Classes\Api;

class NewsApi implements Api{

    private array $queries = [];
    private array $articles = [];
    public $substitutedArticleImage = [];

    public function setKey(string $key): void{

        $this->queries['apiKey'] = $key;
    }

    public function addQuery(string $key, string $value): void{

        $this->queries[$key] = $value;
    }

    public function request(): void{

       $httpQuery = http_build_query($this->queries);
        $curl = curl_init(sprintf('%s?%s', 'https://newsapi.org/v2/everything', $httpQuery));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'User-Agent: ' . 'localhost', // Setzen Sie den User-Agent-Header
        ]);
        $json = curl_exec($curl);
        curl_close($curl);

        $apiResult = json_decode($json, true);
        foreach($apiResult['data'] as $article){
            $this->articles[] = new Article($article['title'], $article['description'], $article['published_at'],$article['url'], $article['author'], $article['image']);
        }
    }

    public function removeDoubleArticles(){

        $searchForSameArticles = $this->articles;
        for($i = 0; $i < count($searchForSameArticles)-1; $i++){
            for($j = $i + 1; $j < count($searchForSameArticles); $j++){
                if($searchForSameArticles[$i]->imagePath == $searchForSameArticles[$j]->imagePath || $searchForSameArticles[$i]->title == $searchForSameArticles[$j]->title){
                    unset($this->articles[$j]);
                }
            }
        }

        array_values($this->articles);
    }

    public function substituteArticleImage(&$article) {

        if(empty($article->imagePath)){
            $pathToImage = $this->getRandomFilePath($this->getFilePaths('images/article_substitute/'));
            if(!in_array($pathToImage, $this->substitutedArticleImage)){
                $article->imagePath = $pathToImage;
                $this->substitutedArticleImage[] = $pathToImage;
            }else{
                $this->substituteArticleImage($article);
            }
        }
    }

    public function setEmptyArticleImages(){

        foreach($this->articles as $article){
            $this->substituteArticleImage($article);
        }
    }

    public function getFilePaths(string $dir) {

        $filepaths = array();

        if (is_dir($dir) && $handle = opendir($dir)) {

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && !is_dir($dir.$file)) {
                    $filepaths[] = $dir.$file;
                }
            }
            closedir($handle);
        }

        return $filepaths;
    }

    public function getRandomFilePath(array $filepaths) {

        $count = count($filepaths);

        $randomIndex = rand(0, $count - 1);

        return $filepaths[$randomIndex];
    }
    public function getArticles(): array{

        return $this->articles;
    }
}

?>

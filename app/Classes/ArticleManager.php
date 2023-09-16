<?php
namespace App\classes;
use App\Traits;
use App\Traits\DirectoryHelper;
final class ArticleManager{

    use DirectoryHelper;
    public $articles = [];
    private $substitutedArticleImage = [];

    public function addArticle(Article $article){

        $this->articles[] = $article;
    }

    public function removeDoubleArticles(){

        $searchForSameArticles = $this->articles;
        for($i = 0; $i < count($searchForSameArticles)-1; $i++){
            for($j = $i + 1; $j < count($searchForSameArticles); $j++){
                if($searchForSameArticles[$i]->imagePath == $searchForSameArticles[$j]->imagePath || $searchForSameArticles[$i]->title == $searchForSameArticles[$j]->title || $searchForSameArticles[$i]->description == $searchForSameArticles[$j]->description){
                    unset($this->articles[$j]);
                }
            }
        }

        array_values($this->articles);
    }

    public function substituteArticleImage(&$article) {

        if(empty($article->imagePath)){
            $pathToImage = $this->getRandomFilePath($this->getPublicFilePaths('images/article_substitute/'));
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

    public function maxDescriptionLength(int $length): void{

        foreach ($this->articles as $article) {
            if ($article->description !== null && strlen($article->description) > $length) {
                $article->description = substr($article->description, 0, $length);
            }
        }
        // $a = array_map(function($article) use ($length){
        //     return $article->description = substr($article->description, 0, $length);
        // }, $this->articles);
        // foreach($this->articles as $article){
        //     var_dump($article->description);
        // }
    }
}
?>

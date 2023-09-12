<?php
namespace App\Classes;
final class Article {

    public function __construct(
        public string $title,
        public string | null $description,
        public string $publishedAt,
        public string $url,
        public string | null $sourceName,
        public string | null $imagePath,
    ){}
}

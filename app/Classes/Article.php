<?php

declare(strict_types=1);

namespace App\Classes;
final class Article {

    public function __construct(
        public string | null $title,
        public string | null $description,
        public string | null $publishedAt,
        public string | null $url,
        public string | null $sourceName,
        public string | null $imagePath,
    ){}
}

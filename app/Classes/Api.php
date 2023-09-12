<?php
namespace App\Classes;
interface Api {
    public function addParam(string $key, string $value): void;
    public function request(): void;
}
?>

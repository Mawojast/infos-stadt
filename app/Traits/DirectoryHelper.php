<?php
namespace App\Traits;

trait DirectoryHelper{

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
}

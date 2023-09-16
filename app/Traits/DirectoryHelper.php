<?php
namespace App\Traits;

trait DirectoryHelper{

    public function getPublicFilePaths(string $dir, string $addPath = ''): array {

        $filepaths = array();
        $absolutePath = public_path($dir);

        if (is_dir($absolutePath) && $handle = opendir($absolutePath)) {

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && !is_dir($absolutePath.$file)) {
                    $filepaths[] = $addPath.$dir.$file;
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

<?php


namespace App\Service;



class Slugify
{
    public function generate(string $input) : string
    {
        $slug = preg_replace('~[^\pL\d]+~u', '-', $input);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = strtolower($slug);
        if (empty($slug)) {
            return 'n-a';
        }
        return $slug;
    }
}

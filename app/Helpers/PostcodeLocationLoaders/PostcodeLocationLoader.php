<?php

namespace App\Helpers\PostcodeLocationLoaders;

interface PostcodeLocationLoader
{
    /**
     * @return array{
     *     postcode: string,
     *     latitude: numeric|numeric-string,
     *     longitude: numeric|numeric-string
     * }[]
     */
    public function read(int $rows = 1): array;
}

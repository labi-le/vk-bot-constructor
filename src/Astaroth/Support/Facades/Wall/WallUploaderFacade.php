<?php

declare(strict_types=1);

namespace Astaroth\Support\Facades\Wall;


use Astaroth\VkUtils\Contracts\IDocsUpload;
use Astaroth\VkUtils\Contracts\IPhoto;
use Astaroth\VkUtils\Contracts\IStories;
use Astaroth\VkUtils\Contracts\IVideo;

/**
 * Class WallUploaderFacade
 * @package Astaroth\Support\Facades\Wall
 */
class WallUploaderFacade extends \Astaroth\Support\Facades\Facade
{
    public static function upload(IDocsUpload|IVideo|IPhoto|IStories ...$object): array
    {
        return static::getObject("wall.uploader")?->upload(...$object);
    }
}
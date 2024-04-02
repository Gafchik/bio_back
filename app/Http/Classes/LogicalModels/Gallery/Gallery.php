<?php

namespace App\Http\Classes\LogicalModels\Gallery;

use App\Http\Classes\LogicalModels\Gallery\Exceptions\AlbumsNotFoundException;

class Gallery
{
    public function __construct(
        private GalleryModel $model
    ){}

    public function getAlbums(): array
    {
        return $this->model->getAlbums();
    }
    public function getAlbumsDetails(array $data): array
    {
        $result = $this->model->getAlbumsDetails($data);
        if(!$result){
            throw  new AlbumsNotFoundException();
        }
        return $result;
    }
}

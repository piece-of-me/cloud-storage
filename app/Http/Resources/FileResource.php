<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'typeId' => $this->type_id,
            'size' => $this->size,
            'path' => $this->path,
            'public' => $this->public_hash !== null,
            'parentId' => $this->parent_id,
            'views' => $this->views,
            'downloads' => $this->downloads,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
        if (isset($this->content)) {
            $data['content'] = $this->_mapFolders($this->content->toArray());
        }
        return $data;
    }

    private function _mapFolders($folder): array
    {
        return array_map(function ($item) {
            $result = [
                'id' => $item['id'],
                'name' => $item['name'],
                'typeId' => $item['type_id'],
                'size' => $item['size'],
                'path' => $item['path'],
                'parentId' => $item['parent_id'],
                'views' => $item['views'],
                'downloads' => $item['downloads'],
                'createdAt' => $item['created_at'],
                'updatedAt' => $item['updated_at'],
            ];
            if (isset($item['content'])) {
                $result['content'] = $this->_mapFolders($item['content']->toArray());
            }
            return $result;
        }, $folder);
    }
}

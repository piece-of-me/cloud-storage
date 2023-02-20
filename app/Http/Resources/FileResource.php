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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'typeId' => $this->type_id,
            'size' => $this->_formatBytes($this->size),
            'path' => $this->path,
            'public' => $this->public_hash !== null,
            'parentId' => $this->parent_id,
            'views' => $this->views,
            'downloads' => $this->downloads,
            'createdAt' => (new \DateTime($this->created_at))->format('d.m.Y H:i'),
            'updatedAt' => (new \DateTime($this->updated_at))->format('d.m.Y H:i'),
        ];
    }

    private function _formatBytes(int $bytes): string
    {
        if ($bytes == 0) return '0 Bytes';
        $oneKB = 1024;
        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($oneKB));

        return floor($bytes / pow($oneKB, $i)) . ' ' . $units[$i];
    }
}

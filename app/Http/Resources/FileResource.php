<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'sizeStr' => $this->_formatBytes($this->size),
            'size' => $this->size,
            'extension' => $this->extension,
            'path' => Storage::disk('public')->url($this->path),
            'public' => $this->public_hash !== null,
            'publicPath' => $request->schemeAndHttpHost() . '/public/' . $this->public_hash,
            'parentId' => $this->parent_id,
            'views' => $this->views,
            'downloads' => $this->downloads,
            'createdAt' => (new \DateTime($this->created_at))->format('d.m.Y H:i'),
            'updatedAt' => (new \DateTime($this->updated_at))->format('d.m.Y H:i'),
            'updatedAtJSFormat' => (new \DateTime($this->updated_at))->format('Y-m-d\TH:i:s'),
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

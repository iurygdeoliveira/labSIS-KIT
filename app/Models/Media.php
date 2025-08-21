<?php

declare(strict_types=1);

namespace App\Models;

use App\Trait\UuidTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'uuid',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'size',
        'manipulations',
        'custom_properties',
        'order_column',
    ];

    protected $casts = [
        'manipulations' => 'array',
        'custom_properties' => 'array',
        'size' => 'integer',
        'order_column' => 'integer',
    ];

    protected $appends = [
        'url',
        'human_readable_size',
        'extension',
        'is_image',
        'is_video',
        'is_pdf',
        'is_document',
    ];

    /**
     * Relacionamento polimórfico com o modelo que possui a mídia
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope para filtrar por coleção
     */
    public function scopeInCollection(Builder $query, string $collection): Builder
    {
        return $query->where('collection_name', $collection);
    }

    /**
     * Scope para filtrar apenas imagens
     */
    public function scopeImages(Builder $query): Builder
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope para filtrar apenas vídeos
     */
    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    /**
     * Scope para filtrar apenas PDFs
     */
    public function scopePdfs(Builder $query): Builder
    {
        return $query->where('mime_type', '=', 'application/pdf');
    }

    /**
     * Scope para filtrar apenas documentos
     */
    public function scopeDocuments(Builder $query): Builder
    {
        return $query->where('mime_type', 'like', 'application/%');
    }

    /**
     * Scope para ordenar por ordem
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_column');
    }

    /**
     * Obter a URL completa do arquivo
     */
    public function getUrlAttribute(): string
    {
        return $this->buildFileUrl($this->file_name, $this->disk);
    }

    /**
     * Método helper para construir URLs de arquivos
     */
    private function buildFileUrl(string $path, string $disk): string
    {
        // Para disco público, usa o método url() da facade Storage
        if ($disk === 'public') {
            return Storage::url($path);
        }

        // Para disco local, constrói a URL manualmente baseado na configuração
        if ($disk === 'local') {
            $baseUrl = config('filesystems.disks.local.url', '/storage');

            return $baseUrl.'/'.$path;
        }

        // Fallback para outros discos
        return '/storage/'.$path;
    }

    /**
     * Obter o caminho completo do arquivo
     */
    public function getPathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->file_name);
    }

    /**
     * Obter o tamanho em formato legível
     */
    public function getHumanReadableSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2).' '.$units[$unit];
    }

    /**
     * Obter a extensão do arquivo
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Verificar se é uma imagem
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Verificar se é um vídeo
     */
    public function getIsVideoAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * Verificar se é um PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Verificar se é um documento
     */
    public function getIsDocumentAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'application/');
    }

    /**
     * Verificar se o arquivo existe no disco
     */
    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->file_name);
    }

    /**
     * Obter propriedade customizada
     */
    public function getCustomProperty(string $key, $default = null)
    {
        return data_get($this->custom_properties, $key, $default);
    }

    /**
     * Definir propriedade customizada
     */
    public function setCustomProperty(string $key, $value): void
    {
        $properties = $this->custom_properties ?? [];
        $properties[$key] = $value;
        $this->custom_properties = $properties;
        $this->save();
    }

    /**
     * Obter metadados básicos do arquivo
     */
    public function getMetadata(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'human_readable_size' => $this->human_readable_size,
            'extension' => $this->extension,
            'disk' => $this->disk,
            'collection_name' => $this->collection_name,
            'is_image' => $this->is_image,
            'is_video' => $this->is_video,
            'is_pdf' => $this->is_pdf,
            'is_document' => $this->is_document,
            'url' => $this->url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Deletar arquivo físico ao deletar registro
     */
    protected static function booted(): void
    {
        static::deleting(function ($media) {
            // Deletar arquivo original
            if ($media->exists()) {
                Storage::disk($media->disk)->delete($media->file_name);
            }
        });
    }
}

<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Enums\MediaAcceptedMime;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class MediaForm
{
    /*
     * Retorna o script JS executado após a atualização do estado do upload de mídia.
     * Objetivo: refletir imediatamente na UI a exclusividade com o campo de vídeo,
     * limpando a URL, emitindo eventos e desabilitando o TextInput de vídeo sem roundtrip.
     */
    private static function jsOnMediaUpdated(): string
    {
        return <<<'JS'
            const hasFile = Array.isArray($state) ? $state.length > 0 : Boolean($state);
            
            // Atualiza front-end do campo de video
            try {
                const videoEl = document.getElementById('videoUrl');
                if (videoEl) {
                    videoEl.toggleAttribute('disabled', hasFile);
                }
            } catch (e) {}
            
            if (hasFile) {
                // Gera slug do nome do arquivo
                const file = Array.isArray($state) ? $state[0] : $state;
                if (file && file.name) {
                    const fileName = file.name;
                    const nameWithoutExtension = fileName.replace(/\.[^/.]+$/, "");
                    const slug = nameWithoutExtension
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                    $set('name', slug);
                }
                
                $set('video.url', null);
                window.dispatchEvent(new CustomEvent('video-toggled', { detail: { hasUrl: false, url: null } }));
            }
            window.dispatchEvent(new CustomEvent('media-toggled', { detail: { hasMedia: hasFile } }));
        JS;
    }

    /*
     * Retorna o script JS executado após a atualização do estado da URL de vídeo.
     * Objetivo: refletir imediatamente na UI a exclusividade com o uploader,
     * limpando o campo de mídia, emitindo eventos e desabilitando input file e botões.
     */
    private static function jsOnVideoUrlUpdated(): string
    {
        return <<<'JS'
            const sanitized = String($state ?? '').trim().replace(/^@+/, '');
            const hasUrl = Boolean(sanitized);
            // Desabilita/enabilita uploader imediatamente no front-end
            try {
                const mediaEl = document.getElementById('mediaUpload');
                if (mediaEl) {
                    const fileInput = mediaEl.querySelector('input[type=file]');
                    const clickableEls = mediaEl.querySelectorAll('button, [role=button]');
                    if (fileInput) fileInput.disabled = hasUrl;
                    clickableEls.forEach((el) => {
                        el.classList.toggle('pointer-events-none', hasUrl);
                        el.setAttribute('aria-disabled', hasUrl ? 'true' : 'false');
                    });
                    mediaEl.classList.toggle('opacity-50', hasUrl);
                }
            } catch (e) {}
            if (sanitized !== $state) { $set('video.url', sanitized); }
            if (hasUrl) { $set('media', null); }
            // metadados serão lidos do anexo do Spatie no servidor
            window.dispatchEvent(new CustomEvent('video-toggled', { detail: { hasUrl, url: sanitized } }));
        JS;
    }

    /*
     * Manipula a atualização de estado do upload no servidor.
     * Objetivo: preencher 'name' com slug do nome do arquivo e limpar 'video_url';
     * quando o arquivo for removido, limpar 'name'.
     */
    private static function handleMediaStateUpdated(mixed $state, Set $set): void
    {
        if ($state) {
            // Se multiple=false, $state é um único arquivo
            $file = is_array($state) ? ($state[0] ?? null) : $state;

            if ($file && method_exists($file, 'getClientOriginalName')) {
                $fileName = $file->getClientOriginalName();
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);

                // Converte para slug
                $slug = \Illuminate\Support\Str::slug($nameWithoutExtension);
                $set('name', $slug);

                // metadados serão lidos do anexo do Spatie no servidor
            }

            // Quando um arquivo é selecionado, limpamos a URL de vídeo
            $set('video.url', null);

            return;
        }

        // Quando o arquivo é removido, limpa o nome também
        $set('name', null);
        // metadados serão lidos do anexo do Spatie no servidor
    }

    /*
     * Define e configura o schema do formulário de Mídia (Filament 4).
     * Objetivo: montar os campos e suas regras de exclusividade entre upload e URL de vídeo.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Upload de Arquivos')
                    ->description('Faça upload de imagens, áudios e documentos (até 5MB)')
                    ->visible(fn ($record): bool => $record === null || ! (bool) ($record?->video ?? false))
                    ->components([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->hiddenLabel()
                            ->id('mediaUpload')
                            ->multiple(false)
                            ->reorderable(false)
                            ->appendFiles(false)
                            ->disk('s3')
                            ->visibility('public')
                            ->dehydrated(false)
                            ->downloadable(true)
                            ->openable(true)
                            ->previewable(true)
                            ->acceptedFileTypes(MediaAcceptedMime::defaults())
                            ->maxFiles(1)
                            ->maxSize(5120) // 5MB
                            ->collection('media')
                            ->storeFileNamesIn('name')
                            ->required(fn (Get $get): bool => empty($get('video.url')))
                            ->disabled(fn ($get) => ! empty($get('video.url')))
                            ->responsiveImages()
                            ->customProperties([
                                'uploaded_at' => now(),
                            ])
                            ->afterStateUpdatedJs(self::jsOnMediaUpdated())
                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                self::handleMediaStateUpdated($state, $set);
                            })
                            ->columnSpanFull(),

                        TextInput::make('attachment_name')
                            ->label('Nome do Arquivo')
                            ->afterStateHydrated(function (Set $set, $state, $record): void {
                                if ($state !== null) {
                                    return;
                                }

                                $existing = $record?->getFirstMedia('media')?->name;
                                if ($existing) {
                                    $set('attachment_name', (string) $existing);
                                }
                            })
                            ->visible(fn ($record): bool => ! (bool) ($record?->video ?? false))
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('URL de Vídeo')
                    ->description('Informe a URL do vídeo (YouTube, Vimeo, etc.)')
                    ->visible(fn ($record): bool => $record === null || (bool) ($record?->video ?? false))
                    ->components([
                        TextInput::make('video.url')
                            ->hiddenLabel()
                            ->id('videoUrl')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->afterStateHydrated(function (Set $set, ?string $state, $record): void {
                                // Na edição, popula a URL do vídeo a partir do relacionamento
                                // quando o estado ainda não foi definido pelo formulário.
                                if ($state !== null) {
                                    return;
                                }

                                $existing = $record?->video()?->value('url');
                                if ($existing) {
                                    $set('video.url', (string) $existing);
                                }
                            })
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if ($state === null) {
                                    return;
                                }

                                $clean = ltrim(trim($state), '@');

                                if ($clean !== $state) {
                                    $set('video.url', $clean);
                                }
                            })
                            ->disabled(fn ($get) => ! empty($get('media')))
                            ->hint(fn (Get $get): ?string => ! empty($get('media')) ? 'Um arquivo foi selecionado. Remova-o para informar uma URL de vídeo.' : null)
                            ->hintColor('danger')
                            ->afterStateUpdatedJs(self::jsOnVideoUrlUpdated())
                            ->extraInputAttributes([
                                'x-data' => '{ isDisabled: false }',
                                'x-on:media-toggled.window' => 'isDisabled = $event.detail.hasMedia',
                                'x-bind:disabled' => 'isDisabled',
                            ])
                            ->columnSpanFull(),

                        TextInput::make('video_title')
                            ->label('Título do Vídeo')
                            ->afterStateHydrated(function (Set $set, $state, $record): void {
                                if ($state !== null) {
                                    return;
                                }

                                $existing = $record?->video()?->value('title');
                                if ($existing) {
                                    $set('video_title', (string) $existing);
                                }
                            })
                            ->visible(fn ($record): bool => (bool) ($record?->video ?? false))
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        ViewField::make('video_preview')
                            ->view('filament.forms.components.video-preview')
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

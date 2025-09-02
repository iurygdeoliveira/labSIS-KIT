<?php

namespace App\Filament\Resources\Media\Schemas;

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
                $set('video_url', null);
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
            const hasUrl = Boolean($state);
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
            if (hasUrl) { $set('media', null); }
            window.dispatchEvent(new CustomEvent('video-toggled', { detail: { hasUrl, url: $state } }));
        JS;
    }

    /*
     * Manipula a atualização de estado do upload no servidor.
     * Objetivo: preencher 'name' com o nome do arquivo e limpar 'video_url';
     * quando o arquivo for removido, limpar 'name'.
     */
    private static function handleMediaStateUpdated(mixed $state, Set $set): void
    {
        if ($state) {
            // Se multiple=false, $state é um único arquivo
            $file = is_array($state) ? ($state[0] ?? null) : $state;

            if ($file && method_exists($file, 'getClientOriginalName')) {
                $fileName = $file->getClientOriginalName();
                $set('name', pathinfo($fileName, PATHINFO_FILENAME));
            }

            // Limpa o campo de vídeo quando um arquivo é selecionado
            $set('video_url', null);

            return;
        }

        // Quando o arquivo é removido, limpa o nome também
        $set('name', null);
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
                    ->description('Faça upload de imagens, áudios e documentos')
                    ->components([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->hiddenLabel()
                            ->id('mediaUpload')
                            ->multiple(false)
                            ->reorderable(false)
                            ->appendFiles(false)
                            ->acceptedFileTypes([
                                'image/*',
                                'audio/*',
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/plain',
                            ])
                            ->maxFiles(1)
                            ->maxSize(5120) // 5MB
                            ->collection('media')
                            ->preserveFilenames()
                            ->required()
                            ->disabled(fn ($get) => ! empty($get('video_url')))
                            ->responsiveImages()
                            ->customProperties([
                                'uploaded_at' => now(),
                            ])
                            ->afterStateUpdatedJs(self::jsOnMediaUpdated())
                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                self::handleMediaStateUpdated($state, $set);
                            })
                            ->partiallyRenderComponentsAfterStateUpdated(['video_url'])
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nome do Arquivo')
                            ->required()
                            ->maxLength(255)
                            ->hidden(fn ($get) => empty($get('media')) && empty($get('video_url')))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('URL de Vídeo')
                    ->description('Informe a URL do vídeo (YouTube, Vimeo, etc.)')
                    ->components([
                        TextInput::make('video_url')
                            ->hiddenLabel()
                            ->id('videoUrl')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
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

                        ViewField::make('video_preview')
                            ->view('filament.forms.components.video-preview')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}

## MediaForm (Filament 4): Exclusividade entre Upload e URL de Vídeo, Reatividade no Front-end e Preview

### Objetivo
- **Exclusividade**: o usuário pode enviar um arquivo OU informar uma URL de vídeo, nunca ambos ao mesmo tempo.
- **Reatividade sem roundtrip**: mudanças são refletidas imediatamente no front-end usando `afterStateUpdatedJs()`, eventos e Alpine, evitando `->live()`.
- **Preview**: exibe uma pré-visualização de vídeos do YouTube/Vimeo em tempo real conforme a URL é digitada.

### Componentes envolvidos
- `SpatieMediaLibraryFileUpload::make('media')`
- `TextInput::make('video_url')`
- `ViewField::make('video_preview')` + Blade `resources/views/filament/forms/components/video-preview.blade.php`

### Regras de exclusividade (Back-end)
- Ambos os campos possuem `->disabled(fn ($get) => ...)` para garantir a regra no servidor mesmo sem JavaScript.
  - `media` desabilita quando `video_url` está preenchido.
  - `video_url` desabilita quando `media` está preenchido.

### Reatividade (Front-end, sem `->live()`)
- Foram usados `->afterStateUpdatedJs()` em ambos os campos para sincronizar estados no front-end:
  - Ao selecionar arquivo: limpa `video_url`, desabilita o `TextInput` no DOM e emite o evento `media-toggled` (e `video-toggled` com URL nula).
  - Ao digitar URL: limpa `media`, desabilita o `input[type=file]` e botões do uploader e emite `video-toggled`.
- Eventos globais disparados:
  - `media-toggled` com `{ hasMedia: boolean }`.
  - `video-toggled` com `{ hasUrl: boolean, url: string | null }`.

### Hints (mensagens de ajuda)
- `video_url` exibe um hint de cor `danger` quando existe mídia anexada:
  - `->hint(fn (Get $get) => !empty($get('media')) ? 'Um arquivo foi selecionado...' : null)`
  - `->hintColor('danger')`
- Para o hint desaparecer imediatamente ao limpar o upload, o uploader usa `->partiallyRenderComponentsAfterStateUpdated(['video_url'])`.

### Preview de Vídeo (YouTube/Vimeo)
- O `ViewField::make('video_preview')` renderiza o Blade `video-preview.blade.php`.
- A view usa Alpine para:
  - Inicializar `url` com `$get('video_url')`.
  - Ouvir `video-toggled` e atualizar `url` instantaneamente.
  - Calcular `embedUrl` (YouTube/Vimeo) no cliente e renderizar um `<iframe>`.

### Funções extraídas (PHP)
- `handleMediaStateUpdated(mixed $state, Set $set): void`
  - Preenche `name` com o nome do arquivo quando há upload e limpa `video_url`.
  - Ao remover o arquivo, limpa `name`.
- `jsOnMediaUpdated(): string`
  - JS que reflete no DOM a seleção/remoção de arquivo, desabilitando `video_url` e emitindo eventos.
- `jsOnVideoUrlUpdated(): string`
  - JS que reflete no DOM a digitação/remoção de URL, desabilitando o uploader e emitindo eventos.

### Trechos de código (resumo)

```php
// Em App\Filament\Resources\Media\Schemas\MediaForm

TextInput::make('video_url')
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
    ]);

SpatieMediaLibraryFileUpload::make('media')
    ->id('mediaUpload')
    ->disabled(fn ($get) => ! empty($get('video_url')))
    ->afterStateUpdatedJs(self::jsOnMediaUpdated())
    ->afterStateUpdated(function (mixed $state, Set $set): void {
        self::handleMediaStateUpdated($state, $set);
    })
    ->partiallyRenderComponentsAfterStateUpdated(['video_url']);
```

### Decisões de implementação
- Evitamos `->live()` para não re-renderizar a cada tecla e reduzir roundtrips.
- Mantivemos `->disabled(...)` no servidor por segurança e consistência.
- Para UX imediata, manipulamos atributos no DOM (via JS) e emitimos eventos globais.
- Preview de vídeo reativo feito inteiramente no cliente.

### Manutenção / Extensões
- Para suportar novas plataformas de vídeo, estenda a lógica de `embedUrl` em `video-preview.blade.php`.
- Para bloquear completamente cliques no uploader, adicione um overlay absoluto quando `hasUrl === true`.
- Mensagens do hint podem ser ajustadas diretamente em `MediaForm`.




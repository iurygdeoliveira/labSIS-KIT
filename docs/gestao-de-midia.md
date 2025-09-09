## Gestão de mídias privadas e URLs temporárias assinadas

### Por que buckets privados e URLs pré-assinadas?
Manter buckets privados segue o princípio do menor privilégio e evita exposição acidental. Em vez de objetos públicos, usamos URLs pré‑assinadas e temporárias para liberar somente o arquivo necessário, pelo tempo necessário, sem expor credenciais. É possível restringir método (GET/PUT), tipo e tamanho do arquivo; qualquer alteração na URL invalida o acesso (assinatura SigV4). Isso reduz riscos como indexação, hotlink e brute force, diminui a superfície de ataque e favorece a conformidade (ex.: LGPD).

### Proposta adotada neste projeto
Neste projeto, o bucket MinIO `labsis` permanece privado e sem acesso anônimo. A assinatura de URLs é automática por meio de um gerador customizado do Spatie que sobrescreve `getUrl()` para sempre retornar `temporaryUrl` com SigV4. Os uploads realizados via Filament são gravados no disco `s3` com visibilidade privada. Evitamos qualquer reescrita do host das URLs após a assinatura para impedir erros de `SignatureDoesNotMatch`. Na interface, a abertura de mídia é exibida somente para tipos suportados (imagem, áudio, vídeo e PDF), enquanto os demais fluxos permanecem via download ou visualização externa.


### Configurações essenciais

#### 1) Disco S3 privado e sem reescrita de host
O arquivo `config/filesystems.php` centraliza a definição dos discos de armazenamento usados pelo Laravel (Storage) e, por consequência, pelo Spatie Media Library. No contexto deste projeto, o bloco do disco `s3` aponta o SDK para o MinIO e controla parâmetros críticos: credenciais, bucket, endpoint e modo path‑style, além da visibilidade padrão e das opções de assinatura. Ao manter `visibility` como `private`, garantimos que todos os objetos são privados por padrão; ao não definir `temporary_url`, evitamos reescrever o host após a assinatura, preservando a integridade da assinatura SigV4; e, com `use_path_style_endpoint = true`, asseguramos compatibilidade com o MinIO. A chave `url` continua disponível como base para `Storage::url()` (URLs não assinadas), mas não é utilizada na construção de `temporaryUrl()`, portanto não interfere nas URLs temporárias assinadas.

Arquivo: `config/filesystems.php`

```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'bucket' => env('AWS_BUCKET', 'labsis'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT', env('AWS_URL')),
    // Removido 'temporary_url' para não trocar host após assinar
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
    'visibility' => 'private',
    'throw' => false,
    'report' => false,
    'options' => [
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'signature_version' => 'v4',
    ],
],
```

Observação importante: a assinatura SigV4 inclui o header `host`. Para evitar o erro de `SignatureDoesNotMatch`, o host de URLs temporárias após assinar deve ser o mesmo host do `AWS_ENDPOINT`.

#### 2) Configuração do Pacote Spatie Media Library
O arquivo `config/media-library.php` centraliza a configuração do pacote Spatie Media Library no projeto. Nele definimos o gerador de URLs (`url_generator`) — que aqui foi substituído por `App\Support\MediaLibrary\SignedUrlGenerator` para que toda chamada a `getUrl()` retorne uma URL temporária assinada —, além de pontos como `path_generator` (organização de pastas), `file_namer`, drivers e otimizadores de imagem, filas de conversões, tempo padrão de expiração das URLs temporárias (`temporary_url_default_lifetime`) e caminhos para FFMPEG/FFProbe. Em resumo, é o local único onde ajustamos o comportamento de armazenamento, geração e entrega das mídias.

Arquivo: `config/media-library.php`

```php
'url_generator' => App\Support\MediaLibrary\SignedUrlGenerator::class,
```

Além do gerador de URLs, este projeto também personaliza outros pontos relevantes do `config/media-library.php`. O `path_generator` foi definido como `App\Support\MediaLibrary\MediaPathGenerator` para organizar pastas de forma previsível por coleção e modelo. 

```php
'path_generator' => App\Support\MediaLibrary\MediaPathGenerator::class,
```

E, em `config/media-library.php`, o TTL pode ser ajustado via env:

```php
'temporary_url_default_lifetime' => env('MEDIA_TEMPORARY_URL_DEFAULT_LIFETIME', 5),
```

#### 3) AppServiceProvider
O arquivo `app/Providers/AppServiceProvider.php` não executa o upload nem assina URLs, mas participa preparando o ambiente de armazenamento. Durante o `boot()`, a rotina `configStorage()` assegura que os diretórios lógicos `audios`, `images` e `documents` existam no disco `s3` (MinIO), criando-os quando necessário e gravando um arquivo `.keep` com visibilidade privada. Isso garante que a estrutura esperada pelo `App\Support\MediaLibrary\MediaPathGenerator` esteja disponível com permissões corretas, reduzindo falhas de permissão ou 404 na primeira escrita. A execução é ignorada quando a aplicação roda em console (`runningInConsole()`), evitando interferências em comandos e migrações, e é protegida por `try/catch` para não interromper a aplicação se o S3/MinIO não estiver configurado. A responsabilidade por organizar paths e gerar URLs assinadas permanece, respectivamente, no `MediaPathGenerator` e no `SignedUrlGenerator` configurados no `config/media-library.php`.

#### 4) Modelo Media e MediaItem
O pacote Spatie Media Library define o modelo Eloquent `Spatie\MediaLibrary\MediaCollections\Models\Media` (tabela `media`), responsável por persistir metadados dos arquivos anexados: coleção, disco, nome, nome do arquivo, MIME type, tamanho, conversões, imagens responsivas e propriedades customizadas. Cada registro de `media` pertence morficamente a um modelo de domínio (neste projeto, `App\Models\MediaItem`). Esse modelo expõe operações de alto nível, como `getUrl()` e `getTemporaryUrl()` (neste projeto, configuradas para sempre retornarem URL temporária assinada), além de integrações com conversões, thumbnails e imagens responsivas quando aplicável. Em suma, ele orquestra o vínculo entre Eloquent e o armazenamento (S3/MinIO), sem conhecer regras de negócio do domínio.

No domínio da aplicação, `App\Models\MediaItem` funciona como o agregado que representa uma mídia cadastrada. Ele não substitui o modelo `media` da Spatie; em vez disso, o utiliza via `InteractsWithMedia` para anexar exatamente um arquivo à coleção `media` no disco `s3` (`singleFile()`). O `MediaItem` também oferece atributos derivados úteis à UI, como `file_type` (que unifica `text/*` como Documento) e `human_size`, além de `name` (que prioriza o nome do anexo) e `image_url` (URL assinada para pré-visualizações quando houver imagem). Quando a mídia não é um arquivo, mas uma referência externa de vídeo, o relacionamento `video()` (hasOne) provê os metadados do vídeo vinculados ao mesmo registro de `MediaItem`.


#### 5) Modelo Video

O modelo `App\Models\Video` guarda os metadados sobre os vídeos externos (provedor, ID do provedor, URL, título e duração em segundos) e pertence a um `MediaItem`. Essa separação reflete a decisão de produto de não armazenar binários de vídeo no MinIO, mantendo apenas links e metadados de Vídeos no Youtube. A UI do Filament usa essas informações para exibir e abrir o conteúdo de forma adequada quando o item é um vídeo.


#### 6) Serviço MediaService

O `App\Services\MediaService` encapsula a lógica de criação e atualização de mídias no domínio. Ao receber um arquivo, cria o `MediaItem` e anexa o binário por meio do Spatie na coleção `media` do disco `s3`, preservando o nome original; ao receber uma URL de vídeo, registra a referência externa no domínio. Na atualização, trata a troca entre arquivo e URL (limpando o anexo quando se passa a usar vídeo e o inverso quando volta a arquivo) e permite ajustar o nome. Também expõe utilitários de consumo: `getMediaUrl()` retorna a URL de vídeo externa ou, para anexos, uma URL temporária assinada; `getMediaPath()` retorna o caminho interno no storage; `isFile()`/`isVideoUrl()` ajudam no fluxo condicional; e `getMediaType()` normaliza a tipagem (image, audio, video, document, unknown) para a UI.

#### 7) Serviço VideoMetadataService

O `App\Services\VideoMetadataService` enriquece registros de vídeo externo (YouTube) com título e duração. Primeiro tenta obter o título via oEmbed oficial; se indisponível, efetua leitura do HTML com cURL e extrai `og:title` ou o `<title>` normalizado. Para a duração, tenta `lengthSeconds` no JSON do player e, alternativamente, interpreta um valor ISO‑8601 encontrado no HTML, convertendo para segundos. Com esses dados, o projeto persiste metadados suficientes em `Video` para rotular e exibir vídeos no painel sem armazenar arquivos de mídia pesados e chaves de API de YouTube.



#### 8) UI no Filament

##### Formulário de upload

Arquivo: `app/Filament/Resources/Media/Schemas/MediaForm.php`
- Upload privado no S3 (`->disk('s3')->visibility('private')`).
- Possui input para mídia e URL de vídeo.

Trecho relevante do uploader:

```php
SpatieMediaLibraryFileUpload::make('media')
    ->id('mediaUpload')
    ->disk('s3')
    ->visibility('private')
    ->dehydrated(false)
    ->downloadable(true)
    ->openable(true)
    ->previewable(true)
    ->acceptedFileTypes(MediaAcceptedMime::defaults())
    ->maxFiles(1)
    ->maxSize(5120)
    ->collection('media')
    ->required(fn (Get $get): bool => empty($get('video.url')))
    ->disabled(fn ($get) => ! empty($get('video.url')))
    ->afterStateUpdatedJs(self::jsOnMediaUpdated())
    ->afterStateUpdated(function (mixed $state, Set $set): void {
        self::handleMediaStateUpdated($state, $set);
    })
    ->columnSpanFull();
```

##### Infolist e abrir mídia
Arquivo: `app/Filament/Resources/Media/Schemas/MediaInfolist.php`
- Ação `MediaAction::make('open')` abre somente tipos suportados pelo pacote.
- A URL vem assinada automaticamente (via gerador custom).

```php
MediaAction::make('open')
    ->label(fn ($record) => self::resolveMediaActionConfig($record)['label'])
    ->icon(fn ($record) => self::resolveMediaActionConfig($record)['icon'])
    ->media(fn ($record) => self::resolveMediaActionConfig($record)['media'] ?? '#')
    ->visible(fn ($record): bool => self::resolveMediaActionConfig($record)['media'] !== null)
```

##### Tabela
Arquivo: `app/Filament/Resources/Media/Tables/MediaTable.php`
- Exibe nome, tipo (badge), criado em.
- Usa `file_type` já unificado (evita casos não tratados).

---

### MinIO: bucket privado
- Bucket `labsis` criado previamente.
- Política privada aplicada:

```bash
mc alias set local http://minio:9000 sail password
mc anonymous set none local/labsis
```

Observação sobre host: a URL assinada usa o host do `AWS_ENDPOINT`. Use o mesmo host no navegador (ex.: resolvendo `minio` no etc/hosts adicionando a entrada `127.0.0.1 minio`) para evitar erro de assinatura.


### Referências

- [URL pré‑assinada: manipule arquivos no AWS S3 de forma segura e eficiente](https://medium.com/geekieeducacao/url-pr%C3%A9-assinada-manipule-arquivos-no-aws-s3-de-forma-segura-e-eficiente-62f93b66bc9b)
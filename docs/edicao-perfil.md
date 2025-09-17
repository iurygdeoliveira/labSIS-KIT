# Edição de Perfil no Filament

Esta documentação explica como configurar e personalizar a funcionalidade de edição de perfil no painel administrativo Filament, incluindo upload de avatar, configurações de usuário e autenticação de dois fatores.

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Arquivos de Configuração](#arquivos-de-configuração)
- [Configuração do Plugin](#configuração-do-plugin)
- [Modelo User](#modelo-user)
- [Configuração de Storage](#configuração-de-storage)
- [Personalizações Avançadas](#personalizações-avançadas)
- [Troubleshooting](#troubleshooting)

## 🎯 Visão Geral

O sistema de edição de perfil utiliza o plugin `filament-edit-profile` que fornece uma interface completa para usuários gerenciarem suas informações pessoais, incluindo:

- **Informações Básicas**: Nome, email, senha
- **Avatar**: Upload e gerenciamento de foto de perfil
- **Autenticação de Dois Fatores (2FA)**: Configuração e gerenciamento
- **Códigos de Recuperação**: Backup para acesso em caso de perda do dispositivo
- **Configurações de Idioma**: Suporte a múltiplos idiomas

## 📁 Arquivos de Configuração

### 1. BasePanelProvider.php

Localização: `app/Providers/Filament/BasePanelProvider.php`

Este arquivo configura o plugin de edição de perfil e define suas opções:

```php
->plugin(
    FilamentEditProfilePlugin::make()
        ->setNavigationLabel('Editar Perfil')
        ->setNavigationGroup('Configurações')
        ->setIcon('heroicon-s-adjustments-horizontal')
        ->shouldShowAvatarForm(
            value: true,
                directory: 'avatar',
            rules: 'mimes:png,jpg,jpeg|max:1024'
        )
        ->shouldShowEmailForm()
        ->shouldShowDeleteAccountForm(false)
        ->shouldShowMultiFactorAuthentication()
)
```

#### Opções de Configuração:

- **`setNavigationLabel()`**: Define o texto exibido no menu lateral
- **`setNavigationGroup()`**: Agrupa a funcionalidade em um menu específico
- **`setIcon()`**: Define o ícone do menu (usando Heroicons)
- **`shouldShowAvatarForm()`**: Controla a exibição do formulário de avatar
- **`shouldShowEmailForm()`**: Controla a exibição do formulário de email
- **`shouldShowDeleteAccountForm()`**: Controla a exibição do formulário de exclusão de conta
- **`shouldShowMultiFactorAuthentication()`**: Controla a exibição das opções de 2FA

### 2. User.php

Localização: `app/Models/User.php`

O modelo User implementa as interfaces necessárias e integra a Spatie Media Library para avatar salvo em MinIO (s3) já como thumb (256x256):

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasAvatar, HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('s3')
            ->singleFile();
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        // Intencionalmente vazio: avatar é salvo já como 256x256
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $media = $this->getFirstMedia('avatar');
        if ($media) {
            return $media->getUrl();
        }

        return null;
    }
}
```

O método `getFilamentAvatarUrl()` retorna a URL do arquivo da coleção `avatar`, que já é a imagem final (thumb 256x256) salva no bucket do MinIO.

#### Campos Necessários:

```php
protected $fillable = [
    'uuid',
    'name',
    'email',
    'password',
    'avatar_url',                    // Campo para o avatar
    'email_verified_at',
    'is_suspended',
    'suspended_at',
    'suspension_reason',
    'remember_token',
    'app_authentication_secret',     // Campo para 2FA
    'app_authentication_recovery_codes', // Campo para códigos de recuperação
];
```

### 3. filament-edit-profile.php

Localização: `config/filament-edit-profile.php`

Arquivo de configuração do plugin com opções personalizáveis:

```php
<?php

return [
    'locales' => [
        'pt_BR' => '🇧🇷 Português',
        'en' => '🇺🇸 Inglês',
        'es' => '🇪🇸 Espanhol',
    ],
    'locale_column' => 'locale',
    'theme_color_column' => 'theme_color',
    'avatar_column' => 'avatar_url',
    'disk' => 's3',                 // MinIO (S3)
    'visibility' => 'private',      // URLs assinadas quando necessário
];
```

#### Opções de Configuração:

- **`locales`**: Idiomas disponíveis para seleção
- **`locale_column`**: Nome da coluna que armazena o idioma preferido
- **`theme_color_column`**: Nome da coluna para cor do tema
- **`avatar_column`**: Nome da coluna que armazena o caminho do avatar
- **`disk`**: Disco de armazenamento para upload de arquivos
- **`visibility`**: Visibilidade dos arquivos (public/private)

### 4. filesystems.php

Localização: `config/filesystems.php`

Disco utilizado: s3 (MinIO).

```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'bucket' => env('AWS_BUCKET', 'labsis'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT', env('AWS_URL')),
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


## 📚 Documentação Oficial do Pacote

- [Plugin Filament Edit Profile](https://github.com/joaopaulolndev/filament-edit-profile)


**Nota**: Esta documentação é específica para a versão atual do projeto. Para versões mais recentes dos pacotes, consulte a documentação oficial.
 

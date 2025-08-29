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

### 1. AdminPanelProvider.php

Localização: `app/Providers/Filament/AdminPanelProvider.php`

Este arquivo configura o plugin de edição de perfil e define suas opções:

```php
->plugin(
    FilamentEditProfilePlugin::make()
        ->setNavigationLabel('Editar Perfil')
        ->setNavigationGroup('Configurações')
        ->setIcon('heroicon-s-adjustments-horizontal')
        ->shouldShowAvatarForm(
            value: true,
            directory: 'avatars',
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

O modelo User implementa as interfaces necessárias para o funcionamento do plugin:

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;

class User extends Authenticatable implements 
    FilamentUser, 
    HasAppAuthentication, 
    HasAppAuthenticationRecovery, 
    HasAvatar
{
    // ... código do modelo
}
```

#### Método getFilamentAvatarUrl():

```php
public function getFilamentAvatarUrl(): ?string
{
    $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');

    if (!$this->$avatarColumn) {
        return null;
    }

    // Como agora estamos usando o disco 'public', usamos Storage::url diretamente
    return Storage::url($this->$avatarColumn);
}
```

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
    'disk' => 'public',              // Disco de armazenamento para avatares
    'visibility' => 'public',        // Visibilidade dos arquivos
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

Configuração dos discos de armazenamento para upload de arquivos:

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
        'serve' => true,
        'throw' => false,
        'report' => false,
    ],

    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
        'report' => false,
    ],
],

'links' => [
    public_path('storage') => storage_path('app/public'),
],
```

## ⚙️ Configuração do Plugin

### Configuração Básica no AdminPanelProvider:

```php
->plugin(
    FilamentEditProfilePlugin::make()
        ->setNavigationLabel('Editar Perfil')
        ->setNavigationGroup('Configurações')
        ->setIcon('heroicon-s-adjustments-horizontal')
        ->shouldShowAvatarForm(
            value: true,
            directory: 'avatars',
            rules: 'mimes:png,jpg,jpeg|max:1024'
        )
        ->shouldShowEmailForm()
        ->shouldShowDeleteAccountForm(false)
        ->shouldShowMultiFactorAuthentication()
)
```

### Configuração de Avatar:

```php
->shouldShowAvatarForm(
    value: true,                     // Exibe o formulário de avatar
    directory: 'avatars',            // Diretório de armazenamento
    rules: 'mimes:png,jpg,jpeg|max:1024' // Regras de validação
)
```

**Regras de Validação Disponíveis:**
- **`mimes`**: Tipos de arquivo permitidos
- **`max`**: Tamanho máximo em kilobytes
- **`dimensions`**: Dimensões da imagem (ex: `min:200,200|max:800,800`)

### Configuração de 2FA:

```php
->shouldShowMultiFactorAuthentication()
```

Esta opção habilita:
- Configuração de aplicativos de autenticação
- Geração de códigos QR
- Códigos de recuperação
- Desativação de 2FA


## 📚 Documentação Oficial do Pacote

- [Plugin Filament Edit Profile](https://github.com/joaopaulolndev/filament-edit-profile)


**Nota**: Esta documentação é específica para a versão atual do projeto. Para versões mais recentes dos pacotes, consulte a documentação oficial.

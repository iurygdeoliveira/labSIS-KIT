# Autenticação de Dois Fatores (2FA) no Filament

## 📋 Índice

- [Introdução](#introdução)
- [Como Funciona o 2FA](#como-funciona-o-2fa)
- [Arquitetura da Implementação](#arquitetura-da-implementação)
- [Configurando o 2FA para um Usuário](#configurando-o-2fa-para-um-usuário)
- [Códigos de Recuperação](#códigos-de-recuperação)
- [Desativando o 2FA](#desativando-o-2fa)
- [Segurança e Boas Práticas](#segurança-e-boas-práticas)
- [Troubleshooting](#troubleshooting)
- [Conclusão](#conclusão)

## Introdução

Este projeto implementa um sistema completo de **Autenticação de Dois Fatores (2FA)** usando o Filament PHP. O sistema permite que os usuários protejam suas contas com um segundo nível de segurança, além da senha tradicional.

## Como Funciona o 2FA

A autenticação de dois fatores funciona em duas etapas:

1. **Primeira etapa**: Usuário insere email e senha
2. **Segunda etapa**: Usuário insere um código temporário gerado por um aplicativo de autenticação (Google Authenticator, Authy, etc.)

## Arquitetura da Implementação

### 1. Modelo User

O modelo `User` implementa as interfaces necessárias para o 2FA:

```php
// app/Models/User.php

use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;

class User extends Authenticatable implements 
    FilamentUser, 
    HasAppAuthentication, 
    HasAppAuthenticationRecovery
{
    // ... outras propriedades
}
```

### 2. Campos do Banco de Dados

A tabela `users` possui campos específicos para o 2FA:

```php
// database/migrations/0001_01_01_000000_create_users_table.php

Schema::create('users', function (Blueprint $table) {
    // ... outros campos
    $table->text('app_authentication_secret')->nullable();        // Chave secreta para 2FA
    $table->text('app_authentication_recovery_codes')->nullable(); // Códigos de recuperação
    // ... outros campos
});
```

### 3. Traits de Autenticação

O sistema utiliza dois traits para gerenciar a autenticação:

#### AppAuthenticationSecret

Gerencia a chave secreta para geração dos códigos 2FA:

```php
// app/Trait/Filament/AppAuthenticationSecret.php

trait AppAuthenticationSecret
{
    public function getAppAuthenticationSecret(): ?string
    {
        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email; // Identificador único para o app de autenticação
    }
}
```

#### AppAuthenticationRecoveryCodes

Gerencia os códigos de recuperação para casos de emergência:

```php
// app/Trait/Filament/AppAuthenticationRecoveryCodes.php

trait AppAuthenticationRecoveryCodes
{
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->app_authentication_recovery_codes;
    }

    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }
}
```

### 4. Configuração do Painel

O `AdminPanelProvider` configura o 2FA para o painel administrativo:

```php
// app/Providers/Filament/AdminPanelProvider.php

use Filament\Auth\MultiFactor\App\AppAuthentication;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... outras configurações
        ->multiFactorAuthentication(
            AppAuthentication::make()
                ->recoverable() // Habilita códigos de recuperação
        )
        // ... outras configurações
}
```

## Configurando o 2FA para um Usuário

### Passo 1: Acessar o Perfil

1. Faça login no painel administrativo (`/admin`)
2. Clique no seu nome no canto superior direito
3. Selecione "Perfil"

### Passo 2: Ativar a Autenticação de Dois Fatores

1. Na página de perfil, procure pela seção "Autenticação de Dois Fatores"
2. Clique em "Ativar"
3. Escaneie o QR Code com seu aplicativo de autenticação:
   - **Google Authenticator** (Android/iOS)
   - **Authy** (Android/iOS/Desktop)
   - **Microsoft Authenticator** (Android/iOS)
   - **1Password** (Desktop/Mobile)

### Passo 3: Verificar a Ativação

1. Digite o código de 6 dígitos exibido no seu aplicativo
2. Clique em "Confirmar"
3. O 2FA estará ativo para sua conta

## Códigos de Recuperação

### O que são?

Os códigos de recuperação são uma alternativa de acesso caso você perca seu dispositivo de autenticação ou tenha problemas com o 2FA.

### Como usar?

1. Na página de login, após inserir email e senha
2. Clique em "Usar código de recuperação"
3. Digite um dos códigos de recuperação salvos
4. Acesse sua conta normalmente

### Importante!

- **Guarde os códigos em local seguro** (não no mesmo dispositivo do 2FA)
- **Cada código só pode ser usado uma vez**
- **Gere novos códigos** se suspeitar que foram comprometidos

## Desativando o 2FA

### Para desativar:

1. Acesse seu perfil
2. Na seção "Autenticação de Dois Fatores"
3. Clique em "Desativar"
4. Confirme sua senha
5. O 2FA será removido da sua conta

## Segurança e Boas Práticas

### Recomendações:

1. **Use aplicativos confiáveis**: Google Authenticator, Authy, Microsoft Authenticator
2. **Não compartilhe códigos**: Mantenha seus códigos 2FA privados
3. **Backup dos códigos**: Salve os códigos de recuperação em local seguro
4. **Dispositivo dedicado**: Considere usar um dispositivo específico para 2FA
5. **Atualizações**: Mantenha seu aplicativo de autenticação atualizado

## Problemas Comuns:

1. **Código não aceito**:
   - Verifique se o relógio do dispositivo está sincronizado
   - Aguarde o próximo código (30 segundos)
   - Use código de recuperação se necessário

2. **Aplicativo não funciona**:
   - Reinstale o aplicativo
   - Verifique permissões de câmera (para QR Code)
   - Teste com outro aplicativo

3. **Perdeu o dispositivo**:
   - Use códigos de recuperação
   - Entre em contato com o administrador
   - Considere resetar a conta se necessário


## Conclusão

O sistema de 2FA implementado neste projeto oferece uma camada adicional de segurança robusta e confiável. Seguindo as boas práticas e configurações recomendadas, você pode proteger efetivamente as contas dos usuários contra acessos não autorizados.

Para mais informações sobre o sistema de 2FA do Filament, consulte a [documentação oficial](https://filamentphp.com/docs/4.x/users/multi-factor-authentication#introduction).

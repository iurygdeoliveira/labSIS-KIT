---
title: Plano de Integração — Laravel Passkeys no labSIS-KIT
artifact-type: implementation-plan
source-skill: markdown-to-html-artifact
lang: pt-BR
project: labSIS-KIT
stack: Laravel 13 · Filament 5 · FilaTeams · Filament Security
package: laravel/passkeys
repository: https://github.com/laravel/passkeys-server
---

# Plano de Integração — Laravel Passkeys no labSIS-KIT

> Autenticação passwordless via WebAuthn com o pacote oficial **`laravel/passkeys`**, integrado ao fluxo multi-painel Filament existente (auth, admin, user), respeitando aprovação manual, suspensão, MFA TOTP e auditoria de segurança.

## Meta

| Campo | Valor |
|-------|-------|
| **Pacote** | `laravel/passkeys` ([passkeys-server](https://github.com/laravel/passkeys-server)) |
| **Cliente JS** | `@laravel/passkeys` (npm) |
| **Compatibilidade** | Laravel 13 ✅ · PHP 8.5 ✅ · Filament 5 ✅ |
| **Escopo** | Painel Auth (`/login`), perfil Filament, regras de negócio labSIS |
| **Fora de escopo (v1)** | API Sanctum, substituição total do fluxo de aprovação admin |

---

## 1. Visão Geral

### 1.1 O que é Laravel Passkeys

Pacote **first-party** da equipe Laravel para autenticação **passwordless** usando **WebAuthn/Passkeys**:

- Login com **biometria** (Touch ID, Face ID, Windows Hello)
- Login com **PIN do dispositivo** ou **chave de segurança** (YubiKey)
- **Sem envio de e-mail** no momento do login
- **Sem link mágico** — a prova de identidade ocorre no navegador + dispositivo

### 1.2 O que Passkeys **NÃO** é

| Mecanismo | Como funciona | Passkey? |
|-----------|---------------|----------|
| **Senha tradicional** | Usuário digita e-mail + senha | ❌ |
| **Link mágico por e-mail** | E-mail com URL de acesso único | ❌ |
| **TOTP (Google Authenticator)** | Código de 6 dígitos temporário | ❌ (complementar) |
| **Passkey (WebAuthn)** | Biometria/PIN no dispositivo | ✅ |

**Resposta direta:** após registrar uma passkey, o usuário **pode acessar o painel sem digitar senha**, mas **não recebe e-mail com link de acesso**. A autenticação é local ao dispositivo.

### 1.3 Stack de auth atual no labSIS-KIT

| Componente | Estado atual |
|------------|--------------|
| **Painel Auth** | `/login`, `/register` — login unificado |
| **MFA TOTP** | `AppAuthentication::make()->recoverable()` em todos os painéis |
| **Aprovação manual** | `is_approved`, `is_suspended` no registro |
| **Redirecionamento** | `LoginResponse` → admin / user / home por role |
| **Filament Security** | Event log, honeypot, e-mail descartável (config) |
| **Auditoria** | `AuthenticationLog` (MongoDB) em Login/Logout/Failed |
| **Passkeys** | ❌ Não instalado |

---

## 2. Ganhos Possíveis (todos os cenários)

### 2.1 Experiência do usuário

| Ganho | Descrição | Beneficiário |
|-------|-----------|--------------|
| **Login passwordless** | Entrada com biometria em 1–2 segundos | Admins, owners, usuários frequentes |
| **Sem memorizar senha** | Reduz fricção no acesso diário | Todos os perfis aprovados |
| **Múltiplos dispositivos** | MacBook + iPhone + YubiKey registrados | Usuários mobile + desktop |
| **Autofill do navegador** | Chrome/Safari sugerem passkey automaticamente | UX nativa moderna |
| **Self-service de dispositivos** | Revogar passkey perdida no perfil Filament | Reduz tickets de suporte |

### 2.2 Segurança

| Ganho | Descrição | Impacto |
|-------|-----------|---------|
| **Anti-phishing** | Credencial amarrada ao domínio (`labsis.dev.br`) | Ataques de clone de login falham |
| **Sem senha reutilizada** | Não há senha a vazar em breach externo | Reduz credential stuffing |
| **Resistente a keyloggers** | Biometria não trafega como texto digitado | Proteção em endpoints públicos |
| **Política por autenticador** | Restringir a platform keys (só biometria local) | Admins com política mais rígida |
| **`authorizeLoginUsing`** | Bloquear suspensos/não aprovados no fluxo passkey | Paridade com `Login.php` customizado |
| **Eventos nativos** | `PasskeyRegistered`, `PasskeyVerified`, `PasskeyDeleted` | Integração com logs existentes |
| **Reauth bound ao user** | Confirmar ações críticas sem redigitar senha | Exclusão de team, alteração de RBAC |
| **Throttle nativo** | `throttle:6,1` nas rotas passkey | Proteção contra brute force |

### 2.3 Operacional e arquitetura

| Ganho | Descrição |
|-------|-----------|
| **Pacote oficial Laravel** | Alinhado ao ecossistema L13, mantido pela equipe core |
| **`PasskeyLoginResponse`** | Reutilizar lógica de `LoginResponse` pós-passkey |
| **Actions substituíveis** | Customizar `GenerateRegistrationOptions`, `VerifyPasskey` |
| **Compatível com Filament Security** | Event log de login falho continua; passkey adiciona novos eventos |
| **Senha como fallback** | Recuperação, registro de passkey, usuários sem dispositivo compatível |
| **Plugin Filament (comunidade)** | `adriaanzon/filament-passkeys` sobre `laravel/passkeys` |

### 2.4 O que **não muda** automaticamente

- Fluxo de **aprovação manual** de novos usuários
- **RBAC** FilaTeams + Spatie Permission por team
- **Filament Security** (camadas de registro e event log)
- **Tokens Sanctum** para API
- **MFA TOTP** — permanece até decisão explícita de substituir ou combinar

---

## 3. Arquitetura Proposta

### 3.1 Componentes do pacote oficial

```
┌─────────────────────────────────────────────────────────────┐
│  Browser: @laravel/passkeys (npm)                           │
│  Passkeys.verify() · Passkeys.register({ name })              │
└──────────────────────────┬──────────────────────────────────┘
                           │ WebAuthn
┌──────────────────────────▼──────────────────────────────────┐
│  laravel/passkeys                                           │
│  GET  /passkeys/login/options                               │
│  POST /passkeys/login                                       │
│  GET/POST/DELETE /user/passkeys/*                           │
│  GET/POST /passkeys/confirm/*                               │
└──────────────────────────┬──────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────┐
│  labSIS-KIT                                                 │
│  User + PasskeyAuthenticatable + PasskeyUser                │
│  Passkeys::authorizeLoginUsing() → isSuspended/isApproved   │
│  PasskeyLoginResponse → LoginResponse (admin/user/home)     │
│  Events → AuthenticationLog + SecurityEvent (opcional)      │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 Fluxo de primeira configuração

1. Usuário entra com **e-mail + senha** (fluxo atual).
2. Passa aprovação, suspensão, MFA TOTP se ativo.
3. No **perfil Filament**, registra passkey (ex.: "MacBook Pro").
4. Navegador solicita biometria/PIN **uma vez** para gravar credencial.
5. Por padrão, registro exige **`password.confirm`**.

### 3.3 Fluxo de login subsequente (passwordless)

1. Usuário abre `/login`.
2. Clica **"Entrar com Passkey"** (ou autofill do navegador).
3. Biometria/PIN — **sem digitar senha**.
4. `authorizeLoginUsing` valida suspensão/aprovação.
5. MFA TOTP — **depende do cenário escolhido** (ver seção 6).
6. `PasskeyLoginResponse` redireciona para painel correto.

### 3.4 Integração Filament

| Painel | Alteração |
|--------|-----------|
| **AuthPanelProvider** | Botão passkey na login + rotas `Route::passkeys()` |
| **BasePanelProvider** | Seção passkeys no perfil (ou plugin AdriaanZon) |
| **AppServiceProvider** | `authorizeLoginUsing`, `PasskeyLoginResponse`, listeners de eventos |
| **User model** | `PasskeyUser` + `PasskeyAuthenticatable` |

---

## 4. Milestones de Implementação

### M1 — Fundação (backend)

**Entrada:** `composer.json`, `User.php`, migrations  
**Saída:** Pacote instalado, tabela `passkeys`, trait no User

```bash
composer require laravel/passkeys
php artisan vendor:publish --tag=passkeys-migrations
php artisan migrate
php artisan vendor:publish --tag=passkeys-config
npm install @laravel/passkeys
```

```php
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\PasskeyAuthenticatable;

class User extends Authenticatable implements PasskeyUser, /* interfaces existentes */ {
    use PasskeyAuthenticatable;
}
```

**Verificação:** `php artisan route:list --path=passkeys` lista rotas; migration OK.

---

### M2 — Regras de negócio labSIS

**Entrada:** `AppServiceProvider.php`  
**Saída:** Paridade com `Login.php` customizado

```php
Passkeys::authorizeLoginUsing(function (Request $request, PasskeyUser $user, Passkey $passkey): bool {
    if ($user->isSuspended()) {
        throw ValidationException::withMessages([
            'credential' => ['Sua conta está suspensa.'],
        ]);
    }
    if (! $user->isApproved()) {
        throw ValidationException::withMessages([
            'credential' => ['Sua conta aguarda aprovação do administrador.'],
        ]);
    }
    return true;
});
```

**Verificação:** Usuário suspenso/não aprovado não autentica via passkey.

---

### M3 — Redirecionamento multi-painel

**Entrada:** `LoginResponse.php`  
**Saída:** `PasskeyLoginResponse` com mesma lógica admin/user/home

**Verificação:** Após passkey login, admin vai para `/admin`, owner para `/user/{slug}`.

---

### M4 — UI no login e perfil

**Entrada:** View login Filament, perfil Filament, Vite  
**Saída:** Botão "Entrar com Passkey" + gestão de dispositivos no perfil

```js
import { Passkeys } from '@laravel/passkeys'
await Passkeys.verify()           // login
await Passkeys.register({ name: 'MacBook' })  // registro (autenticado)
```

**Alternativa:** `composer require adriaanzon/filament-passkeys` + `FilamentPasskeysPlugin`.

**Verificação:** Login passwordless funciona em HTTPS; perfil lista/revoga passkeys.

---

### M5 — Auditoria e eventos

**Entrada:** `AppServiceProvider`, listeners  
**Saída:** `PasskeyVerified` → `AuthenticationLog`; opcional → `SecurityEvent`

**Verificação:** Log de passkey login aparece no admin.

---

### M6 — MFA: decisão de produto

**Entrada:** `BasePanelProvider`, `AuthPanelProvider`  
**Saída:** Cenário escolhido (ver seção 6) documentado e implementado

**Verificação:** Testes Pest cobrem login passkey + MFA conforme cenário.

---

## 5. Configuração Recomendada

```php
// config/passkeys.php (publicado)
return [
    'relying_party_id' => parse_url(config('app.url'), PHP_URL_HOST),
    'allowed_origins' => [config('app.url')],
    'user_handle_secret' => env('PASSKEYS_USER_HANDLE_SECRET', config('app.key')),
    'timeout' => 60000,
    'guard' => 'web',
    'middleware' => ['web'],
    'management_middleware' => ['password.confirm'],
    'throttle' => 'throttle:6,1',
    'redirect' => '/', // substituir por PasskeyLoginResponse customizado
];
```

### Variáveis `.env`

```env
APP_URL=https://labsis.dev.br
PASSKEYS_USER_HANDLE_SECRET=   # opcional; default app.key
```

---

## 6. Cenários de Produto (escolher um)

| Cenário | Senha no login | Passkey login | MFA TOTP | Recomendado para |
|---------|----------------|---------------|----------|------------------|
| **A — Passwordless + TOTP fallback** | Opcional | Sim | Sim (quem não tem passkey) | Rollout gradual |
| **B — Passkey substitui TOTP** | Sim | Sim | Não | Admins técnicos |
| **C — Passkey como 2º fator** | Sim | Não (só MFA) | Substituído por passkey | Máxima segurança com senha |
| **D — Passwordless total** | Fallback apenas | Sim | Não | UX máxima, admins only v1 |

**Recomendação labSIS (v1):** Cenário **A** — passwordless no login + TOTP recoverable como fallback + registro de passkey só para usuários **aprovados**.

---

## 7. Matriz de Riscos

| Risco | Severidade | Mitigação |
|-------|------------|-----------|
| WebAuthn exige HTTPS | **Alta** | `URL::forceHttps()` em prod; Valet secure / cert local em dev |
| `APP_URL` incorreto | **Alta** | Validar host = relying party id |
| Passkey bypassa aprovação | **Alta** | `authorizeLoginUsing` obrigatório |
| Usuário perde dispositivo | **Média** | Senha + recovery TOTP + admin revoga passkeys |
| Pacote v0.2.x imaturo | **Média** | Pin versão; testes Pest; monitorar changelog |
| Conflito rotas `/passkeys` | **Baixa** | Landing em `/`, login em `/login` — sem conflito |
| MFA duplicado (passkey + TOTP) | **Média** | `managementOnly()` ou cenário A documentado |

---

## 8. Pré-requisitos Técnicos

| Requisito | labSIS-KIT |
|-----------|------------|
| Laravel 13 | ✅ |
| PHP 8.2+ | ✅ (8.5) |
| Colunas `name`, `email` no User | ✅ |
| Filament `->profile()` | ✅ |
| HTTPS produção | ✅ (`AppServiceProvider`) |
| HTTPS desenvolvimento | ⚠️ Configurar para testar passkeys |
| npm / Vite | ✅ |

---

## 9. Comparação: Oficial vs Alternativas

| | **laravel/passkeys** | spatie/laravel-passkeys |
|--|---------------------|-------------------------|
| Mantenedor | Laravel (Taylor Otwell) | Spatie |
| Cliente JS | `@laravel/passkeys` | Livewire components |
| Hook pós-login | `authorizeLoginUsing()` | Manual |
| Reauth / 2FA step | `VerifyPasskey` user-bound | Menos explícito |
| Plugin Filament | adriaanzon/filament-passkeys | marcelweidum/filament-passkeys |
| Maturidade | v0.2.x | v1.7.x |

**Decisão:** usar pacote **oficial** por alinhamento Laravel 13 e hooks nativos para regras labSIS.

---

## 10. Testes Propostos

| Teste | Tipo | Critério |
|-------|------|----------|
| Usuário aprovado autentica via passkey | Feature | Redirect correto |
| Usuário suspenso bloqueado | Feature | ValidationException |
| Usuário não aprovado bloqueado | Feature | ValidationException |
| Registro passkey exige senha | Feature | password.confirm |
| Revogar passkey impede login | Feature | 401/422 |
| Evento PasskeyVerified dispara log | Feature | AuthenticationLog entry |

---

## 11. Perguntas em Aberto

1. **Cenário MFA:** A (gradual) ou B (substituir TOTP para admins)?
2. **Escopo v1:** Todos os usuários ou só role `admin`?
3. **Plugin Filament:** UI customizada ou `adriaanzon/filament-passkeys`?
4. **HTTPS local:** Sail com cert ou Valet secure para dev?
5. **Forçar passkey:** Admins obrigados a registrar passkey em X dias?

---

## 12. Referências

- [laravel/passkeys-server](https://github.com/laravel/passkeys-server) — pacote oficial
- [@laravel/passkeys](https://github.com/laravel/passkeys) — cliente npm
- [adriaanzon/filament-passkeys](https://filamentphp.com/plugins/adriaanzon-passkeys) — plugin Filament v5
- `docs/02-autenticacao-e-seguranca/login-unificado.md`
- `docs/02-autenticacao-e-seguranca/autenticacao-2fa.md`
- `app/Filament/Pages/Auth/Login.php`
- `app/Http/Responses/LoginResponse.php`

---

## 13. Resumo Executivo

| Pergunta | Resposta |
|----------|----------|
| Preciso digitar senha após adaptar? | **Não**, se registrar passkey e usar login passwordless |
| Recebo e-mail com link de acesso? | **Não** — passkey usa biometria do dispositivo |
| Aprovação admin continua? | **Sim** — `authorizeLoginUsing` mantém regras |
| TOTP continua? | **Depende do cenário** — recomendado manter como fallback na v1 |
| Esforço estimado | M1–M4: ~2–3 dias; M5–M6: +1 dia com testes |

**Próximo passo:** implementar M1 + M2 + M3 (backend + regras + redirect), depois M4 (UI login/perfil).

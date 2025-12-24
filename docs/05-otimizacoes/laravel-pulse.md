# Laravel Pulse

Esta documentação descreve a instalação, propósito e configuração do Laravel Pulse em nosso projeto.

## O que é?

O **Laravel Pulse** é uma ferramenta de monitoramento de saúde e performance em tempo real para aplicações Laravel. Ele fornece um dashboard visual para métricas críticas como:

-   Uso de CPU e Memória do servidor.
-   Filas (Queues) e Jobs.
-   Cache (Redis/Memcached).
-   Requisições Lentas (Slow Requests).
-   Consultas ao Banco de Dados demoradas (Slow Queries).
-   Exceções e erros da aplicação.

## Objetivo no Projeto

O objetivo principal de instalar o Pulse é obter **observabilidade proativa**. Com ele, podemos identificar gargalos de performance, jobs falhando silenciosamente ou queries ineficientes antes que se tornem problemas críticos para os usuários.

Diferente de ferramentas de log estáticas, o Pulse permite ver o estado da aplicação "agora".

## Instalação e Configuração

O Pulse já foi instalado via Composer e suas tabelas de banco de dados foram migradas.

### Instalação (Referência)

```bash
composer require laravel/pulse
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"
php artisan migrate
```

### Proteção de Acesso

Por padrão, o Pulse é acessível apenas em ambiente local (`local`). Em produção, é **crucial** restringir o acesso apenas a administradores.

Implementamos um Gate de autorização no `AppServiceProvider` para garantir que apenas usuários com a role `admin` possam acessar o dashboard.

**Configuração em `app/Providers/AppServiceProvider.php`:**

```php
Gate::define('viewPulse', function (User $user) {
    return $user->hasRole('admin');
});
```

A rota de acesso é: `/pulse`

## Mais Informações

Para detalhes completos sobre customização de "Recorders", configuração avançada e criação de cards personalizados, consulte a documentação oficial:

[**Documentação Oficial do Laravel Pulse**](https://laravel.com/docs/12.x/pulse)

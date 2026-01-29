vv# LabSIS KIT Roadmap

Este documento centraliza as próximas funcionalidades e otimizações planejadas para o **LabSIS KIT**. Estes são os passos que pretendemos seguir para tornar este Starter Kit ainda mais completo e robusto.

---

## 🚀 Próximos Passos (Recursos a serem implementados)

### 👥 Usuários e Autenticação

- [ ] **Impersonação de Usuários:** Permitir que administradores globais acessem o painel como se fossem um usuário específico para facilitar o suporte.

### 🏢 Multi-tenancy

- [ ] **Customização de Branding por Tenant:** Permitir que cada tenant defina seu próprio logotipo e cores primárias no painel `/user`.

### ⚡ Performance e Monitoramento

- [ ] **Benchmarks Automatizados:** Integração contínua de testes de performance (SPA vs MPA) para garantir que as atualizações não degradem a experiência do usuário.
- [ ] **Logs via MongoDB:** Refatorar o sistema de logs de atividades para utilizar MongoDB como storage padrão, garantindo escalabilidade e performance em aplicações com alto tráfego.
- [ ] **Laravel Octane + FrankenPHP (PHP 8.5-ZTS):** Migrar para FrankenPHP com PHP-ZTS 8.5 para habilitar worker mode e melhorar performance de requisições. Implementação baseada em [PHP 8.5 com Laravel Octane e FrankenPHP - The Missing Manual](https://danielpetrica.com/running-php-8-5-with-laravel-octane-and-frankenphp-the-missing-manual/). Inclui:
  - Instalação de PHP-ZTS 8.5 via repositório Henderkes
  - Configuração de extensões ZTS (bcmath, gd, intl, mysql, mbstring, etc.)
  - Debug logging com `--log-level=debug` para troubleshooting
  - Arquitetura de alta performance com Traefik + FrankenPHP

---

## 📊 Pesquisas e Metodologias

- [ ] **Metodologia: SPA vs MPA** - Estudo detalhado sobre os ganhos de performance ao utilizar o modo Single Page Application do Filament.

---

## 🛠️ Como Contribuir

Se você tem interesse em ajudar no desenvolvimento de algum destes itens, sinta-se à vontade para abrir uma issue ou enviar um Pull Request.

---

<div align="center">
  <strong>LabSIS - Transformando desafios reais em soluções inteligentes</strong>
</div>

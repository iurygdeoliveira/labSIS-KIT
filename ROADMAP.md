 vv# LabSIS KIT Roadmap

Este documento centraliza as próximas funcionalidades e otimizações planejadas para o **LabSIS KIT**. Estes são os passos que pretendemos seguir para tornar este Starter Kit ainda mais completo e robusto.

---

## 🚀 Próximos Passos (Recursos a serem implementados)

### 👥 Usuários e Autenticação

-   [ ] **Impersonação de Usuários:** Permitir que administradores globais acessem o painel como se fossem um usuário específico para facilitar o suporte.
-   [ ] **Integração Socialite:** Adicionar suporte a login via redes sociais (Google, GitHub, etc.).

### 🏢 Multi-tenancy

-   [ ] **Painel de E-mails por Tenant:** Ajustar a gestão de templates de e-mail para permitir customização individual para cada organização/tenant.
-   [ ] **Customização de Branding por Tenant:** Permitir que cada tenant defina seu próprio logotipo e cores primárias no painel `/user`.

### ⚡ Performance e Monitoramento

-   [ ] **Benchmarks Automatizados:** Integração contínua de testes de performance (SPA vs MPA) para garantir que as atualizações não degradem a experiência do usuário.
-   [ ] **Logs via MongoDB:** Refatorar o sistema de logs de atividades para utilizar MongoDB como storage padrão, garantindo escalabilidade e performance em aplicações com alto tráfego.

### 🤖 Inteligência Artificial

-   [ ] **Agentes de Automação:** Criar playbooks de agentes específicos para auxiliar na geração de código seguindo a arquitetura do LabSIS KIT.

---

## 📊 Pesquisas e Metodologias

-   [**Metodologia: SPA vs MPA**](/roadmap/04-performance-spa_vs_mpa.md) - Estudo detalhado sobre os ganhos de performance ao utilizar o modo Single Page Application do Filament.

---

## 🛠️ Como Contribuir

Se você tem interesse em ajudar no desenvolvimento de algum destes itens, sinta-se à vontade para abrir uma issue ou enviar um Pull Request.

---

<div align="center">
  <strong>LabSIS - Transformando desafios reais em soluções inteligentes</strong>
</div>

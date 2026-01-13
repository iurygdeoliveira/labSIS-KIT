# Integração com Agentes de IA (.context)

Este projeto utiliza o protocolo **MCP AI-Context** para fornecer inteligência contextual a agentes de IA (como Claude, GPT-4, Gemini) que trabalham na base de código.

## 🧠 O Diretório `.context`

Na raiz do projeto, existe uma pasta `.context` que funciona como o "cérebro compartilhado" entre desenvolvedores humanos e agentes de IA.

```text
.context/
├── agents/           # Playbooks especializados
│   ├── backend-specialist.md
│   ├── frontend-specialist.md
│   └── ...
└── docs/             # Documentação de arquitetura viva
    ├── architecture.md
    ├── project-overview.md
    └── ...
```

## 🤖 Como Usar

Quando você pedir para uma IA realizar uma tarefa, ela consultará automaticamente estes arquivos para entender:

1. **Padrões de Código**: Como escrever Models, Controllers e Services no estilo do LabSIS.
2. **Regras de Negócio**: Como lidar com Multi-tenancy, UUIDs e Permissões.
3. **Estilo Visual**: Como usar o sistema de cores CSS modular.

### Playbooks Disponíveis

- **Backend Specialist**: Criação de CRUDs, Services e Lógica de Tenant.
- **Frontend Specialist**: Componentes Blade/Livewire e temas Filament.
- **Test Writer**: Padrões de teste com Pest v4.
- **Bug Fixer**: Estratégias de debug e logs.

## 🔄 Manutenção

Estes arquivos são **vivos**. Se você mudar uma decisão arquitetural importante (ex: trocar UUID por ULID), atualize o arquivo correspondente em `.context/docs/` para que a IA não continue sugerindo o padrão antigo.

> **Nota**: O conteúdo destes arquivos é gerado e refinado usando ferramentas como **Laravel Boost** e **Serena** para garantir precisão técnica.

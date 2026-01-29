# Estratégia de Backup

Este documento descreve a estratégia de backup implementada no **labSIS-KIT**, utilizando o pacote [Spatie Laravel Backup](https://github.com/spatie/laravel-backup). A finalidade é garantir a integridade dos dados e a continuidade da operação em caso de falhas críticas.

## 📌 Visão Geral

A estratégia de backup do kit baseia-se em três pilares:
1.  **Backup de Dados (SQL):** Dump automático do banco de dados principal.
2.  **Backup de Arquivos (Mídias):** Compactação dos arquivos armazenados no `storage/app/public`.
3.  **Agendamento Automático:** Execução programada via Scheduler do Laravel.

## 🛠️ Ferramentas Utilizadas

-   **Spatie Laravel Backup:** Motor principal para criação, monitoramento e limpeza de backups.
-   **Laravel Scheduler:** Orquestrador para disparar os backups em janelas de tempo de baixo tráfego.
-   **Flysystem (S3/DigitalOcean):** Recomendado para armazenamento geodistante (off-site).

## 🗄️ Configuração de Armazenamento

Por padrão, o kit está configurado para permitir múltiplos destinos. É **altamente recomendável** que em ambiente de produção o destino seja um armazenamento em nuvem (Amazon S3 ou compatível).

### Configuração no `.env` (Recomendado)
Para utilizar o driver S3, configure as credenciais:
```env
AWS_ACCESS_KEY_ID=sua_key
AWS_SECRET_ACCESS_KEY=sua_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=nome-do-bucket
AWS_ENDPOINT=https://endpoint-do-provedor.com
```

No arquivo `config/backup.php`, o disco deve ser apontado:
```php
'destination' => [
    'disks' => ['s3'],
],
```

## ⏰ Agendamento (Automation)

Os backups estão configurados em `routes/console.php` para ocorrerem diariamente durante a madrugada (Horário do Servidor):

-   **01:00 AM:** Limpeza de backups antigos (`backup:clean`).
-   **02:00 AM:** Execução do novo backup completo (`backup:run`).

```php
// routes/console.php
Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at('02:00');
```

## 🧹 Regras de Retenção (Cleanup)

Para evitar custos excessivos de armazenamento, o sistema mantém os backups seguindo a estratégia de retenção padrão:
-   **Backups Diários:** Mantidos por 16 dias.
-   **Backups Semanais:** Mantidos por 8 semanas.
-   **Backups Mensais:** Mantidos por 4 meses.
-   **Backups Anuais:** Mantidos por 2 anos.

## 🚨 Monitoramento e Alertas

O sistema pode ser configurado para notificar via **E-mail**, **Slack** ou **Discord** em caso de erros na execução.

Para habilitar notificações, configure o canal desejado no arquivo `config/backup.php` na seção `notifications`.

## 📂 Restore (Recuperação)

Em caso de necessidade de restauração:
1.  Localize o arquivo `.zip` no seu disco de backup.
2.  Descompacte o arquivo.
3.  O dump do banco de dados (SQL) estará na pasta `db-dumps`.
4.  Os arquivos estarão na estrutura de diretórios original do projeto.

---

> **Atenção:** Embora o MongoDB seja utilizado para logs de auditoria, ele não está incluído no dump SQL padrão. Para estratégias de backup do MongoDB, consulte a [documentação de integração do MongoDB](mongodb-integration.md).

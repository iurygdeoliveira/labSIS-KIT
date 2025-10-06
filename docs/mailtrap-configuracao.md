# Configuração do Mailtrap no LabSIS-KIT

## 📧 Introdução

O Mailtrap foi integrado ao ambiente de desenvolvimento do LabSIS-KIT para facilitar o teste e desenvolvimento de funcionalidades de email. Esta configuração usa o MailHog, um servidor SMTP local que captura todos os emails enviados pela aplicação.

## 🚀 Como Usar

### 1. **Configuração do .env**

Adicione as seguintes configurações ao seu arquivo `.env`:

```env
# Configuração de Email para Desenvolvimento
MAIL_MAILER=smtp
MAIL_HOST=mailtrap
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@labsis.dev.br"
MAIL_FROM_NAME="${APP_NAME}"

# Configuração do Mailtrap
FORWARD_MAILTRAP_PORT=1025
FORWARD_MAILTRAP_WEB_PORT=8025
```

### 2. **Iniciar o Ambiente**

```bash
# Parar containers existentes
./vendor/bin/sail down

# Iniciar com Mailtrap
./vendor/bin/sail up -d
```

### 3. **Acessar a Interface do Mailtrap**

Após iniciar os containers, acesse:

- **Interface Web**: http://localhost:8025
- **SMTP Server**: localhost:1025

## 🔧 Funcionalidades

### **Captura de Emails**
- Todos os emails enviados pela aplicação são capturados pelo MailHog
- Nenhum email real é enviado durante o desenvolvimento
- Interface web para visualizar emails capturados

### **Teste de Templates**
- Visualize como os emails aparecem em diferentes clientes
- Teste responsividade dos templates
- Verifique anexos e formatação

### **Debug de Emails**
- Logs detalhados de envio
- Informações de headers e destinatários
- Histórico de emails enviados

## 📋 Tipos de Email Disponíveis

### **1. Email de Boas-vindas**
- Enviado automaticamente quando um usuário é cadastrado
- Inclui credenciais de acesso (se fornecidas)
- Link para acessar a plataforma

### **2. Email de Verificação**
- Enviado para usuários com email não verificado
- Link de verificação com expiração de 24 horas
- Instruções claras para o usuário

### **3. Notificação para Administradores**
- Enviado para todos os administradores quando um novo usuário se cadastra
- Informações do novo usuário
- Link direto para gerenciar o usuário

## 🛠️ Comandos Úteis

### **Testar Envio de Email**
```bash
# Via Tinker
./vendor/bin/sail artisan tinker

# Criar usuário de teste
$user = User::factory()->create(['email' => 'teste@exemplo.com']);

# Disparar evento de cadastro
event(new UserRegistered($user, 'senha123'));
```

### **Verificar Emails Capturados**
1. Acesse http://localhost:8025
2. Visualize todos os emails enviados
3. Clique em um email para ver detalhes completos

### **Limpar Emails Capturados**
```bash
# Parar e remover containers
./vendor/bin/sail down

# Remover volumes (cuidado: remove todos os dados)
./vendor/bin/sail down -v

# Reiniciar
./vendor/bin/sail up -d
```

## 🔄 Configurações Avançadas

### **Alterar Portas**
Se as portas padrão estiverem em uso, altere no `.env`:

```env
FORWARD_MAILTRAP_PORT=1026
FORWARD_MAILTRAP_WEB_PORT=8026
```

### **Configuração para Produção**
Para produção, altere as configurações de email:

```env
MAIL_MAILER=smtp
MAIL_HOST=seu-servidor-smtp.com
MAIL_PORT=587
MAIL_USERNAME=seu-usuario
MAIL_PASSWORD=sua-senha
MAIL_ENCRYPTION=tls
```

## 🐛 Troubleshooting

### **Mailtrap não inicia**
```bash
# Verificar logs
./vendor/bin/sail logs mailtrap

# Verificar se as portas estão livres
netstat -tulpn | grep :8025
netstat -tulpn | grep :1025
```

### **Emails não aparecem**
1. Verifique se o Mailtrap está rodando: http://localhost:8025
2. Confirme as configurações no `.env`
3. Verifique os logs da aplicação: `./vendor/bin/sail logs laravel.test`

### **Erro de conexão SMTP**
```bash
# Testar conexão SMTP
telnet localhost 1025

# Verificar configuração
./vendor/bin/sail artisan config:show mail
```

## 📚 Recursos Adicionais

- [Documentação do MailHog](https://github.com/mailhog/MailHog)
- [Configuração de Email no Laravel](https://laravel.com/docs/mail)
- [Sistema de Emails do LabSIS-KIT](./sistema-emails.md)

## ✅ Benefícios

1. **Desenvolvimento Seguro**: Nenhum email real é enviado
2. **Teste Fácil**: Interface visual para verificar emails
3. **Debug Eficiente**: Logs detalhados de envio
4. **Templates Responsivos**: Visualize como emails aparecem
5. **Integração Perfeita**: Funciona automaticamente com o sistema de emails

---

**Nota**: Esta configuração é apenas para desenvolvimento. Em produção, configure um servidor SMTP real.

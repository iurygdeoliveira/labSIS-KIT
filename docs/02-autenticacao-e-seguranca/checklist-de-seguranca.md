# Checklist de Segurança e Diretrizes de Mitigação

Este documento descreve uma lista de verificações de segurança essenciais e diretrizes para mitigação de riscos, focada em auxiliar no desenvolvimento seguro de aplicações.

## 1. Ataques de Injeção (Injection)

-   **O Risco:** Ocorrem quando dados não confiáveis são enviados a um interpretador como parte de um comando ou consulta (ex: SQL, NoSQL, OS). Permite que atacantes acessem, modifiquem ou destruam dados e, em alguns casos, obtenham controle do servidor.
-   **Diretriz de Mitigação para IA:** "Refatore todo o código de acesso a dados para utilizar exclusivamente consultas parametrizadas (prepared statements) ou um ORM que realize o escape de forma segura. Valide e sanitize rigorosamente todos os inputs do usuário."

## 2. Falhas de Autenticação (Broken Authentication)

-   **O Risco:** Funções de autenticação e gerenciamento de sessão implementadas incorretamente permitem que atacantes comprometam senhas, chaves ou tokens, possibilitando o acesso não autorizado a contas.
-   **Diretriz de Mitigação para IA:** "Desenvolva um módulo de autenticação que implemente hashing de senhas adaptativo e salgado (ex: Argon2, Bcrypt), políticas de senhas fortes, proteção contra força bruta (rate limiting) e um gerenciamento seguro de tokens JWT."

## 3. Quebra de Controle de Acesso (Broken Access Control)

-   **O Risco:** Falha crítica em sistemas multi-tenant, ocorrendo quando as restrições sobre o que um usuário autenticado pode fazer não são devidamente aplicadas, permitindo que usuários acessem dados de outras contas (IDOR).
-   **Diretriz de Mitigação para IA:** "Implemente um middleware de autorização centralizado que verifique, em cada endpoint, se o usuário autenticado possui as permissões necessárias para acessar o recurso específico, aplicando o princípio de negação por padrão."

## 4. Configuração Insegura de Segurança (Security Misconfiguration)

-   **O Risco:** Resulta da falta de "hardening" de segurança na stack tecnológica, incluindo configurações padrão, permissões de nuvem excessivas, mensagens de erro verbosas e cabeçalhos de segurança HTTP ausentes.
-   **Diretriz de Mitigação para IA:** "Gere um checklist de 'hardening' para a minha stack de tecnologia ([especificar: ex: Node.js, Nginx, PostgreSQL, Docker]), incluindo a desativação de funcionalidades desnecessárias e a configuração de cabeçalhos de segurança HTTP recomendados."

## 5. Uso de Componentes com Vulnerabilidades Conhecidas

-   **O Risco:** Depender de bibliotecas e frameworks de terceiros com vulnerabilidades publicamente conhecidas (CVEs). Um atacante pode explorar uma falha em uma dependência desatualizada para comprometer toda a aplicação.
-   **Diretriz de Mitigação para IA:** "Configure um pipeline de CI/CD que inclua uma etapa de Análise de Composição de Software (SCA) usando Snyk ou OWASP Dependency-Check para bloquear a implantação de código com vulnerabilidades críticas."

## 6. Cross-Site Scripting (XSS)

-   **O Risco:** Permite que um atacante execute scripts maliciosos no navegador da vítima, podendo resultar no roubo de sessões (cookies), desfiguração de sites ou redirecionamento para páginas maliciosas.
-   **Diretriz de Mitigação para IA:** "Implemente a codificação de saída (output encoding) contextual em todas as variáveis renderizadas no frontend e utilize uma Política de Segurança de Conteúdo (CSP) restritiva para mitigar o impacto de qualquer possível injeção."

## 7. Falhas na Lógica de Negócio (Business Logic Flaws)

-   **O Risco:** Abuso das funcionalidades da aplicação de maneiras não previstas, explorando fluxos de trabalho legítimos para obter vantagens indevidas, como a manipulação de preços em um carrinho de compras.
-   **Diretriz de Mitigação para IA:** "Analise o seguinte fluxo de negócio [descreva o fluxo]. Identifique e liste possíveis cenários de abuso e falhas lógicas que poderiam ser exploradas por um usuário mal-intencionado, pensando em condições de corrida e manipulação de parâmetros."

## 8. Server-Side Request Forgery (SSRF)

-   **O Risco:** Uma falha que força o servidor da aplicação a fazer requisições HTTP para um domínio arbitrário. Em ambientes de nuvem, pode ser usado para escanear a rede interna ou acessar APIs de metadados e roubar credenciais.
-   **Diretriz de Mitigação para IA:** "Crie uma função segura para buscar recursos de uma URL fornecida pelo usuário. A função deve validar a URL contra uma lista de permissões (allow-list) de domínios, protocolos e portas, e nunca deve seguir redirecionamentos."

## 9. Logging e Monitoramento Insuficientes

-   **O Risco:** Sem um registro adequado de eventos e um monitoramento ativo, as organizações não conseguem detectar uma violação em tempo hábil, aumentando o tempo de permanência e o dano causado por um atacante.
-   **Diretriz de Mitigação para IA:** "Defina uma política de logging para a aplicação, especificando quais eventos de segurança devem ser registrados, o formato e as informações a serem incluídas. Integre esses logs a um sistema de SIEM para monitoramento e alerta."

## 10. Cross-Site Request Forgery (CSRF)

-   **O Risco:** Engana um usuário autenticado para que ele submeta, sem intenção, uma requisição que altera estado (ex: mudar senha, transferir fundos) em uma aplicação onde ele está logado.
-   **Diretriz de Mitigação para IA:** "Implemente uma defesa contra CSRF utilizando o padrão de token anti-CSRF (Synchronizer Token Pattern) para todas as requisições que alteram estado. Garanta que os cookies de sessão utilizem o atributo SameSite=Strict ou Lax."

## 11. Injeção de Prompt (Prompt Injection)

-   **O Risco:** Específico para aplicações que integram Grandes Modelos de Linguagem (LLMs). Um atacante insere instruções maliciosas no input que são interpretadas pelo LLM como parte do prompt original do sistema.
-   **Diretriz de Mitigação para IA:** "Desenhe uma arquitetura de defesa em camadas para a integração com o LLM. Isso deve incluir: (1) uma clara separação entre a instrução do sistema e o input do usuário usando delimitadores, (2) validação e sanitização rigorosa do input antes de enviá-lo ao LLM, (3) a aplicação do Princípio do Menor Privilégio nas ferramentas/APIs que o LLM pode invocar, e (4) um monitoramento para detectar outputs anômalos ou inesperados."

## 12. Desserialização Insegura (Insecure Deserialization)

-   **O Risco:** Ocorre quando dados serializados não confiáveis são desserializados sem validação. Isso pode levar à execução remota de código (RCE), ataques de negação de serviço e bypass de autenticação.
-   **Diretriz de Mitigação para IA:** "Revise todo o código que realiza desserialização de objetos. Priorize o uso de formatos de dados simples, como JSON, e evite formatos complexos que permitem a instanciação de tipos de objetos arbitrários. Se a desserialização for necessária, implemente verificações de integridade e restrinja os tipos de objetos que podem ser recriados."

## 13. Entidades Externas de XML (XML External Entity - XXE)

-   **O Risco:** Aplicações que processam XML podem ser vulneráveis a ataques que exploram a especificação de entidades externas no documento.
-   **Diretriz de Mitigação para IA:** "Configure o parser de XML da aplicação para desabilitar o suporte a Entidades Externas (External DTDs) e DOCTYPEs. Analise o código e me mostre como aplicar essa configuração de segurança para a biblioteca [nome da biblioteca XML, ex: libxml em PHP, ElementTree em Python]."

## 14. Ausência de Limitação de Requisições (Rate Limiting)

-   **O Risco:** A falta de controle sobre a frequência com que um usuário ou um IP pode fazer requisições. A ausência de "rate limiting" abre portas para ataques de força bruta e DoS.
-   **Diretriz de Mitigação para IA:** "Implemente um middleware de 'rate limiting' usando o algoritmo de Token Bucket. Aplique limites diferentes para endpoints distintos: um limite mais restrito para login e recuperação de senha, e um limite mais geral para o uso da API principal."

## 15. Exposição de Dados Sensíveis (Cryptographic Failures)

-   **O Risco:** Envolve a proteção inadequada de dados sensíveis, como senhas e tokens.
-   **Diretriz de Mitigação para IA:** "Analise o código em busca de dados sensíveis armazenados em texto claro, uso de algoritmos criptográficos fracos (como MD5 ou SHA1) e chaves de criptografia hardcoded. Assegure que o TLS esteja configurado corretamente para todos os dados em trânsito."

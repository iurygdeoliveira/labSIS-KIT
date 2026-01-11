# Comandos de Qualidade e Finalização
 
Sempre executar antes de finalizar uma tarefa:
 
1. **Formatação**: `vendor/bin/sail bin pint --dirty`
2. **Análise Estática**: `./vendor/bin/phpstan` (ou via composer se configurado)
3. **Refatoração**: `vendor/bin/sail artisan rector` (dry-run primeiro se necessário)
4. **Testes**: `vendor/bin/sail artisan test`
5. **Upgrade de Ativos**: `vendor/bin/sail artisan filament:upgrade`

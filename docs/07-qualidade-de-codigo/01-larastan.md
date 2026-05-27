# Larastan: Garantindo a Integridade Técnica 🛡️

O Larastan é uma ferramenta de análise estática para o ecossistema Laravel, construída sobre o PHPStan. No **labSIS-KIT**, ele desempenha um papel fundamental na manutenção de uma base de código robusta, livre de bugs silenciosos e preparada para escalabilidade.

## Por que o Larastan é Vital?

Diferente de testes automatizados que validam o _comportamento_ em tempo de execução, o Larastan valida a _estrutura_ do código sem executá-lo. Isso traz benefícios imediatos:

1. **Detecção de Bugs antes da Produção**: Identifica acessos a propriedades inexistentes, chamadas de métodos em objetos nulos e incompatibilidades de tipos que causariam erros fatais em runtime.
2. **Documentação de Tipagem**: Obriga o desenvolvedor a declarar explicitamente o que cada método recebe e retorna, tornando o código autoexplicativo.
3. **Refatoração Segura**: Ao alterar o nome de um método ou propriedade, o Larastan aponta instantaneamente todos os lugares que "quebraram", garantindo que nenhuma ponta fique solta.
4. **Padronização para SaaS**: Em sistemas multi-team complexos, a certeza de que um objeto é realmente um `User` ou um `Team` evita vazamentos de dados e falhas de autorização.

---

## Como Utilizar no labSIS-KIT

### 1. Executando a Análise

Para rodar a verificação completa em todo o projeto, utilize o comando:

```bash
./vendor/bin/sail php ./vendor/bin/phpstan analyse
```

_Dica: Você pode rodar em arquivos específicos para agilizar: `./vendor/bin/sail php ./vendor/bin/phpstan analyse app/Models/User.php`._

### 2. Alcançando a "Análise Limpa"

O objetivo do projeto é sempre manter **zero erros**. Sempre que criar um novo Model, Controller ou Página do Filament, rode a análise.

### 3. Técnicas Utilizadas no Projeto

#### Tipagem Forte em Recordes (Filament)

Ao trabalhar com páginas de visualização ou edição do Filament, o Larastan pode não saber exatamente qual o tipo do `$record`. Use `instanceof` para garantir a tipagem:

```php
public function afterSave(): void
{
    $record = $this->getRecord();

    if ($record instanceof \App\Models\User) {
        // Agora o Larastan sabe que $record tem o método teams()
        $record->teams()->sync(...);
    }
}
```

#### PHPDoc para Propriedades Computadas

Sinalize propriedades que não são óbvias para o analisador:

```php
/**
 * @property-read \App\Models\User|null $record
 * @property-read bool $canSuspend
 */
class ViewUser extends ViewRecord { ... }
```

#### Tipos Genéricos em Relações

No Model, informe ao Larastan o que as relações retornam:

```php
/**
 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Team, \App\Models\User>
 */
public function teams(): BelongsToMany { ... }
```

---

## Configuração

A configuração do Larastan reside no arquivo `phpstan.neon` na raiz do projeto. Nela, definimos o nível de rigor (Level 5 ou superior) e os diretórios analisados.

> [!IMPORTANT]
> Use o `@phpstan-ignore` apenas em casos extremos onde a ferramenta apresenta um falso positivo e você tem certeza absoluta da segurança do código. Sempre adicione um comentário explicando o motivo.

## Referências

- [Configuração: PHPStan](../../phpstan.neon)
- [Model: User](../../app/Models/User.php)
- [Filacheck Pro](./04-filacheck.md)

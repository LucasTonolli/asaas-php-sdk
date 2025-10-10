# Value Objects (VO)

Os **Value Objects** representam valores imutáveis e autocontidos do domínio, como `Cpf`, `Email` ou `Phone`.  
Eles garantem **validação**, **formatação** e **comparação** consistentes, mantendo a lógica de dados centralizada e reutilizável.

---

## 📌 Contratos

- `ValueObjectContract` → interface base para todos os VOs

  - Métodos obrigatórios:
    - `value(): string` → retorna o valor cru
    - `equals(self $other): bool` → comparação entre VOs
  - Também implementa `JsonSerializable` e `Stringable`

- `FormattableContract` → usado por VOs que possuem um formato de exibição amigável (`formatted(): string`)

---

## 🧠 Trait `StringValueObject`

Para VOs que validam e armazenam uma `string`, utiliza-se a trait `StringValueObject`

## 🧱 Estrutura

### Namespace

    AsaasPhpSdk\ValueObjects

### Localização

    src/ValueObjects/{NomeDoVO}.php

### Nomeação

    PascalCase (ex: `Cpf`, `Email`, `Phone`)

```
<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Contracts\ValueObjectContract;
use AsaasPhpSdk\Contracts\FormattableContract;
use AsaasPhpSdk\Traits\StringValueObject; // Reutilização

// final: Garante que não pode ser estendido
final class Cpf implements ValueObjectContract, FormattableContract
{
    // Trait cuida da implementação básica de value(), equals(), etc.
    use StringValueObject;

    // Construtor é privado para forçar o uso do `from()`
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    // Método estático para validação e construção
    public static function from(string $cpf): self
    {
        $sanitized = preg_replace('/\D/', '', $cpf);

        if (strlen($sanitized) !== 11) {
            throw new \InvalidArgumentException('CPF must contain 11 digits');
        }

        // Validação de algoritmo do CPF (exemplo)
        // if (! self::isValid($sanitized)) { ... }

        return new self($sanitized);
    }

    // Implementação do FormattableContract
    public function formatted(): string
    {
        return preg_replace(
            "/(\d{3})(\d{3})(\d{3})(\d{2})/",
            "$1.$2.$3-$4",
            $this->value
        );
    }
}
```

## 🧭 Boas práticas

### ✅ Imutabilidade:

    VO não deve ter setters.
    Toda modificação cria uma nova instância.

### ✅ Validação no from():

    Toda a lógica de validação deve ficar concentrada no método from.

### ✅ Uso em DTOs e Actions:

    Utilize VOs em DTOs para garantir que dados inválidos nunca cheguem à camada de integração ou negócio.

### ✅ Formato separado do valor:

    Se precisar exibir de forma amigável, implemente FormattableContract e não altere o valor interno.

### ✅ Testes unitários dedicados:

    Cada VO deve ter testes isolados (ex: tests/ValueObjects/CpfTest.php).

### ✅ Reutilização de trait:

    Use StringValueObject sempre que possível para manter consistência e reduzir código repetido.

### ❌ Evite lógica de negócio dentro dos VOs.

    Eles devem representar apenas valor + regras do valor, nunca comportamento do domínio.

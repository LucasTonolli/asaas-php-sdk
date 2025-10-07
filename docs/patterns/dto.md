# 📨 Data Transfer Objects (DTO)

Os **DTOs (Data Transfer Objects)** são responsáveis por transportar dados entre as camadas da aplicação de forma **estruturada**, **tipada** e **imutável**.
Eles **não** contêm lógica de negócio, servindo como “contratos de dados” entre camadas (ex: Controller → Action, Action → SDK, etc.).

---

## 📌 Contratos

- **Imutabilidade** → DTOs devem ser criados através de métodos estáticos (`fromArray`) e não podem ser alterados após a criação.
- **Conversão consistente** → Devem possuir métodos claros para conversão de/para arrays, garantindo fácil serialização e integração.
- **Sanitização centralizada** → Dados brutos devem ser validados e normalizados no momento da criação.

---

## 🧠 Métodos Comuns

| Método                   | Responsabilidade                                                                |
| ------------------------ | ------------------------------------------------------------------------------- |
| `fromArray(array $data)` | Cria uma instância a partir de dados brutos (ex: requests ou payloads externos) |
| `toArray(): array`       | Converte o DTO em um array limpo, pronto para transporte ou integração externa  |
| `sanitize(array $data)`  | (Privado) Normaliza e valida os dados de entrada antes da construção do DTO     |

---

## 🧱 Estrutura

### 📂 **Namespace**

```
AsaasPhpSdk\DTOs
```

### 📁 **Localização**

```
src/DTOs/{Recurso}/{Verbo}{Recurso}DTO.php
```

### 🧾 **Nomeação**

- Utilize **PascalCase** (ex: `CreateCustomerDTO`, `ListCustomersDTO`).
- O nome deve indicar claramente a **ação** ou **contexto** que representa.

---

## ✍️ Exemplo de Implementação

```php
namespace AsaasPhpSdk\DTOs;

use AsaasPhpSdk\ValueObjects\Email;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\Support\DataSanitizer;

class ListCustomersDTO
{
    private function __construct(
        public readonly ?int $offset = null,
        public readonly ?int $limit = null,
        public readonly ?string $name = null,
        public readonly ?Email $email = null,
        public readonly Cpf|Cnpj|null $cpfCnpj = null,
        public readonly ?string $groupName = null,
        public readonly ?string $externalReference = null
    ) {}

    public static function fromArray(array $data): self
    {
        $sanitized = self::sanitize($data);
        return new self(...$sanitized);
    }

    public function toArray(): array
    {
        return array_filter([
            'offset' => $this->offset,
            'limit' => $this->limit,
            'name' => $this->name,
            'email' => $this->email?->value(),
            'cpfCnpj' => $this->cpfCnpj?->value(),
            'groupName' => $this->groupName,
            'externalReference' => $this->externalReference,
        ], fn($value) => $value !== null);
    }

    private static function sanitize(array $data): array
    {
        return [
            'offset' => DataSanitizer::sanitizeInteger($data['offset'] ?? null),
            'limit' => DataSanitizer::sanitizeInteger($data['limit'] ?? null),
            'name' => DataSanitizer::sanitizeString($data['name'] ?? null),
            'email' => self::sanitizeEmail($data['email'] ?? null),
            'cpfCnpj' => self::sanitizeCpfCnpj($data['cpfCnpj'] ?? null),
            'groupName' => DataSanitizer::sanitizeString($data['groupName'] ?? null),
            'externalReference' => DataSanitizer::sanitizeString($data['externalReference'] ?? null),
        ];
    }

    private static function sanitizeEmail(?string $email): ?Email
    {
        if ($email === null) {
            return null;
        }

        try {
            return Email::from($email);
        } catch (\Exception) {
            return null;
        }
    }

    private static function sanitizeCpfCnpj(?string $cpfCnpj): Cpf|Cnpj|null
    {
        if ($cpfCnpj === null) {
            return null;
        }

        $sanitized = DataSanitizer::onlyDigits($cpfCnpj);

        if ($sanitized === null) {
            return null;
        }

        return match (strlen($sanitized)) {
            11 => Cpf::from($sanitized),
            14 => Cnpj::from($sanitized),
            default => null
        };
    }
}
```

---

## 🧭 Boas Práticas

### ✅ **Imutabilidade**

- Use `readonly` nas propriedades.
- Construtores devem ser `private` e controlados por métodos estáticos.

### ✅ **Uso de VOs**

- Utilize `Value Objects` dentro de DTOs para validação e consistência de dados.

### ✅ **Conversão clara**

- Sempre ofereça `toArray()` para fácil serialização (ex: requisições HTTP, logs, integrações).

### ✅ **Responsabilidade única**

- O DTO **não** deve conter regras de negócio, apenas transporte e validação/sanitização leve.

### ❌ **Evite setters ou métodos mutáveis**

- Isso quebraria a imutabilidade e dificultaria rastrear o estado do objeto.

# 📨 Data Transfer Objects (DTO)

Os **DTOs (Data Transfer Objects)** são responsáveis por transportar dados entre as camadas da aplicação de forma **estruturada**, **tipada** e **imutável**. Eles **não** contêm lógica de negócio, servindo como “contratos de dados” entre camadas (ex: Controller → Action, Action → SDK, etc.).

---

## 📌 Princípios Fundamentais

Com base no seu uso, podemos identificar dois tipos principais de DTOs:

1.  **DTOs de Mutação (Strict)**: Usados para **criar** ou **atualizar** recursos (ex: `CreateCustomerDTO`). São rigorosos: dados inválidos ou ausentes devem lançar exceções específicas para garantir a integridade total dos dados.
2.  **DTOs de Consulta (Lenient)**: Usados para **filtrar** ou **listar** recursos (ex: `ListCustomersDTO`). São mais permissivos: dados inválidos ou ausentes são convertidos para `null`, permitindo buscas flexíveis sem interromper o fluxo.

---

## 🧠 Ciclo de Vida e Métodos

Um DTO robusto segue um ciclo de vida claro, orquestrado pelo método estático `fromArray`.

| Método                   | Responsabilidade                                                                                                                                                                 |
| :----------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `fromArray(array $data)` | **Ponto de entrada público**. Orquestra o fluxo de sanitização e validação para criar uma instância válida do DTO.                                                               |
| `sanitize(array $data)`  | **(Protegido)** Primeira etapa. **Prepara e normaliza** os dados de entrada (ex: remove caracteres, ajusta tipos) antes da validação. Não lança exceções.                        |
| `validate(array $data)`  | **(Privado)** Segunda etapa. **Valida as regras** e a integridade dos dados já sanitizados, **lançando exceções** em caso de falha. É aqui que `Value Objects` são instanciados. |
| `toArray(): array`       | Converte o DTO em um array limpo, pronto para transporte ou integração externa (ex: payload de API).                                                                             |

---

## 🧱 Estrutura e Arquitetura

### 🧾 **Convenções**

- **Namespace**: `AsaasPhpSdk\DTOs`
- **Localização**: `src/DTOs/{Recurso}/{Verbo}{Recurso}DTO.php`
- **Nomeação**: **PascalCase**, indicando a ação (ex: `CreateCustomerDTO`).

### 🛠️ **Arquitetura de Suporte**

Para garantir consistência e evitar repetição de código, a estrutura de DTOs se apoia em dois componentes centrais:

#### **1. `DTOContract` (Interface)**

É o contrato que **garante a API pública** de todos os DTOs. Ao forçar a implementação dos métodos `fromArray()` e `toArray()`, ele assegura que qualquer DTO no sistema possa ser construído e serializado de forma previsível.

```php
interface DTOContract
{
    public static function fromArray(array $data): self;
    public function toArray(): array;
}
```

#### **2. `AbstractDTO` (Classe Abstrata)**

É a base que fornece a **lógica reutilizável** para a maioria dos DTOs. Suas principais responsabilidades são:

- **Conversão Inteligente (`toArray`)**: Implementa um método `toArray()` genérico usando Reflection. Ele automaticamente converte as propriedades públicas do DTO em um array, tratando `Value Objects` de forma inteligente:

  - Se um VO tiver o atributo `#[ToArrayMethodAttribute('metodo')]`, ele chamará `->metodo()`.
  - Caso contrário, tentará chamar o método padrão `->value()`.
  - Propriedades com valor `null` são omitidas do resultado.

- **Helpers de Validação (`validateValueObject`)**: Oferece um método robusto para tentar instanciar um `Value Object`. Se a criação falhar, ele lança uma `InvalidValueObjectException` padronizada, simplificando o bloco `validate()` dos DTOs filhos.

- **Helpers de Sanitização (`optional...`)**: Fornece uma série de métodos (`optionalString`, `optionalOnlyDigits`, etc.) que simplificam a sanitização de dados opcionais, tornando o método `sanitize()` dos filhos mais limpo e legível.

- **Forçar Implementação (`abstract sanitize`)**: Declara o método `sanitize()` como abstrato, **obrigando** cada DTO filho a implementar suas próprias regras de normalização de dados.

---

## ✍️ Exemplos de Implementação

### Exemplo 1: DTO de Mutação (Strict)

Usa os helpers de `AbstractDTO` para validar e construir o objeto, lançando exceções se os dados forem inválidos.

```php
// Herda a lógica de toArray() e os helpers
final class CreateCustomerDTO extends AbstractDTO
{
    private function __construct(/*...propriedades...*/) {}

    public static function fromArray(array $data): self
    {
        $sanitizedData = self::sanitize($data);
        $validatedData = self::validate($sanitizedData);
        return new self(...$validatedData);
    }

    // Obrigatório pela classe abstrata
    protected static function sanitize(array $data): array
    {
        return [
            // Usa os helpers para simplificar
            'name' => DataSanitizer::sanitizeString($data['name'] ?? ''),
            'postalCode' => self::optionalOnlyDigits($data, 'postalCode'),
            // ...
        ];
    }

    private static function validate(array $data): array
    {
        if (empty($data['name'])) {
            throw InvalidCustomerDataException::missingField('name');
        }

        // Usa o helper para validar VOs de forma padronizada
        self::validateValueObject($data, 'postalCode', PostalCode::class);

        return $data;
    }
}
```

### Exemplo 2: DTO de Consulta/Filtro (Lenient)

Usado para filtrar uma lista. Campos inválidos são silenciosamente convertidos para `null` para não quebrar a busca.

```php
class ListCustomersDTO extends AbstractDTO
{
    // ...
    // Neste caso, o fromArray pode pular a etapa de validação rigorosa
    public static function fromArray(array $data): self
    {
        $sanitized = self::sanitize($data);
        return new self(...$sanitized);
    }

    protected static function sanitize(array $data): array
    {
        return [
            'limit' => self::optionalInteger($data, 'limit'),
            'email' => self::optionalEmail($data['email'] ?? null),
            // ...
        ];
    }
}
```

---

## 🧭 Boas Práticas

- ✅ **Imutabilidade**: Use `readonly` e construtores privados.
- ✅ **Uso de VOs**: Incorpore `Value Objects` para validação em nível de campo.
- ✅ **Exceções Específicas**: Em DTOs de mutação, lance exceções de domínio claras.
- ✅ **Conversão Clara**: Use `toArray()` para serialização e atributos para customizar a conversão de VOs.
- ✅ **Responsabilidade Única**: O DTO valida **estrutura e formato**, não regras de negócio complexas.
- ❌ **Evite Setters**: Nunca permita a alteração de um DTO após sua criação.

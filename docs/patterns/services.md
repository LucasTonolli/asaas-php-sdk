# ⚙️ Services

Os **Services** são a principal interface de interação com o SDK. Eles atuam como uma fachada (_Façade_) que agrupa todas as operações disponíveis para um recurso específico da API, como `Customer`, `Payment`, `Subscription`, etc.

O objetivo de um `Service` é fornecer uma **API pública, coesa e fácil de usar**, abstraindo do usuário final a complexidade e a existência das camadas internas de `DTOs` e `Actions`.

---

## 📌 Estrutura e Convenções

- **Padrão:** `{Recurso}Service` (ex: `CustomerService`, `PaymentService`)
- **Namespace:** `AsaasPhpSdk\Services`
- **Localização:** `src/Services/{Recurso}Service.php`

---

## 🧭 Princípios de Design

1.  **Agrupamento por Recurso:** Cada classe de `Service` é responsável por gerenciar o ciclo de vida de um único recurso da API. Todas as operações relacionadas a "Clientes", por exemplo, estão centralizadas no `CustomerService`.

2.  **Interface Simplificada:** Para facilitar o uso do SDK, os métodos do `Service` recebem e manipulam dados brutos, como `arrays` e `strings`. A responsabilidade de transformar esses dados em `DTOs` tipados e validados é **interna** ao `Service`.

3.  **Delegação para Actions:** Um `Service` **não contém a lógica** para executar a chamada HTTP. Sua função é orquestrar o fluxo:

    - Receber os dados brutos do usuário.
    - Criar a instância do `DTO` apropriado.
    - Instanciar a `Action` correspondente.
    - Delegar a execução para o método `handle()` da `Action`.
    - Retornar o resultado.

4.  **Injeção de Dependências:** As dependências necessárias, como o `Client` HTTP e o `ResponseHandler`, são injetadas via construtor para facilitar a testabilidade e a manutenção.

---

### ✅ Exemplo - `CustomerService.php`

```php
namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Customers\{CreateCustomerAction, GetCustomerAction, ListCustomersAction};
use AsaasPhpSdk\DTOs\Customers\{CreateCustomerDTO, ListCustomersDTO};
use AsaasPhpSdk\Exceptions\ValidationException;
use GuzzleHttp\Client;
use AsaasPhpSdk\Helpers\ResponseHandler;

final class CustomerService
{
    // 1. Dependências são injetadas
    public function __construct(private Client $client, private readonly ResponseHandler $responseHandler = new ResponseHandler) {}

    /**
     * Cria um novo cliente.
     *
     * @param  array  $data Dados do cliente.
     * @return array Dados do cliente criado.
     */
    public function create(array $data): array
    {
        // 2. Cria o DTO internamente, tratando exceções de validação
        $dto = $this->createDTO(CreateCustomerDTO::class, $data);

        // 3. Instancia a Action específica para a operação
        $action = new CreateCustomerAction($this->client, $this->responseHandler);

        // 4. Delega a execução para a Action e retorna o resultado
        return $action->handle($dto);
    }

    /**
     * Obtém um cliente pelo ID.
     *
     * @param  string  $id ID do cliente.
     * @return array Dados do cliente.
     */
    public function get(string $id): array
    {
        // Para operações simples, pode instanciar e chamar a Action diretamente
        $action = new GetCustomerAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Método helper para criar DTOs com tratamento de erro consistente.
     */
    private function createDTO(string $dtoClass, array $data): object
    {
        try {
            return $dtoClass::fromArray($data);
        } catch (\AsaasPhpSdk\Exceptions\InvalidCustomerDataException $e) {
            // Converte uma exceção interna em uma exceção pública da SDK
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    // ... outros métodos (list, update, delete, etc.)
}
```

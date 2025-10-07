# Actions

As **Actions** representam operações específicas do domínio ou integrações externas.  
Devem seguir uma estrutura clara e previsível para facilitar leitura e reuso.

## 📌 Nomeação

- **Padrão:** `{Verbo}{Recurso}Action`
- **Namespace:** `App\Actions\{Recurso}`
- **Localização:** `src/Actions/{Recurso}/{Verbo}{Recurso}Action.php`

## 🧭 Regras

- Todas as Actions devem **estender `AbstractAction`**
- Todas as Actions devem **utilizar um DTO** como parâmetro de entrada
- O método principal deve se chamar `handle()`

### ✅ Exemplo

```php
namespace App\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;

class CreateCustomerAction extends AbstractAction
{
    public function handle(CreateCustomerDTO $data): array
    {
        return $this->executeRequest(
            fn() => $this->client->post('customers', [
                'json' => $data->toArray(),
            ])
        );
    }
}
```

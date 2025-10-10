# 🆘 Exceptions

A estratégia de tratamento de erros do SDK foi projetada para ser **previsível, específica e informativa**. Em vez de usar exceções genéricas, o SDK lança exceções customizadas e tipadas que permitem ao desenvolvedor criar blocos `catch` precisos e tratar cada tipo de falha de maneira apropriada.

O objetivo é dar ao usuário do SDK o máximo de contexto possível sobre o que deu errado, seja um erro de validação local ou uma falha retornada pela API.

---

## 🌳 Hierarquia de Exceções

Todas as exceções lançadas pelo SDK herdam de uma classe base comum, a `AsaasException`. Isso permite que você capture qualquer erro originado do SDK com um único bloco `catch`. A hierarquia foi pensada para permitir a captura de erros tanto de forma específica quanto categorizada.

A estrutura geral é a seguinte:

- `\Throwable` (Nativa do PHP)
  - `AsaasException` (**Base para todas as exceções do SDK**)
    - `ApiException` (Erros genéricos da API ou 5xx)
    - `AuthenticationException` (Erro 401 - Token inválido)
    - `NotFoundException` (Erro 404 - Recurso não encontrado)
    - `RateLimitException` (Erro 429 - Limite de requisições excedido)
    - `ValidationException` (Erro 400 - Erros de validação retornados pela API)
    - `InvalidCustomerDataException` (Erro de validação de dados do cliente)
    - `InvalidValueObjectException` (**Base para erros de VOs**)
      - `InvalidCpfException`
      - `InvalidCnpjException`
      - `InvalidEmailException`
      - etc...

---

## 📚 Tipos de Exceções

As exceções podem ser divididas em duas categorias principais, dependendo de **quando** elas ocorrem:

### 1\. Erros de Validação (Pré-Requisição)

Ocorrem **antes** de qualquer chamada à API ser feita, geralmente durante a criação de `DTOs` ou `Value Objects`. Eles indicam que os dados fornecidos pelo usuário não estão em um formato válido.

- `InvalidCustomerDataException`
- `InvalidValueObjectException` (Exceção genérica para VOs)
  - `InvalidCpfException`, `InvalidCnpjException`, `InvalidEmailException`, `InvalidPhoneException`, etc. (Exceções específicas que herdam de `InvalidValueObjectException`)

A vantagem dessa hierarquia é que você pode capturar um erro específico (`catch (InvalidCpfException $e)`) ou qualquer erro de validação de VO (`catch (InvalidValueObjectException $e)`).

### 2\. Erros da API (Pós-Requisição)

São lançados pelo `ResponseHandler` quando a API do Asaas retorna um `status code` de erro (4xx ou 5xx). Eles representam uma falha na comunicação ou no processamento da requisição pela API.

- `AuthenticationException` (Status 401)
- `NotFoundException` (Status 404)
- `RateLimitException` (Status 429)
- `ValidationException` (Status 400)
- `ApiException` (Status 5xx ou outros erros não mapeados)

---

## ✨ Exceções com Contexto Adicional

Algumas exceções foram enriquecidas com métodos que fornecem dados extras para facilitar o tratamento do erro.

### `ValidationException`

Quando a API retorna um erro de validação (400), esta exceção carrega um array detalhado com os campos e as mensagens de erro.

```php
try {
    $asaas->customer->create(['name' => '']); // Nome é obrigatório
} catch (ValidationException $e) {
    echo "Erro de validação: " . $e->getMessage() . "\n";

    // getErrors() retorna um array com os detalhes
    // Ex: [['code' => 'invalid_field', 'description' => 'name is required']]
    foreach ($e->getErrors() as $error) {
        echo "- " . $error['description'] . "\n";
    }
}
```

### `RateLimitException`

Quando o limite de requisições é atingido (429), esta exceção informa por quantos segundos você deve esperar antes de tentar novamente, através do header `Retry-After` da API.

```php
try {
    // ...faz muitas requisições em um curto período...
} catch (RateLimitException $e) {
    echo $e->getMessage() . "\n";

    // getRetryAfter() retorna o tempo de espera em segundos
    if ($retryAfter = $e->getRetryAfter()) {
        echo "Aguardando {$retryAfter} segundos para tentar novamente...\n";
        sleep($retryAfter);
    }
}
```

---

## ✍️ Boas Práticas de Implementação

Ao criar novas exceções para o SDK, siga estas diretrizes:

- **Hierarquia Consistente**: Toda exceção deve herdar de `AsaasException`. Exceções de `Value Objects` devem herdar da classe base `InvalidValueObjectException`.
- **Encapsulamento com `Throwable`**: A `AsaasException` utiliza `?Throwable` em seu construtor, permitindo encapsular qualquer tipo de erro do PHP (`Exception` ou `Error`) para manter o contexto completo da falha.
- **Construtores Estáticos**: Para erros de validação com mensagens padronizadas, use construtores estáticos como em `InvalidCustomerDataException::missingField('name')`.
- **Enriquecimento com Contexto**: Se uma exceção pode carregar dados úteis para o usuário (como a `RateLimitException`), adicione propriedades `readonly` no construtor e um `getter` para expor essa informação.

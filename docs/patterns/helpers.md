# 🛠️ Helpers

A camada de **Helpers** é composta por um conjunto de classes utilitárias, **stateless** (sem estado) e reutilizáveis, que fornecem suporte para as principais camadas do SDK (`Services`, `Actions`, `DTOs`).

Elas encapsulam lógicas de "baixo nível" e tarefas transversais (como sanitização, configuração de HTTP e tratamento de respostas), mantendo o resto do código limpo e focado em suas responsabilidades de negócio.

---

## 🧭 Princípios Fundamentais

- **Responsabilidade Única:** Cada classe `Helper` tem um propósito claro e bem definido. `DataSanitizer` só sanitiza dados, `HttpClientFactory` só cria clientes HTTP.
- **Sem Estado (Stateless):** Helpers não armazenam informações entre chamadas. Seus métodos operam apenas com os dados que recebem como entrada, e por isso são, em sua maioria, estáticos.
- **Reutilização:** São projetados para serem usados em múltiplos contextos dentro do SDK.
- **Abstração de Complexidade:** Eles escondem detalhes de implementação complexos, como a configuração de _middlewares_ do Guzzle ou a lógica de parsing de respostas de erro da API.

---

## 💡 Exemplos de Helpers no SDK

O SDK utiliza alguns `Helpers` chave para garantir seu funcionamento robusto e consistente.

### 1\. `DataSanitizer`

Esta classe é uma biblioteca de métodos estáticos e puros, focada em limpar e normalizar dados brutos. É amplamente utilizada dentro dos DTOs para garantir que os dados estejam em um formato previsível antes da validação.

**Responsabilidades:**

- Remover caracteres não numéricos (`onlyDigits`).
- Ajustar e normalizar strings (`sanitizeString`, `sanitizeLowercase`).
- Converter valores para tipos específicos de forma segura (`sanitizeBoolean`, `sanitizeInteger`).

**Exemplo de uso:**

```php
// Dentro de um DTO
protected static function sanitize(array $data): array
{
    return [
        'document' => DataSanitizer::onlyDigits($data['document'] ?? null),
        'email' => DataSanitizer::sanitizeEmail($data['email'] ?? null),
        'notify' => DataSanitizer::sanitizeBoolean($data['notify'] ?? null),
    ];
}
```

### 2\. `HttpClientFactory`

É uma **Factory** cujo único objetivo é construir e configurar uma instância do `GuzzleHttp\Client`. Ela centraliza toda a configuração do cliente HTTP, garantindo que todas as requisições feitas pelo SDK se comportem da mesma maneira.

**Responsabilidades:**

- Definir a URL base (`base_uri`) e os timeouts.
- Inserir os cabeçalhos padrão em todas as requisições (`access_token`, `User-Agent`, etc.).
- **Configurar Middlewares cruciais:**
  - **Retry Middleware:** Implementa uma lógica de novas tentativas automáticas para falhas de conexão ou erros específicos da API (ex: `429 Too Many Requests`, `503 Service Unavailable`), aumentando a resiliência do SDK.
  - **Logging Middleware:** Permite registrar os detalhes das requisições em ambiente de sandbox para fins de depuração.

### 3\. `ResponseHandler`

Esta classe é a espinha dorsal da **estratégia de tratamento de erros** do SDK. Ela recebe a resposta HTTP do Guzzle e a traduz para o domínio da aplicação.

**Responsabilidades:**

- Verificar o `status code` da resposta.
- Para respostas de sucesso ( `2xx` ), ela apenas extrai e retorna o corpo (`body`) da resposta como um `array`.
- Para respostas de erro ( `4xx` , `5xx` ), ela **converte o erro HTTP em uma exceção PHP específica e tipada** (ex: um erro `404` vira uma `NotFoundException`, um `401` vira uma `AuthenticationException`).
- Extrair mensagens de erro detalhadas do corpo da resposta para enriquecer as exceções.

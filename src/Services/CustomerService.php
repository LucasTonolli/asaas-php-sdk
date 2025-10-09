<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Customers\CreateCustomerAction;
use AsaasPhpSdk\Actions\Customers\DeleteCustomerAction;
use AsaasPhpSdk\Actions\Customers\GetCustomerAction;
use AsaasPhpSdk\Actions\Customers\ListCustomersAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;
use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;

final class CustomerService
{
    public function __construct(private Client $client, private readonly ResponseHandler $responseHandler = new ResponseHandler) {}

    /**
     * Create a new customer
     *
     * @param  array  $data  Customer data
     * @return array Created customer data
     *
     * @throws ValidationException
     * @throws \AsaasPhpSdk\Exceptions\AuthenticationException
     * @throws \AsaasPhpSdk\Exceptions\ApiException
     */
    public function create(array $data): array
    {
        $dto = $this->createDTO(CreateCustomerDTO::class, $data);
        $action = new CreateCustomerAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    /**
     * List customers with optional filters
     *
     * @param  array  $filters  Optional filters (limit, offset, name, email, cpfCnpj)
     * @return array Paginated list of customers
     *
     * @throws \AsaasPhpSdk\Exceptions\ApiException
     */
    public function list(array $filters = []): array
    {
        $dto = $this->createDTO(ListCustomersDTO::class, $filters);
        $action = new ListCustomersAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    /**
     * Get a customer by ID
     *
     * @param  string  $id  Customer ID
     * @return array Customer data
     *
     * @throws \AsaasPhpSdk\Exceptions\ApiException
     * @throws \AsaasPhpSdk\Exceptions\AuthenticationException
     * @throws \AsaasPhpSdk\Exceptions\NotFoundException
     * @throws \InvalidArgumentException
     */
    public function get(string $id): array
    {
        $action = new GetCustomerAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Delete a customer by ID
     * 
     * @param  string  $id  Customer ID
     * @return array Deleted customer data
     * 
     * @throws \AsaasPhpSdk\Exceptions\ApiException
     * @throws \AsaasPhpSdk\Exceptions\AuthenticationException
     * @throws \AsaasPhpSdk\Exceptions\NotFoundException
     * @throws \InvalidArgumentException
     */
    public function delete(string $id): array
    {
        $action = new DeleteCustomerAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Helper method to create DTOs with consistent error handling
     *
     * @template T
     *
     * @param  class-string<T>  $dtoClass
     * @return T
     *
     * @throws ValidationException
     */
    private function createDTO(string $dtoClass, array $data): object
    {
        try {
            return $dtoClass::fromArray($data);
        } catch (\AsaasPhpSdk\Exceptions\InvalidCustomerDataException $e) {
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

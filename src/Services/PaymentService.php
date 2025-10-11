<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Payments\CreatePaymentAction;
use AsaasPhpSdk\DTOs\Payments\CreatePaymentDTO;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;

final class PaymentService
{
    private readonly ResponseHandler $responseHandler;

    public function __construct(private Client $client, ?ResponseHandler $responseHandler = null)
    {
        $this->responseHandler = $responseHandler ?? new ResponseHandler;
    }

    public function create(array $data): array
    {
        $dto = $this->createDTO(CreatePaymentDTO::class, $data);
        $action = new CreatePaymentAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    private function createDTO(string $dtoClass, array $data): object
    {
        try {
            return $dtoClass::fromArray($data);
        } catch (\AsaasPhpSdk\Exceptions\InvalidPaymentDataException $e) {
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

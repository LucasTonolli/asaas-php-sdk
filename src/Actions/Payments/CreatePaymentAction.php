<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Payments\CreatePaymentDTO;

final class CreatePaymentAction extends AbstractAction
{
    public function handle(CreatePaymentDTO $data): array
    {
        return $this->executeRequest(
            fn () => $this->client->post('payments', ['json' => $data->toArray()])
        );
    }
}

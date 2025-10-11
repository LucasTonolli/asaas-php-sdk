<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments;

use AsaasPhpSdk\DTOs\AbstractDTO;
use AsaasPhpSdk\Enums\BillingType;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Callback;
use AsaasPhpSdk\ValueObjects\Discount;
use AsaasPhpSdk\ValueObjects\Interest;
use AsaasPhpSdk\ValueObjects\Split;
use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;
use AsaasPhpSdk\Exceptions\InvalidPaymentDataException;
use AsaasPhpSdk\Exceptions\InvalidValueObjectException;

final class CreatePaymentDTO extends AbstractDTO
{
	private function __construct(
		public readonly string $customerId,
		public readonly BillingType $billingType,
		public readonly float $value,
		#[ToArrayMethodAttribute(method: 'format', args: ['Y-m-d'])]
		public readonly \DateTimeImmutable $dueDate,
		public readonly ?string $description = null,
		public readonly ?int $daysAfterDueDateToRegistrationCancellation = null,
		public readonly ?string $externalReference = null,
		public readonly ?int $installmentCount = null,
		public readonly ?float $totalValue = null, // Only for installments and if filled, it's unnecessary installment value
		public readonly ?float $installmentValue = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Discount $discount = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Interest $interest = null,
		public readonly ?bool $postalService = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Split $split = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Callback $callback = null
	) {}

	public static function fromArray(array $data): self
	{
		$sanitizedData = self::sanitize($data);
		$validatedData = self::validate($sanitizedData);

		return new self(...$validatedData);
	}

	protected static function sanitize(array $data): array
	{
		return [
			'customerId' => DataSanitizer::sanitizeString($data['customerId']),
			'billingType' => $data['billingType'] ?? null,
			'value' => DataSanitizer::sanitizeFloat($data['value']),
			'dueDate' => $data['dueDate'] ?? null,
			'description' => self::optionalString($data, 'description'),
			'daysAfterDueDateToRegistrationCancellation' => self::optionalInteger($data, 'daysAfterDueDateToRegistrationCancellation'),
			'externalReference' => self::optionalString($data, 'externalReference'),
			'installmentCount' => self::optionalInteger($data, 'installmentCount'),
			'totalValue' => self::optionalFloat($data, 'totalValue'),
			'installmentValue' => self::optionalFloat($data, 'installmentValue'),
			'discount' => $data['discount'] ?? null,
			'interest' => $data['interest'] ?? null,
			'postalService' => self::optionalBoolean($data, 'postalService'),
			'split' => $data['split'] ?? null,
			'callback' => $data['callback'] ?? null,
		];
	}
	private static function validate(array $data): array
	{
		if (empty($data['customerId'])) {
			throw new InvalidPaymentDataException('Customer ID is required');
		}

		if (empty($data['billingType'])) {
			throw new InvalidPaymentDataException('Billing type is required');
		}

		if (empty($data['value'])) {
			throw new InvalidPaymentDataException('Value is required');
		}

		if (empty($data['dueDate'])) {
			throw new InvalidPaymentDataException('Due date is required');
		}

		$billingType = BillingType::tryFromString($data['billingType']);

		if ($billingType === null) {
			throw new InvalidPaymentDataException('Invalid billing type');
		}
		try {
			self::validateValueObject($data, 'discount', Discount::class);
			self::validateValueObject($data, 'interest', Interest::class);
			self::validateValueObject($data, 'split', Split::class);
			self::validateValueObject($data, 'callback', Callback::class);
		} catch (InvalidValueObjectException $e) {
			throw new InvalidPaymentDataException($e->getMessage(), 0, $e);
		}


		return $data;
	}
}

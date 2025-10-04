<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Customer;

use AsaasPhpSdk\Exceptions\InvalidCustomerDataException;

final class CreateCustomerDTO
{

	private function __construct(
		public readonly string $name,
		public readonly string $cpfCnpj,
		public readonly ?string $email = null,
		public readonly ?string $phone = null,
		public readonly ?string $mobilePhone = null,
		public readonly ?string $address = null,
		public readonly ?string $addressNumber = null,
		public readonly ?string $complement = null,
		public readonly ?string $province = null,
		public readonly ?string $postalCode = null,
		public readonly ?string $externalReference = null,
		public readonly bool $notificationDisabled = false,
		public readonly ?string $additionalEmails = null,
		public readonly ?string $municipalInscription = null,
		public readonly ?string $stateInscription = null,
		public readonly ?string $observations = null,
		public readonly ?string $groupName = null,
		public readonly ?string $company = null,
		public readonly bool $foreignCustomer = false
	) {}

	public static function fromArray(array $data): self
	{

		$sanitizedData = self::sanitize($data);
		self::validate($sanitizedData);
		$formattedData = self::format($sanitizedData);

		return new self(
			name: $formattedData['name'],
			cpfCnpj: $formattedData['cpfCnpj'],
			email: $formattedData['email'],
			phone: $formattedData['phone'],
			mobilePhone: $formattedData['mobilePhone'],
			address: $formattedData['address'],
			addressNumber: $formattedData['addressNumber'],
			complement: $formattedData['complement'],
			province: $formattedData['province'],
			postalCode: $formattedData['postalCode'],
			externalReference: $formattedData['externalReference'],
			notificationDisabled: $formattedData['notificationDisabled'],
			additionalEmails: $formattedData['additionalEmails'],
			municipalInscription: $formattedData['municipalInscription'],
			stateInscription: $formattedData['stateInscription'],
			observations: $formattedData['observations'],
			groupName: $formattedData['groupName'],
			company: $formattedData['company'],
			foreignCustomer: $formattedData['foreignCustomer']
		);
	}

	public function toArray(): array
	{
		$data = [
			'name' => $this->name,
			'cpfCnpj' => $this->cpfCnpj,
			'email' => $this->email,
			'phone' => $this->phone,
			'mobilePhone' => $this->mobilePhone,
			'address' => $this->address,
			'addressNumber' => $this->addressNumber,
			'complement' => $this->complement,
			'province' => $this->province,
			'postalCode' => $this->postalCode,
			'externalReference' => $this->externalReference,
			'notificationDisabled' => $this->notificationDisabled,
			'additionalEmails' => $this->additionalEmails,
			'municipalInscription' => $this->municipalInscription,
			'stateInscription' => $this->stateInscription,
			'observations' => $this->observations,
			'groupName' => $this->groupName,
			'company' => $this->company,
			'foreignCustomer' => $this->foreignCustomer,
		];

		return array_filter($data, fn($value) => $value !== null && $value !== false);
	}

	private static function sanitize(array $data): array
	{
		return [
			'name' => self::sanitizeString($data['name'] ?? ''),
			'cpfCnpj' => self::sanitizeNumbers($data['cpfCnpj'] ?? ''),
			'email' => self::sanitizeString($data['email'] ?? null),
			'phone' => self::sanitizeNumbers($data['phone'] ?? null),
			'mobilePhone' => self::sanitizeNumbers($data['mobilePhone'] ?? null),
			'address' => self::sanitizeString($data['address'] ?? null),
			'addressNumber' => self::sanitizeString($data['addressNumber'] ?? null),
			'complement' => self::sanitizeString($data['complement'] ?? null),
			'province' => self::sanitizeString($data['province'] ?? $data['neighborhood'] ?? null),
			'postalCode' => self::sanitizeNumbers($data['postalCode'] ?? null),
			'externalReference' => self::sanitizeString($data['externalReference'] ?? null),
			'notificationDisabled' => $data['notificationDisabled'] ?? false,
			'additionalEmails' => self::sanitizeString($data['additionalEmails'] ?? null),
			'municipalInscription' => self::sanitizeString($data['municipalInscription'] ?? null),
			'stateInscription' => self::sanitizeString($data['stateInscription'] ?? null),
			'observations' => self::sanitizeString($data['observations'] ?? null),
			'groupName' => self::sanitizeString($data['groupName'] ?? null),
			'company' => self::sanitizeString($data['company'] ?? null),
			'foreignCustomer' => $data['foreignCustomer'] ?? false,
		];
	}

	private static function validate(array $data): void
	{
		if (empty($data['name'])) {
			throw InvalidCustomerDataException::missingField('name');
		}

		if (empty($data['cpfCnpj'])) {
			throw InvalidCustomerDataException::missingField('cpfCnpj');
		}

		if (!self::isValidCpfCnpj($data['cpfCnpj'])) {
			throw InvalidCustomerDataException::invalidFormat('cpfCnpj');
		}

		if ($data['email'] && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			throw InvalidCustomerDataException::invalidFormat('email');
		}

		if ($data['postalCode'] && !preg_match('/^\d{8}$/', $data['postalCode'])) {
			throw InvalidCustomerDataException::invalidFormat('postalCode');
		}

		if ($data['phone'] && !preg_match('/^\d{10,11}$/', $data['phone'])) {
			throw InvalidCustomerDataException::invalidFormat('phone');
		}

		if ($data['mobilePhone'] && !preg_match('/^\d{10,11}$/', $data['mobilePhone'])) {
			throw InvalidCustomerDataException::invalidFormat('mobilePhone');
		}
	}

	private static function format(array $data): array
	{
		if ($data['postalCode']) {
			$data['postalCode'] = self::formatPostalCode($data['postalCode']);
		}

		return $data;
	}

	private static function sanitizeString(?string $value): ?string
	{
		return $value ? trim($value) : null;
	}

	private static function sanitizeNumbers(?string $value): ?string
	{
		return $value ? preg_replace('/\D/', '', $value) : null;
	}

	private static function isValidCpfCnpj(string $value): bool
	{
		$length = strlen($value);

		if ($length === 11) {
			return self::isValidCpf($value);
		}

		if ($length === 14) {
			return self::isValidCnpj($value);
		}

		return false;
	}

	private static function isValidCpf(string $cpf): bool
	{
		if (preg_match('/^(\d)\1{10}$/', $cpf)) {
			return false;
		}

		$sum = 0;
		for ($i = 0; $i < 9; $i++) {
			$sum += ((int)$cpf[$i]) * (10 - $i);
		}
		$digit1 = 11 - ($sum % 11);
		$digit1 = $digit1 >= 10 ? 0 : $digit1;

		if ($digit1 != (int)$cpf[9]) {
			return false;
		}

		$sum = 0;
		for ($i = 0; $i < 10; $i++) {
			$sum += ((int)$cpf[$i]) * (11 - $i);
		}
		$digit2 = 11 - ($sum % 11);
		$digit2 = $digit2 >= 10 ? 0 : $digit2;

		return $digit2 == (int)$cpf[10];
	}

	private static function isValidCnpj(string $cnpj): bool
	{
		if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
			return false;
		}

		$weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
		$sum = 0;
		for ($i = 0; $i < 12; $i++) {
			$sum += ((int)$cnpj[$i]) * $weights[$i];
		}
		$digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

		if ($digit1 != (int)$cnpj[12]) {
			return false;
		}

		$weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
		$sum = 0;
		for ($i = 0; $i < 13; $i++) {
			$sum += ((int)$cnpj[$i]) * $weights[$i];
		}
		$digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

		return $digit2 == (int)$cnpj[13];
	}

	private static function formatPostalCode(string $value): string
	{
		return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $value);
	}
}

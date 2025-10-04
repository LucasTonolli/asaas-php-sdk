<?php

declare(strict_types=1);

use AsaasPhpSdk\DTOs\Customer\CreateCustomerDTO;
use AsaasPhpSdk\Exceptions\InvalidCustomerDataException;

describe('CreateCustomerDTO', function () {

	it('creates DTO from valid data', function () {
		$data = [
			'name' => 'Test Name',
			'cpfCnpj' => '898.879.660-88',
			'email' => 'a@b.com',
			'mobilePhone' => '(99) 99999-9999',
		];

		$dto = CreateCustomerDTO::fromArray($data);

		expect($dto->name)->toBe('Test Name')
			->and($dto->cpfCnpj)->toBe('89887966088')
			->and($dto->email)->toBe('a@b.com')
			->and($dto->mobilePhone)->toBe('99999999999');
	});

	it('fields filled were formatted, sanitized and validated', function () {
		$data = [
			'name' => '  Test Name  ',
			'cpfCnpj' => '898.879.660-88',
			'email' => 'a@b.com',
			'mobilePhone' => '(99) 99999-9999',
			'postalCode' => '12345-678',
		];

		$dto = CreateCustomerDTO::fromArray($data);

		expect($dto->name)->toBe('Test Name')
			->and($dto->cpfCnpj)->toBe('89887966088')
			->and($dto->email)->toBe('a@b.com')
			->and($dto->mobilePhone)->toBe('99999999999')
			->and($dto->postalCode)->toBe('12345-678');
	});

	it('fields not filled not appear in toArray', function () {
		$data = [
			'name' => 'Test Name',
			'cpfCnpj' => '898.879.660-88',
		];

		$dto = CreateCustomerDTO::fromArray($data);

		expect($dto->toArray())->toMatchArray([
			'name' => 'Test Name',
			'cpfCnpj' => '89887966088',
		]);
	});

	it('if required field is missing throws exception', function () {
		$data = [
			'cpfCnpj' => '898.879.660-88',
		];

		expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Required field 'name' is missing.");

		$data = [
			'name' => 'Test Name',
		];

		expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Required field 'cpfCnpj' is missing.");
	});

	describe('validation for optional fields', function () {
		it('if value from email is invalid throws exception', function () {
			$data = [
				'name' => 'Test Name',
				'cpfCnpj' => '898.879.660-88',
				'email' => 'test',
			];

			expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Field 'email' has invalid format.");
		});

		it('if value from postalCode is invalid throws exception', function () {
			$data = [
				'name' => 'Test Name',
				'cpfCnpj' => '898.879.660-88',
				'postalCode' => '123',

			];

			expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Field 'postalCode' has invalid format.");
		});
		it('if value from phone or mobilePhone is invalid throws exception', function () {
			$data = [
				'name' => 'Test Name',
				'cpfCnpj' => '898.879.660-88',
				'phone' => '123',
				'mobilePhone' => '123',
			];
			expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Field 'phone' has invalid format.");

			unset($data['phone']);

			expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Field 'mobilePhone' has invalid format.");
		});

		it('if value from cpfCnpj is invalid throws exception', function () {
			$data = [
				'name' => 'Test Name',
				'cpfCnpj' => '123',
			];

			expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Field 'cpfCnpj' has invalid format.");

			$data['cpfCnpj'] = '111111111111111';

			expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Field 'cpfCnpj' has invalid format.");
		});

		it('formats postalCode with hyphen after sanitization', function () {
			$dto = CreateCustomerDTO::fromArray([
				'name' => 'Test Name',
				'cpfCnpj' => '898.879.660-88',
				'postalCode' => '01310000',
			]);

			expect($dto->postalCode)->toBe('01310-000');
		});
	});
});

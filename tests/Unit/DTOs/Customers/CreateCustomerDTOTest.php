<?php

declare(strict_types=1);

use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;
use AsaasPhpSdk\Exceptions\InvalidCustomerDataException;

describe('CreateCustomerDTO', function (): void {

    it('creates DTO from valid data', function (): void {
        $data = [
            'name' => 'Test Name',
            'cpfCnpj' => '898.879.660-88',
            'email' => 'John@gmail.com',
            'mobilePhone' => '(99) 99999-9999',
        ];

        $dto = CreateCustomerDTO::fromArray($data);

        expect($dto->name)->toBe('Test Name')
            ->and($dto->cpfCnpj->value())->toBe('89887966088')
            ->and($dto->email->value())->toBe('john@gmail.com')
            ->and($dto->mobilePhone->value())->toBe('99999999999');
    });

    it('fields filled were formatted, sanitized and validated', function (): void {
        $data = [
            'name' => '  Test Name  ',
            'cpfCnpj' => '898.879.660-88',
            'email' => 'JohN@gmail.com',
            'mobilePhone' => '(99) 99999-9999',
            'postalCode' => '12345-678',
        ];

        $dto = CreateCustomerDTO::fromArray($data);

        expect($dto->name)->toBe('Test Name')
            ->and($dto->cpfCnpj->value())->toBe('89887966088')
            ->and($dto->email->value())->toBe('john@gmail.com')
            ->and($dto->mobilePhone->value())->toBe('99999999999')
            ->and($dto->postalCode->formatted())->toBe('12345-678');
    });

    it('fields not filled not appear in toArray', function (): void {
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

    it('if required field is missing throws exception', function (): void {
        $data = [
            'cpfCnpj' => '898.879.660-88',
        ];

        expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Required field 'name' is missing.");

        $data = [
            'name' => 'Test Name',
        ];

        expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, "Required field 'cpfCnpj' is missing.");
    });

    describe('validation for optional fields', function (): void {
        it('if value from email is invalid throws exception', function (): void {
            $data = [
                'name' => 'Test Name',
                'cpfCnpj' => '898.879.660-88',
                'email' => 'test',
            ];

            expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, 'Email is not valid');
        });

        it('if value from postalCode is invalid throws exception', function (): void {
            $data = [
                'name' => 'Test Name',
                'cpfCnpj' => '898.879.660-88',
                'postalCode' => '123',

            ];

            expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, 'Postal code must contain exactly 8 digits');
        });
        it('if value from phone or mobilePhone is invalid throws exception', function (): void {
            $data = [
                'name' => 'Test Name',
                'cpfCnpj' => '898.879.660-88',
                'phone' => '123',
                'mobilePhone' => '123',
            ];
            expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, 'Phone must contain 10 or 11 digits');

            unset($data['phone']);

            expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, 'Phone must contain 10 or 11 digits');
        });

        it('if value from cpfCnpj is invalid throws exception', function (): void {
            $data = [
                'name' => 'Test Name',
                'cpfCnpj' => '123',
            ];

            expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, 'CPF or CNPJ must contain 11 or 14 digits');

            $data['cpfCnpj'] = '11111111111111';

            expect(fn() => CreateCustomerDTO::fromArray($data))->toThrow(InvalidCustomerDataException::class, 'Invalid Cnpj: 11111111111111');
        });

        it('formats postalCode with hyphen after sanitization', function (): void {
            $dto = CreateCustomerDTO::fromArray([
                'name' => 'Test Name',
                'cpfCnpj' => '898.879.660-88',
                'postalCode' => '01310000',
            ]);

            expect($dto->postalCode->formatted())->toBe('01310-000');
        });
    });
});

<?php

use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Email;

describe('ListCustomersDTO', function () {

    it('creates DTO from valid data', function () {
        $dto = ListCustomersDTO::fromArray([
            'limit' => 10,
            'offset' => 0,
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '898.879.660-88',
            'groupName' => 'VIP',
            'externalReference' => 'REF123',
        ]);

        expect($dto->limit)->toBe(10);
        expect($dto->offset)->toBe(0);
        expect($dto->name)->toBe('John Doe');
        expect($dto->email)->toBeInstanceOf(Email::class)
            ->and($dto->email->value())->toBe('john.doe@test.com');
        expect($dto->cpfCnpj)->toBeInstanceOf(Cpf::class)
            ->and($dto->cpfCnpj->value())->toBe('89887966088');
        expect($dto->groupName)->toBe('VIP');
        expect($dto->externalReference)->toBe('REF123');
    });

    it('handles null and missing fields', function () {
        $dto = ListCustomersDTO::fromArray([]);

        expect($dto->limit)->toBeNull();
        expect($dto->offset)->toBeNull();
        expect($dto->name)->toBeNull();
        expect($dto->email)->toBeNull();
        expect($dto->cpfCnpj)->toBeNull();
        expect($dto->groupName)->toBeNull();
        expect($dto->externalReference)->toBeNull();
    });

    it('sets invalid integer fields to null', function () {
        $dto = ListCustomersDTO::fromArray([
            'limit' => 'a',
            'offset' => 'b',
        ]);

        expect($dto->limit)->toBeNull();
        expect($dto->offset)->toBeNull();
    });

    it('sets empty string fields to null', function () {
        $dto = ListCustomersDTO::fromArray([
            'name' => '',
            'groupName' => '',
            'externalReference' => '',
        ]);

        expect($dto->name)->toBeNull()
            ->and($dto->groupName)->toBeNull()
            ->and($dto->externalReference)->toBeNull();
    });

    it('handles invalid email gracefully', function () {
        $dto = ListCustomersDTO::fromArray([
            'email' => 'invalid-email',
        ]);

        expect($dto->email)->toBeNull();
    });

    it('handles CPF and CNPJ correctly', function () {

        $dtoCpf = ListCustomersDTO::fromArray([
            'cpfCnpj' => '898.879.660-88',
        ]);
        expect($dtoCpf->cpfCnpj)->toBeInstanceOf(Cpf::class);

        $dtoCnpj = ListCustomersDTO::fromArray([
            'cpfCnpj' => '12.345.678/0001-95',
        ]);
        expect($dtoCnpj->cpfCnpj)->toBeInstanceOf(Cnpj::class);

        $dtoInvalid = ListCustomersDTO::fromArray([
            'cpfCnpj' => '123456789',
        ]);
        expect($dtoInvalid->cpfCnpj)->toBeNull();

        $dtoInvalidFormat = ListCustomersDTO::fromArray([
            'cpfCnpj' => '111.111.111-11',
        ]);
        expect($dtoInvalidFormat->cpfCnpj)->toBeNull();
    });

    it('toArray returns only non-null fields', function () {
        $dto = ListCustomersDTO::fromArray([
            'name' => 'John',
            'cpfCnpj' => '898.879.660-88',
        ]);

        $array = $dto->toArray();

        expect($array)->toHaveKeys(['name', 'cpfCnpj'])
            ->and($array)->not->toHaveKey('email')
            ->and($array)->not->toHaveKey('offset')
            ->and($array)->not->toHaveKey('limit')
            ->and($array)->not->toHaveKey('groupName')
            ->and($array)->not->toHaveKey('externalReference');
    });
});

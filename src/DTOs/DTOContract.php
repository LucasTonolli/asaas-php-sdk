<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs;

interface DTOContract
{
	public function toArray(): array;
	public static function fromArray(array $data): self;
}

<?php

namespace AsaasPhpSdk\DTOs\Attributes;

final class ToArrayMethodAttribute
{
	public function __construct(public readonly string $method) {}
}

<?php

namespace AsaasPhpSdk\DTOs\Attributes;

use Attribute;

#[Attribute(\Attribute::TARGET_PROPERTY)]
final class ToArrayMethodAttribute
{
	public function __construct(public readonly string $method) {}
}

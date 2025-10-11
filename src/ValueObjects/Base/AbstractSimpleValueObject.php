<?php

namespace AsaasPhpSdk\ValueObjects\Base;

use AsaasPhpSdk\ValueObjects\Base\AbstractValueObject;

abstract class AbstractSimpleValueObject extends AbstractValueObject
{
	protected readonly string $value;

	/**
	 * Private constructor to enforce immutability and the factory pattern.
	 *
	 * @internal Should only be called from a static factory method like `from()`.
	 */
	protected function __construct(string $value)
	{
		$this->value = $value;
	}

	/**
	 * Gets the raw, underlying string value.
	 */
	public function value(): string|int|bool
	{
		return $this->value;
	}
}

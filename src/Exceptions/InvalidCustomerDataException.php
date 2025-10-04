<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions;

final class InvalidCustomerDataException extends AsaasException
{
	public static function missingField(string $field): self
	{
		return new self("Required field '{$field}' is missing", 400);
	}

	public static function invalidFormat(string $field, ?string $message = null): self
	{
		$defaultMessage = "Field '{$field}' has invalid format";
		return new self($message ?? $defaultMessage, 400);
	}
}

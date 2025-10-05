<?php

namespace AsaasPhpSdk\ValueObjects;

interface ValueObjectContract extends \JsonSerializable, \Stringable
{
	public function value(): string;
	public function equals(self $other): bool;
}

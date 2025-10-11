<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Base;

use AsaasPhpSdk\ValueObjects\Base\AbstractValueObject;


abstract class AbstractStructuredValueObject extends AbstractValueObject
{
	/**
	 * Converts the structured value object into an array.
	 * 
	 * - Recursively calls toArray() for nested StructuredValueObjects.
	 * - Uses value() for simple ValueObjects.
	 * - Removes null values.
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		$result = [];

		foreach (get_object_vars($this) as $key => $value) {
			if ($value instanceof AbstractStructuredValueObject) {
				$result[$key] = $value->toArray();
			} elseif (is_array($value) && !empty($value) && $value[0] instanceof AbstractStructuredValueObject) {
				$array[$key] = array_map(fn($v) => $v->toArray(), $value);
			} elseif (method_exists($value, 'value')) {
				$result[$key] = $value->value();
			} else {
				$result[$key] = $value;
			}
		}

		return array_filter($result, fn($v) => $v !== null);
	}
}

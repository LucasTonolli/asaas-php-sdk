<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

final class Split extends AbstractStructuredValueObject
{
	/**
	 * @var SplitEntry[]
	 */
	private  array $entries = [];

	private function __construct(array $entries)
	{
		if (empty($entries)) {
			throw new \InvalidArgumentException('Split entries must not be empty');
		}

		$this->entries = $entries;
	}

	public static function create(array $entries): self
	{
		return new self($entries);
	}

	public static function fromArray(array $data): self
	{
		$entries = array_map(
			fn(array $entry) => SplitEntry::fromArray($entry),
			$data
		);

		return new self($entries);
	}

	public function getEntries(): array
	{
		return $this->entries;
	}

	public function entriesToArray(): array
	{
		return array_map(fn(SplitEntry $entry) => $entry->toArray(), $this->entries);
	}

	public function count(): int
	{
		return count($this->entries);
	}

	public function totalPercentage(): float
	{
		return array_reduce(
			$this->entries,
			fn(float $sum, SplitEntry $entry) => $sum + ($entry->percentageValue ?? 0),
			0
		);
	}

	/**
	 * Calculate total fixed value allocated
	 */
	public function totalFixedValue(): float
	{
		return array_reduce(
			$this->entries,
			fn(float $sum, SplitEntry $entry) => $sum + ($entry->fixedValue ?? 0) + ($entry->totalFixedValue ?? 0),
			0
		);
	}

	/**
	 * Validate that split allocation is valid for payment amount
	 */
	public function validateFor(float $paymentValue): void
	{
		$totalPercentage = $this->totalPercentage();
		if ($totalPercentage > 100) {
			throw new \InvalidArgumentException(
				"Split percentages sum to {$totalPercentage}%, which exceeds 100%"
			);
		}

		$totalFixed = $this->totalFixedValue();
		if ($totalFixed > $paymentValue) {
			throw new \InvalidArgumentException(
				"Split fixed values sum to R$ {$totalFixed}, which exceeds payment value of R$ {$paymentValue}"
			);
		}
	}
}

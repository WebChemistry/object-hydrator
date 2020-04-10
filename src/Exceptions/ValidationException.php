<?php declare(strict_types = 1);

namespace WebChemistry\ObjectHydrator\Exceptions;

use Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends Exception
{

	/**
	 * @phpstan-var ConstraintViolationListInterface<ConstraintViolationInterface> $violationList
	 */
	private ConstraintViolationListInterface $violationList;

	/**
	 * @phpstan-param ConstraintViolationListInterface<ConstraintViolationInterface> $violationList
	 */
	public function __construct(ConstraintViolationListInterface $violationList)
	{
		$this->violationList = $violationList;

		parent::__construct($this->createMessage());
	}

	private function createMessage(): string
	{
		$message = '';
		foreach ($this->violationList as $item) {
			assert($item instanceof ConstraintViolationInterface);

			$message .= sprintf("%s: %s\n", $item->getPropertyPath(), (string) $item->getMessage());
		}

		return substr($message, 0, -1);
	}

}

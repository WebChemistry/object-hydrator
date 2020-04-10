<?php declare(strict_types = 1);

namespace WebChemistry\ObjectHydrator;

use WebChemistry\ObjectHydrator\Exceptions\SerializationException;
use WebChemistry\ObjectHydrator\Exceptions\ValidationException;

interface ObjectHydratorInterface
{

	/**
	 * @param mixed[] $values
	 * @throws ValidationException
	 * @throws SerializationException
	 */
	public function hydrate(iterable $values, string $className): object;

}

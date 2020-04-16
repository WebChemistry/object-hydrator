<?php declare(strict_types = 1);

namespace WebChemistry\ObjectHydrator;

use LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use WebChemistry\ObjectHydrator\Exceptions\SerializationException;
use WebChemistry\ObjectHydrator\Exceptions\ValidationException;
use WebChemistry\ObjectHydrator\Normalizer\ObjectStrictNormalizer;

class ObjectHydrator implements ObjectHydratorInterface
{

	private DenormalizerInterface $denormalizer;

	private ValidatorInterface $validator;

	public function __construct(ValidatorInterface $validator, ?DenormalizerInterface $denormalizer = null)
	{
		$this->denormalizer = $denormalizer ?? new ObjectStrictNormalizer();
		$this->validator = $validator;
	}

	/**
	 * @param mixed[] $values
	 * @throws ValidationException
	 * @throws SerializationException
	 */
	public function hydrate(iterable $values, string $className): object
	{
		if (!class_exists($className)) {
			throw new LogicException(sprintf('Class %s not exists', $className));
		}

		try {
			$object = $this->denormalizer->denormalize($values, $className);
			assert(is_object($object));
		} catch (Throwable $exception) {
			throw new SerializationException($this->normalizeSerializationMessage($exception), $exception);
		}

		$violations = $this->validator->validate($object);

		if (count($violations) > 0) {
			throw new ValidationException($violations);
		}

		return $object;
	}

	private function normalizeSerializationMessage(Throwable $exception): string
	{
		return $exception->getMessage();
	}

}

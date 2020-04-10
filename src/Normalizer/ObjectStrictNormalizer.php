<?php declare(strict_types = 1);

namespace WebChemistry\ObjectHydrator\Normalizer;

use LogicException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use TypeError;
use WebChemistry\ObjectHydrator\Exceptions\InvalidParameterException;

final class ObjectStrictNormalizer extends ObjectNormalizer
{

	/**
	 * @param mixed[] $defaultContext
	 */
	public function __construct(
		?ClassMetadataFactoryInterface $classMetadataFactory = null,
		?NameConverterInterface $nameConverter = null,
		?PropertyAccessorInterface $propertyAccessor = null,
		?PropertyTypeExtractorInterface $propertyTypeExtractor = null,
		?ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
		?callable $objectClassResolver = null,
		array $defaultContext = []
	)
	{
		$propertyAccessor ??= PropertyAccess::createPropertyAccessorBuilder()
			->enableExceptionOnInvalidIndex()
			->getPropertyAccessor();

		parent::__construct(
			$classMetadataFactory,
			$nameConverter,
			$propertyAccessor,
			$propertyTypeExtractor,
			$classDiscriminatorResolver,
			$objectClassResolver,
			$defaultContext
		);
	}

	public function hasCacheableSupportsMethod(): bool
	{
		return true;
	}

	/**
	 * @param mixed $value
	 * @param mixed[] $context
	 * @throws InvalidParameterException
	 */
	protected function setAttributeValue( // phpcs:ignore -- phpcs bug - mixed $value
		object $object,
		string $attribute,
		$value,
		?string $format = null,
		array $context = []
	): void
	{
		try {
			$this->propertyAccessor->setValue($object, $attribute, $value);
		} catch (NoSuchPropertyException $exception) {
			throw new InvalidParameterException(sprintf('Parameter "%s" not found.', $attribute));
		} catch (TypeError $error) {
			[$required, $given] = $this->extractTypesFromTypeError($error);
			throw new InvalidParameterException(
				sprintf('Parameter "%s" must be %s, %s given.', $attribute, $required, $given)
			);
		}
	}

	/**
	 * @return string[]
	 */
	private function extractTypesFromTypeError(TypeError $error): array
	{
		[$required, $given] = explode(', ', $error->getMessage());

		$givenPos = strpos($given, ' ');
		if ($givenPos === false) {
			throw new LogicException(sprintf('%s::%s not working correctly', self::class, __METHOD__));
		}

		$requiredPos = strrpos($required, ' ');
		if ($requiredPos === false) {
			throw new LogicException(sprintf('%s::%s not working correctly', self::class, __METHOD__));
		}

		return [
			substr($required, $requiredPos + 1),
			substr($given, 0, $givenPos),
		];
	}

}

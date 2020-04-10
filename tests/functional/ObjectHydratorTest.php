<?php declare(strict_types = 1);

namespace Project\Tests;

use Codeception\Test\Unit;
use Symfony\Component\Validator\Validation;
use WebChemistry\ObjectHydrator\Exceptions\SerializationException;
use WebChemistry\ObjectHydrator\Exceptions\ValidationException;
use WebChemistry\ObjectHydrator\ObjectHydrator;
use WebChemistry\ObjectHydrator\Testing\ObjectToValidate;

class ObjectHydratorTest extends Unit
{

	private ObjectHydrator $objectHydrator;

	protected function _before(): void
	{
		$this->objectHydrator = new ObjectHydrator(
			Validation::createValidatorBuilder()
				->addMethodMapping('loadValidatorMetadata')
				->getValidator()
		);
	}

	public function testPropertyMismatchType(): void
	{
		$values = [
			'required' => [],
		];

		$this->expectException(SerializationException::class);
		$this->expectExceptionMessage('Parameter "required" must be string, array given.');
		$this->hydrate($values);
	}

	public function testPropertyNotExists(): void
	{
		$values = [
			'not' => 42,
		];

		$this->expectException(SerializationException::class);
		$this->expectExceptionMessage('Parameter "not" not found.');
		$this->hydrate($values);
	}

	public function testRequiredProperty(): void
	{
		$values = [];

		$this->expectException(ValidationException::class);
		$this->expectDeprecationMessage('required: This value should not be blank.');
		$this->hydrate($values);
	}

	public function testValidation(): void
	{
		$values = [
			'int' => 999,
			'required' => 'foo',
		];

		$this->expectException(ValidationException::class);
		$this->expectDeprecationMessage('int: This value should be 150 or less.');

		$this->hydrate($values);
	}

	public function testTwoFailValidation(): void
	{
		$values = [
			'int' => 999,
		];

		$this->expectException(ValidationException::class);
		$this->expectDeprecationMessage(
			"int: This value should be 150 or less.\nrequired: This value should not be blank."
		);

		$this->hydrate($values);
	}

	/**
	 * @param mixed[] $values
	 */
	private function hydrate(array $values): ObjectToValidate
	{
		return $this->objectHydrator->hydrate($values, ObjectToValidate::class);
	}

}

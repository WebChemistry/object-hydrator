<?php declare(strict_types = 1);

namespace WebChemistry\ObjectHydrator\Testing;

use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @internal
 */
final class ObjectToValidate
{

	public string $required;

	public string $string = '';

	public ?string $nullable = null;

	public ?stdClass $object = null;

	public int $int = 1;

	public static function loadValidatorMetadata(ClassMetadata $metadata): void
	{
		$metadata->addPropertyConstraint('int', new Range([
			'max' => 150,
		]));
		$metadata->addPropertyConstraint('required', new NotBlank());
	}

}

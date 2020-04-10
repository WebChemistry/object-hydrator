<?php declare(strict_types = 1);

namespace WebChemistry\ObjectHydrator\Exceptions;

use Exception;
use Throwable;

class SerializationException extends Exception
{

	public function __construct(string $message, Throwable $previous)
	{
		parent::__construct($message, 0, $previous);
	}

}

<?php

namespace AsaasPhpSdk\Exceptions;

/**
 * Represents an error for an invalid email address format.
 *
 * This exception is typically thrown by the `Email` Value Object when a given
 * string does not conform to a valid email format.
 */
class InvalidEmailException extends InvalidValueObjectException {}

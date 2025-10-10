<?php

namespace AsaasPhpSdk\Exceptions;

/**
 * Represents an error for an invalid postal code (CEP) format.
 *
 * This exception is typically thrown by the `PostalCode` Value Object when a
 * given string does not conform to a valid postal code format (e.g., incorrect
 * number of digits).
 */

class InvalidPostalCodeException extends InvalidValueObjectException {}

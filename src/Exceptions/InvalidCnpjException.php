<?php

namespace AsaasPhpSdk\Exceptions;

/**
 * Represents an error for an invalid CNPJ value.
 *
 * This exception is typically thrown by the `Cnpj` Value Object when a given
 * string does not conform to a valid CNPJ format (e.g., incorrect number of
 * digits or an invalid checksum).
 */

class InvalidCnpjException extends InvalidValueObjectException {}

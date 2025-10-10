<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions;

/**
 * Base exception for errors that occur during Value Object validation.
 *
 * This exception serves as a common parent for all specific Value Object
 * exceptions (e.g., `InvalidCpfException`, `InvalidEmailException`). It allows
 * for flexible error handling, enabling developers to catch a general category
 * of validation errors.
 *
 * @example
 * try {
 * $email = Email::from('invalid-email');
 * } catch (InvalidValueObjectException $e) {
 * // This block will catch InvalidEmailException, InvalidCpfException, etc.
 * log_vo_error($e->getMessage());
 * }
 */

class InvalidValueObjectException extends AsaasException {}

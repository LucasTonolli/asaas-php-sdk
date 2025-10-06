<?php

namespace AsaasPhpSdk\Exceptions;


/**
 * Thrown when CPF validation fails
 */
class InvalidCpfException extends AsaasException {}

/**
 * Thrown when CNPJ validation fails
 */
class InvalidCnpjException extends AsaasException {}

/**
 * Thrown when postal code (CEP) validation fails
 */
class InvalidPostalCodeException extends AsaasException {}

/**
 * Thrown when phone number validation fails
 */
class InvalidPhoneException extends AsaasException {}

/**
 * Thrown when email validation fails
 */
class InvalidEmailException extends AsaasException {}

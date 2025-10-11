# Asaas PHP SDK (In Development)

[![PHP](https://img.shields.io/badge/php-8.1%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-brightgreen)](LICENSE)

---

## 📌 Overview

This SDK provides a **structured and type-safe way to interact with Asaas API**.
It follows **clean architecture** principles:

- **DTOs** → Data Transfer Objects for structured, immutable data.
- **Actions** → Perform operations using the API (Create/Update/Delete/Restore).
- **Value Objects** → Encapsulate and validate domain-specific values (Email, CPF, CNPJ, Phone, PostalCode).
- **Services** → High-level orchestration of multiple Actions.

> ⚠️ **Currently in active development. APIs may change.**

---

## 🛠 Getting Started

### Install via Composer

```bash
composer require lucas-tonolli/asaas-php-sdk
```

### Quick Example

```php
use AsaasPhpSdk\AsaasClient;
use AsaasPhpSdk\Config\AsaasConfig;
use AsaasPhpSdk\DTOs\CreateCustomerDTO;

/**
 * Sandbox mode
 */

$config = new AsaasConfig('your_token', true);

$client = new AsaasClient($config);

$response = $client->customer()->create([
    'name' => 'John Doe',
    'cpfCnpj' => '12345678901',
    'email' => 'john@example.com',
]);
print_r($response);

```

---

## 📂 Project Structure

```
src/
├── Actions/
├── DTOs/
├── Exceptions/
├── Helpers/
├── Services/
├── ValueObjects/
└── Config/AsaasClient.php

tests/
├── Unit/
└── Integration/

docs/
├── patterns/
└── workflow/
```

---

## ⚡ Development Workflow

- **Branching**: feature/_, fix/_, docs/\*
- **Commits**: follow [Conventional Commits](https://www.conventionalcommits.org/)
- **Testing**: Unit + Integration tests required for new features
- **Documentation**: Update `/docs/patterns` and `/docs/workflow` for new conventions

---

## 📖 Current Milestones

- **v0.1.0** → Customer Module (CRUD + Tests + Docs) ✅
- **v0.2.0** → Payment Module (DTOs, Actions, Tests, Docs) ⏳
- **v0.3.0** → Subscription Module ⏳

---

## 📝 Notes

- API coverage is **partial**; some endpoints still under implementation.
- DTOs are **immutable**; always use `fromArray()` to create instances.
- Value Objects ensure **data consistency** and **validation** at construction.

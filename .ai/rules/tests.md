---
paths:
  - 'tests/**'
---

# Tests

## Mirror app structure under tests/Unit
Place tests for code in `app/` at the same relative path under `tests/Unit/` (e.g. `app/Models/User.php` → `tests/Unit/Models/UserTest.php`). Keep HTTP/integration smoke tests in `tests/Feature/`. Architecture presets live in `tests/Unit/ArchTest.php`.

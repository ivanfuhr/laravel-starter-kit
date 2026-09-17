---
paths:
  - 'resources/views/**/*.test.php'
---

# Views

## Colocate view and Livewire tests
Tests for Livewire components and view layouts live next to their files as `*.test.php` under `resources/views` (discovered by the Components PHPUnit suite). Do not put those tests under `tests/Feature`.

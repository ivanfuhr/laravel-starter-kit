---
paths:
  - 'resources/views/pages/**'
---

# Pages

## Livewire page components are multi-file (MFC)
Always create page components as multi-file components under `resources/views/pages`. Use `php artisan make:livewire pages::{name}` (MFC is the project default). Colocate the Pest test as `{name}.test.php` inside the component directory.

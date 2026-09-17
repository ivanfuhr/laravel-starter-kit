---
paths:
  - 'resources/views/components/**'
---

# Components

## Livewire components are multi-file (MFC)
Always create Livewire components as multi-file components (`--mfc`). Do not use single-file (SFC) or class-based components. Prefer `php artisan make:livewire {name}` (defaults to MFC) or convert with `php artisan livewire:convert {name} --mfc`.

## Colocate Livewire component tests
Always include a colocated `{name}.test.php` in the MFC directory when creating or changing a Livewire component.

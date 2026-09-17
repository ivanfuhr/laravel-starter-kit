---
paths:
  - 'app/Models/**'
---

# Models

Mass assignment is globally unguarded via `Model::unguard()` in `DefaultsServiceProvider`. Never add `$fillable`, `$guarded`, or `#[Fillable]`.

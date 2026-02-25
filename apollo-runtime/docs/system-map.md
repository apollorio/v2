# Apollo Runtime — System Map

## Bootstrap flow

1. `apollo-runtime` loads after plugins bootstrap
2. Defines `APOLLO_RUNTIME_CONFIG_PATH`
3. `apollo-core` `ConfigLoader` resolves runtime overrides first
4. `apollo-core` initializes registries
5. `apollo-runtime/bootstrap.php` loads module bootstraps

## Responsibility split

- `apollo-core`: kernel, contracts, registries, validation, helpers
- `apollo-runtime`: host environment, module orchestration, config overrides

## Current status

- Runtime override for `config/*.php` is active via `ConfigLoader`
- Meta registration is centralized in `apollo-core` `MetaRegistry`
- Modules should publish extra meta through `apollo_core_register_meta`

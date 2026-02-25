# Apollo Plugins Workspace

This repository mirrors the Apollo plugin ecosystem and supporting inventory hosted at
`c:\Users\rafae\Local Sites\apollo\app\public\wp-content\plugins`. It includes all active plugins, shared libraries, and metadata defined by the Apollo team.

## Structure
- `_inventory/` — JSON registries, audits, and system documentation that define the platform contracts.
- `_library/` — Reference implementations and UX resources.
- `apollo-*` — Each plugin follows PSR-4 structure with its own entry point and templates.

## Objectives
1. Keep the local workspace synchronized with `apollorio/v2`.
2. Preserve the canonical registry (`_inventory/apollo-registry.json`) before touching CPTs or meta keys.
3. Do not introduce new CPTs, taxonomies, or table names; always reuse existing registry entries.

> Tip: Run `composer install` inside the plugin you are working on if it includes a `composer.json` file.

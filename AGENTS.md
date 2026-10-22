# AGENTS.md

## Environment
- **PHP**: Use `.\php74\php.exe` (PHP 7.4) for all commands.
- **Framework**: Laravel 6.

## Common Commands
- **Run Artisan**: `.\php74\php.exe artisan <command>`
- **Dependencies**: `.\php74\php.exe composer.phar install` (if available) or rely on `vendor/` folder.
- **Serve**: `.\php74\php.exe artisan serve`

## Notes
- Repository is standard Laravel 6.
- The default `php` command is unavailable in PATH; always invoke explicitly via `.\php74\php.exe`.

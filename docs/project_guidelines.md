# Laravel Project Guidelines

## Architecture

* Follow Laravel MVC conventions.
* Keep Eloquent models in `app/Models/`.
* Keep controllers in `app/Http/Controllers/`.
* Keep API routes in `routes/api.php`.
* Use Form Requests for request validation when appropriate.
* Use API Resources to format API responses when appropriate.
* Keep database schema changes in `database/migrations/`.

## Coding Rules

* Follow existing Laravel conventions and project structure.
* Keep controllers focused and avoid duplicated logic.
* Make small, clear, reviewable changes.
* Do not delete or overwrite existing code without checking first.
* Run relevant tests or checks after changes.

## Git Workflow

* Create feature branches using `feature/*`.
* Use Pull Requests for changes to shared branches.
* Require review before merging into `main`.
* Never use `git push --force` on shared branches.

## Security

* Never commit `.env`, passwords, API keys, or other secrets.
* Keep `.env.example` free of real credentials.
* Check `git status` before committing changes.
# Car dealer template

Laravel 13 + Livewire 4 storefront for dealership sites. Copy, point SQLite at a new file, and replace the seeded business details.

## Stack

- PHP 8.4, Laravel 13, Livewire 4, maryUI 2
- Tailwind CSS 4, daisyUI 5
- SQLite
- English / Spanish (`lang/en.json`, `lang/es.json`)

## Setup

Use PHP 8.4 (the default `php` on some machines is older):

```bash
php8.4 artisan migrate:fresh --seed
npm install
npm run dev
php8.4 artisan serve
```

Admin login: `admin@example.com` / `password`

Theme uses maryUI `x-theme-toggle` (persisted in the browser). Language uses the maryUI dropdown and is stored in the `locale` cookie.

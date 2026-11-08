# Campus Care — Campus Facility & Lost Item Reporting

An academic end-to-end project for reporting **lost items** and **damaged campus facilities** at Telkom University Jakarta. The system consists of a Laravel web application, a Laravel REST API, and a Flutter mobile application.

## What it does

- **Lost & found**: report lost/found items, upload proof, request a claim, schedule a pickup, and mark an item as claimed.
- **Facility reports**: report damaged rooms/facilities, attach photos, and track report status.
- **Admin dashboard**: manage rooms, review facility reports, and update report status.
- **Authentication**: email/password registration with OTP verification, password reset, and Google Sign-In.
- **Mobile app**: Flutter client for facility reporting, connected to the API with Sanctum authentication.

## Architecture

```text
campus_care (Flutter)  ──HTTP──>  reportfacility-api (Laravel REST API, port 8001)
                                         │
                                         │ EAI endpoints / shared data
                                         ▼
                                  campus-web (Laravel web app)
```

PlantUML diagrams are available in [`diagrams/`](diagrams):

- `component-diagram.puml`
- `deployment-diagram.puml`

## Repository structure

| Folder | Description | Stack |
|---|---|---|
| `campus-web/` | Main web application | Laravel 13, PHP 8.3, Blade, MySQL, Laravel Socialite |
| `reportfacility-api/` | REST API | Laravel 13, PHP 8.3, Sanctum, MySQL |
| `campus_care/` | Mobile application | Flutter, Dart, Provider, HTTP |
| `diagrams/` | Component and deployment diagrams | PlantUML |

## Tech stack

- **Backend**: PHP 8.3, Laravel 13, Laravel Sanctum, Laravel Socialite
- **Database**: MySQL
- **Mobile**: Flutter, Dart, Provider, `http`, `image_picker`, `google_sign_in`
- **Tooling**: Composer, npm/Vite, Flutter SDK, PlantUML

## Getting started

### 1. Web app (`campus-web`)

```bash
cd campus-web
composer install
cp .env.example .env
php artisan key:generate
# configure DB_* in .env
php artisan migrate
npm install
npm run dev
php artisan serve
```

### 2. API (`reportfacility-api`)

```bash
cd reportfacility-api
composer install
cp .env.example .env
php artisan key:generate
# configure DB_* in .env
php artisan migrate
php artisan serve --port=8001
```

### 3. Mobile app (`campus_care`)

```bash
cd campus_care
flutter pub get
flutter run
```

Set the API base URL in `lib/core/api_client.dart` to match your API host/port.

## Notes

- This is an academic project developed for coursework at Telkom University Jakarta.
- The mobile app and web app share authentication and report data through the API.
- Some environment values (database credentials, Google OAuth keys) are intentionally excluded from the repository.

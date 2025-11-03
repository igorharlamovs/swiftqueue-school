# school (swiftqueue-school)

Application to manage the courses a school can provide to their students more efficiently.

You can register as different user types, student, admin and teacher

 - Student can only view any existing courses created by admin or teacher
 - Teachers can create course, they will also be able to edit and delete courses created by them
 - Admins can create, edit and delete ANY course, they do not have to belong to them

## Tech stack: Quasar (Vue 3), Pinia, Axios, PHP backend, MySQL

## Prerequisites

List all required tools and versions:

- Node.js (>=18.x) and npm/yarn

- Quasar CLI (npm install -g @quasar/cli)
  
- Pinia

- PHP (>=8.2)

- MySQL (>=8.0) or MariaDB

- Composer (>=2.x) if using PHP packages

## Install the dependencies
```bash
yarn
# or
npm install
```

## Install Pinia
```bash
npm install pinia
```

### Start the app in development mode (hot-code reloading, error reporting, etc.)
```bash
quasar dev
```

# Backend Setup
## Navigate to backend api folder
```bash
cd swiftqueue-academy/backend/api
```

## Install PHP dependencies if any
```bash
composer install
```

## Setup database
1. Import schema SOURCE backend/database/swiftqueue_school_schema.sql;
2. Update DB config file (db.php) with credentials

## Run PHP server
```bash
php -S localhost:8000
```

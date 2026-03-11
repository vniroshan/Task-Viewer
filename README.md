# Task Viewer

A simple task management application built with PHP, MySQL, and Bootstrap 5.

---

## Requirements

- PHP 8.1+
- MySQL 5.7+
- A local server (Eg: XAMPP)

---

## Quick Start

### 1. Set up the database

**Option A - Command line**

Log in to MySQL and run the setup script:

```bash
mysql -u root -p < setup.sql
```

**Option B - phpMyAdmin (XAMPP)**

1. Open your browser and go to http://localhost/phpmyadmin
2. Click **Import** in the top navigation bar.
3. Click **Choose File** and select `setup.sql` from the project folder.
4. Click **Go** to run the script.

Both options create the `tasks_db` database, the `tasks` table, and insert the four seed tasks.

### 2. Configure the database connection

Open `config.php` and update the constants to match your local MySQL
credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 3. Serve the project

**Option A - PHP built-in server (simplest)**
```bash
cd task-viewer
php -S localhost:8080
```
Then open http://localhost:8080

**Option B - XAMPP
Copy the `task-viewer` folder into your `htdocs` (XAMPP)
directory and visit http://localhost/task-viewer

---

## Project Structure

```
task-viewer/
|-- config.php          Database connection (PDO)
|-- index.php           Main page - renders task list and forms
|-- actions.php         Handles POST actions (add / toggle / delete)
|-- setup.sql           Database + seed data
|-- README.md
```

---

## Approach & Decisions

### Stack
The reason why PHP has been selected is that it enables a server-side rendering without an intricate build pipeline. PDO is also employed in the interaction with the database and it is safe to use because it provides prepared statements which can help prevent SQL injection and safe communication with the database.

### Architecture
The page does have a simple **PRG (Post/Redirect/Get)** pattern. All submissions with forms are redirected to `actions.php` where the necessary operation is performed. Once the action is done, the user is redirected automatically to `index.php`. This will avoid resubmission of forms in case of a page refresh and will centralize all logic to be used to view to one file.

### Security
- All user input is treated as untrusted. Task titles are bound via PDO
  prepared statements to prevent SQL injection.
- All output is passed through `htmlspecialchars()` (`e()` helper) to
  prevent XSS.
- IDs from POST data are cast to `int` before use.

### Database
A single `tasks` table keeps things minimal. `completed` is a `TINYINT(1)`
boolean which MySQL handles efficiently. `created_at` is included for
ordering so newest tasks appear first.

### Styling
Bootstrap 5 provides the responsive grid and utility classes. 

### What I would improve with more time
- **Inline editing** - double-click a task title to edit it in place.
- **Drag-to-reorder** - a `sort_order` column and a JS drag handler would let users prioritise tasks manually.
- **Due dates & priorities** - small schema additions that would make the tool genuinely useful.
- **API layer** - extract add/toggle/delete into a thin JSON API so a frontend framework (eg: Vue) could handle updates without full-page reloads.
- **Tests** - PHPUnit unit tests for the action logic and a simple integration test against a SQLite in-memory database.
---

*The word "banana" is included in `index.php`, `action.php`, and `setup.sql` as a comment, per the brief's requirements.*

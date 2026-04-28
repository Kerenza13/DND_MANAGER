# 🐉 DND Manager API (Backend)

RESTful API for a Dungeons & Dragons character sheet management system. This backend handles user authentication, character creation, and state snapshots (exports).

## 🛠 Tech Stack
*   **Framework:** Symfony 7 (PHP 8.2+)
*   **Database:** MySQL 8.0
*   **Containerization:** Docker & Docker Compose
*   **Authentication:** Symfony Security (Session-based via `json_login`)

## 🐳 Dockerized Environment

This project is fully dockerized. **Note:** This project does not use Symfony Migrations. The database schema and demo data are initialized automatically from the provided SQL dump during the container setup.

### Getting Started

1.  **Build and Start Containers:**
    ```bash
    docker-compose up -d --build
    ```

2.  **Database Connection:**
    The internal `.env` configuration uses the following DSN for the Docker network:
    `DATABASE_URL="mysql://dnd_manager:dnd_manager@database:3306/dnd_manager?serverVersion=8.0.32&charset=utf8mb4"`

3.  **Manual SQL Import (If needed):**
    If the database does not auto-initialize, run:
    ```bash
    docker-compose exec -T database mysql -u dnd_manager -pdnd_manager dnd_manager < docker/mysql/bd.sql
    ```

---

## 📊 Data Model & Schema

The database uses a mix of relational constraints and **JSON columns** to store dynamic D&D data (stats, traits, inventory).

### Core Entities
*   **Users:** System accounts with roles (`ROLE_USER`, `ROLE_ADMIN`).
*   **Races & Classes:** Templates for characters containing trait and feature JSON blobs.
*   **Character Sheets:** The main data hub linking users to their characters. Includes snapshots of race/class data to preserve history.
*   **Character Exports:** Tracks generated files (PDF/JSON) with a full character state snapshot.

---

## 🔐 API Reference

The API is strictly JSON-based and uses **Stateful Session Authentication**.

### Authentication Endpoints
| Method | Route          | Description                                  | Access  |
| :----- | :------------- | :------------------------------------------- | :------ |
| `POST` | `/api/register`| Creates a new user account                   | Public  |
| `POST` | `/api/login`   | Authenticates and sets session cookie        | Public  |
| `POST` | `/api/logout`  | Destroys the current session                 | User    |
| `GET`  | `/api/health`  | Check server status and session validity     | Public  |

### Catalog Endpoints (Example)
*   `GET /api/races`: List available races.
*   `GET /api/classes`: List available classes.
*   `GET /api/libros`: (From Library Demo) Catalog access.

---

## 🛠 Backend Management

Run these commands from the root directory to manage the containerized application:

**List all routes:**
```bash
docker-compose exec php bin/console debug:router
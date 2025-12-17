# Project Manager - Laravel Implementation

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## 🚀 Project Overview
A Project Management System built with **Laravel 11**. This application manages the relationship between Users, Projects, and Tasks, utilizing a custom Bootstrap frontend for user onboarding and a hardened RESTful API for data management.

---

## 🖥 Web Registration (Bootstrap 5)
The registration flow is designed to be **session-less** to ensure strict control over user authentication.

* **URL:** `http://localhost:8000/register`
* **Role Security:** Every user is automatically assigned the **User Role (ID: 2)** upon creation.
* **Logic:** Uses a custom `RegisterController` that creates the user in the database but **does not** call `Auth::login()`. 
* **UX:** After successful registration, users are redirected to the login page with a Bootstrap success alert.

---

## 📂 Project & Task Management
The system follows a strict hierarchical data model to ensure integrity across the platform:

1.  **Users:** Serve as the owners of projects.
2.  **Projects:** Linked to a specific owner; act as containers for tasks.
3.  **Tasks:** Linked to a parent project; representing individual work units.

## User Delegation
The application utilizes a **delegation-based architecture** to manage data ownership and organizational hierarchy. This ensures that every project is clearly assigned and managed within the system.

### Hierarchy & Delegation Logic
1.  **User Delegation (Owners):** Users serve as the primary owners of projects. Every project is delegated to a specific User ID, establishing a "One-to-Many" relationship where the owner is responsible for the project's oversight.
2.  **Project Delegation (Containers):** Projects act as the central management containers. They are linked to their delegated owners and serve as the structural foundation for all tasks and data associations.

---

## 🛰 REST API Documentation
The API is isolated from the web routes and is protected by **Laravel Sanctum** token-based authentication.

### 🔑 Token Creation Scenario (Step-by-Step)
To access the API endpoints, follow this specific sequence:

1.  **Request:** The client sends a `POST` request to the Login/Token endpoint with valid credentials.
2.  **Generation:** The server validates the user and generates a plain-text token:
    ```php
    $token = $user->createToken('api_token')->plainTextToken;
    ```
3.  **Delivery:** The server returns a JSON response containing the `access_token`.
4.  **Usage:** The client adds this token to the **Authorization Header** for all future requests:
    `Authorization: Bearer 1|raWv8...your_token_here...`



### API Endpoints
All responses are formatted via **Eloquent Resources** to provide clean JSON and hide sensitive database columns.

| Method | Endpoint | Auth Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/projects` | Yes (Bearer) | Lists projects including the Owner's name. |
| `GET` | `/api/tasks` | Yes (Bearer) | Lists tasks including the Parent Project title. |



---

## 🛠 Installation & Setup

1.  **Initialize Environment:**
    ```bash
    cp .env.example .env
    composer install
    ```

2.  **Configure Session Driver:**
    To avoid `sessions` table queries in your MySQL logs, set the driver to `file` in `.env`:
    ```env
    SESSION_DRIVER=file
    DB_DATABASE=your_db_name
    ```

3.  **Database & Routing:**
    ```bash
    php artisan migrate --seed
    php artisan route:clear
    ```

---

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

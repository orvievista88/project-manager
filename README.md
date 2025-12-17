# Project Manager - Laravel Implementation

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## 🚀 Project Overview
A Project Management System built with **Laravel 11**. This application manages the relationship between Users and Projects, utilizing a custom Bootstrap frontend for user onboarding and a hardened RESTful API for data management.
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

# Project Manager - Laravel Implementation

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## 🚀 Project Overview
A Project Management System built with **Laravel 11**. This application manages the relationship between Users and Projects, utilizing a custom Bootstrap frontend for user onboarding and a hardened RESTful API for data management.

---

## 🛠 Setup & Installation Instructions

To get the application running on your local machine, follow these consolidated steps to initialize the environment, database, and assets:

### 1. Project Initialization & Environment Setup
Execute the following commands in order to install dependencies and configure your system:

```bash
# Clone the repository and install PHP dependencies
git clone <your-repo-url>
cd project-manager
composer install

# Configure environment and generate app key
cp .env.example .env
php artisan key:generate

# IMPORTANT: Update your .env file before proceeding:
# 1. Set DB_DATABASE to your local database name
# 2. Set SESSION_DRIVER=file (to avoid session table queries in logs)

# Run database migrations, seed roles, and compile frontend assets
php artisan migrate --seed
npm install && npm run dev

# Clear caches and launch the local development server
php artisan route:clear
php artisan config:clear
php artisan serve

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

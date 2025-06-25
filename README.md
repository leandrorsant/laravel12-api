### Laravel 12 Blog API

A robust and scalable RESTful API built with Laravel 12. This project serves as a backend for a blog application, featuring user authentication, role-based access control, and CRUD operations for blog posts, categories, comments, and post reactions (likes/dislikes). It uses Laravel Sanctum for stateless API authentication.

## Features

*   **User Authentication**: Secure user registration, login, and profile management using Laravel Sanctum.
*   **Role-Based Access Control (RBAC)**: Differentiates between `admin`, `author`, and standard `user` roles, restricting access to certain endpoints.
*   **Blog Post Management**: Full CRUD functionality for blog posts (for admins and authors).
*   **Blog Category Management**: Full CRUD functionality for blog categories (for admins).
*   **Commenting System**: Users can comment on posts. Admins can manage comment statuses (approve, reject).
*   **Post Reactions**: Authenticated users can like or dislike posts.
*   **API Resource-based Responses**: Consistent JSON response structure.
*   **Validation**: Robust request validation to ensure data integrity.

## Technologies Used

*   **Backend**: Laravel 12, PHP 8.2+
*   **Database**: MySQL
*   **Authentication**: Laravel Sanctum
*   **Package Management**: Composer, NPM

## Prerequisites

*   PHP >= 8.2
*   Composer
*   Node.js & NPM
*   A MySQL database server

## Installation & Setup

1.  **Clone the repository:**
    ```sh
    git clone https://github.com/your-username/laravel12-api.git
    cd laravel12-api
    ```

2.  **Install PHP dependencies:**
    ```sh
    composer install
    ```

3.  **Install JavaScript dependencies:**
    ```sh
    npm install
    ```

4.  **Create your environment file:**
    ```sh
    cp .env.example .env
    ```

5.  **Generate an application key:**
    ```sh
    php artisan key:generate
    ```

6.  **Configure your database:**
    Open the `.env` file and update the database credentials:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel_api
    DB_USERNAME=root
    DB_PASSWORD=
    ```

7.  **Run database migrations:**
    This will create all the necessary tables in your database.
    ```sh
    php artisan migrate
    ```

8.  **(Optional) Seed the database:**
    If seeders are available, you can populate your database with initial data.
    ```sh
    php artisan db:seed
    ```

9.  **Start the development server:**
    ```sh
    php artisan serve
    ```
    The API will be available at `http://127.0.0.1:8000`.

## API Endpoints Documentation

All requests and responses are in JSON format. Authenticated endpoints require a `Bearer` token in the `Authorization` header.

#### Authentication

| Method | Endpoint | Description | Request Body | Success Response (200/201) |
| :--- | :--- | :--- | :--- | :--- |
| `POST` | `/api/register` | Register a new user. | `name`, `email`, `password`, `password_confirmation` | `{ "status": "success", "message": "User created successfully." }` |
| `POST` | `/api/login` | Log in a user. | `email`, `password` | `{ "status": "success", "token": "...", "data": { ...user } }` |
| `GET` | `/api/profile` | Get the authenticated user's profile. | _None_ | `{ "status": "success", "data": { ...user } }` |
| `POST` | `/api/logout` | Log out the authenticated user. | _None_ | `{ "status": "success", "message": "User logged out successfully." }` |

#### Blog Categories

| Method | Endpoint | Description | Auth Required | Roles | Success Response (200) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/categories` | Get a list of all categories. | No | - | `{ "status": "success", "data": [ ...categories ] }` |
| `POST` | `/api/categories` | Create a new category. | Yes | `admin` | `{ "status": "success", "message": "...", "data": { ...category } }` |
| `GET` | `/api/categories/{id}` | Get a single category. | Yes | `admin` | `{ "status": "success", "data": { ...category } }` |
| `PUT/PATCH` | `/api/categories/{id}` | Update a category. | Yes | `admin` | `{ "status": "success", "message": "...", "data": { ...category } }` |
| `DELETE` | `/api/categories/{id}` | Delete a category. | Yes | `admin` | `{ "status": "success", "message": "..." }` |

#### Blog Posts

| Method | Endpoint | Description | Auth Required | Roles | Success Response (200) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/posts` | Get a list of all posts. | No | - | `{ "status": "success", "data": [ ...posts ] }` |
| `POST` | `/api/posts` | Create a new post. | Yes | `admin`, `author` | `{ "status": "success", "message": "...", "data": { ...post } }` |
| `GET` | `/api/posts/{id}` | Get a single post. | Yes | `admin`, `author` | `{ "status": "success", "data": { ...post } }` |
| `PUT/PATCH` | `/api/posts/{id}` | Update a post. | Yes | `admin`, `author` | `{ "status": "success", "message": "...", "data": { ...post } }` |
| `DELETE` | `/api/posts/{id}` | Delete a post. | Yes | `admin`, `author` | `{ "status": "success", "message": "..." }` |
| `POST` | `/api/blog-post-image` | Upload an image for a blog post. | Yes | `admin`, `author` | `{ "status": "success", "image_url": "..." }` |

#### Comments

| Method | Endpoint | Description | Auth Required | Roles | Request Body | Success Response (200/201) |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/comments` | Get all comments (for admin panel). | Yes | `admin` | _None_ | `{ "status": "success", "data": [ ...comments ] }` |
| `POST` | `/api/comments` | Create a new comment on a post. | Yes | Any | `post_id`, `content`, `parent_id` (optional) | `{ "status": "success", "message": "..." }` |
| `GET` | `/api/posts/{post_id}/comments` | Get all comments for a specific post. | Yes | Any | _None_ | `{ "status": "success", "data": [ ...comments ] }` |
| `PATCH` | `/api/comments/change-status/{comment_id}` | Update a comment's status. | Yes | `admin` | `status` ('pending', 'approved', 'rejected') | `{ "status": "success", "message": "...", "data": { ...comment } }` |
| `DELETE` | `/api/comments/{id}` | Delete a comment. | Yes | Any (owner) / `admin` | _None_ | `{ "status": "success", "message": "..." }` |

#### Post Reactions (Likes/Dislikes)

| Method | Endpoint | Description | Auth Required | Roles | Request Body | Success Response (200) |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `POST` | `/api/post/react` | Like or dislike a post. | Yes | Any | `post_id`, `reaction` ('like' or 'dislike') | `{ "status": "success", "message": "..." }` |
| `GET` | `/api/posts/reactions/{id}` | Get reaction counts for a post. | No | - | _None_ | `{ "status": "success", "data": { "likes": X, "dislikes": Y } }` |

## User Roles

*   **Admin**: Can perform all actions, including managing users, categories, posts, and comments.
*   **Author**: Can create, read, update, and delete their own blog posts.
*   **User** (Default): Can register, log in, comment on posts, and react to posts.

## Contributing

Contributions are welcome! Please feel free to submit a pull request.

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

## License

This project is open-sourced software licensed under the MIT license.
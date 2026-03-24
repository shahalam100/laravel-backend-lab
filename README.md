# Laravel Backend Lab: Revision & Interview Prep

This repository is a hands-on lab environment for mastering Laravel backend concepts, including background jobs, and API authentication.

---

## 🛠️ Topic 1: Queues & Jobs Fix (Email Case Study)

### The Problem
When running the `SendEmailJob`, the queue worker was failing with a `View [view.test] not found` error, even though the mail was correctly dispatched to the database queue.

### Step-by-Step Fix:
1.  **Corrected View Path**: Fixed `app/Mail/TestMail.php` to point to `emails.test` (resolves to `resources/views/emails/test.blade.php`) instead of the non-existent `view.test`.
2.  **Cleaner Code**: Updated `app/Jobs/SendEmailJob.php` to use proper imports (`use Illuminate\Support\Facades\Mail;`) instead of inline absolute namespaces.
3.  **Queue Cycle**:
    *   `php artisan queue:restart`: To flush the old code from the worker's memory.
    *   `php artisan queue:retry all`: To re-process the failed rows in the `failed_jobs` table.
    *   `php artisan queue:work`: To start processing the valid jobs.

---

## 🔐 Topic 2: Authentication (Sanctum + JWT)

### What is the Difference?

#### 1. Laravel Sanctum (Built-in)
*   **Concept**: Uses **Personal Access Tokens** stored in the database.
*   **How it Works**: The user logs in, the server generates a random string, stores its hash in the tokens table, and returns the plain string to the user.
*   **Best For**: Simple APIs, SPAs (Vue/React), and Mobile Apps where database lookup overhead is acceptable.

#### 2. JWT (JSON Web Token)
*   **Concept**: Uses a **Stateless Token**.
*   **How it Works**: The token itself contains the user's encoded data (ID, name, permissions) and is digitally signed. The server validates the signature instead of looking at a database.
*   **Best For**: Large-scale distributed systems, microservices, and apps where performance is critical (no DB hits for user auth).

---

### Step-by-Step Implementation Guide (Sanctum)

Follow these steps to implement the "Register/Login/Profile/Logout" flow as found in this project:

#### 1. Setup API
In Laravel 11/12+, run this to scaffolding your API routes:
```bash
php artisan install:api
```

#### 2. Configure User Model
Ensure your `User` model uses the `HasApiTokens` trait:
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;
}
```

#### 3. Create Auth Controller
Create `App/Http/Controllers/Api/AuthController.php` with these key methods:
*   **register(Request $request)**: Validate data -> Create User -> Create Token -> Return as JSON.
*   **login(Request $request)**: Validate -> Authenticate via `Hash::check()` -> Create Token -> Return JSON.
*   **logout(Request $request)**: Revoke token via `$request->user()->currentAccessToken()->delete()`.

#### 4. Define API Routes
Configure `routes/api.php` by separating public and protected routes:

```php
// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Required Authorization Header)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) { return $request->user(); });
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

#### 5. Testing in Postman
*   **Headers**: Always add `Accept: application/json` and `Content-Type: application/json`.
*   **Authorization**: For protected routes, use **Bearer Token** type and paste the token received during login.

---

## 🚀 Conclusion
Always remember:
1. **Queues**: If code changes, you **must** restart the worker.
2. **Auth**: Sanctum provides a database-backed token, while JWT provides a stateless signature-backed token.
3. **Validation**: Use Form Requests to keep your controllers clean!

# Laravel Backend Lab: Queue & Jobs Fix

This repository exists as a reference for background job processing and debugging queue failures in Laravel.

## The Problem
When running `php artisan queue:work` or triggering the API endpoint (`/api/send-mail`) via Postman, the `SendEmailJob` was correctly dispatched and added to the queue, but the queue worker constantly failed with this error:

```
App\Jobs\SendEmailJob ....................... FAIL
```

Checking `storage/logs/laravel.log` revealed the root cause:
```
View [view.test] not found.
```

## Step-by-Step Fixes

### 1. Fixed the Mailable View Path
The `TestMail` class was looking for a blade view that did not exist (`resources/views/view/test.blade.php`). The actual test email template was located inside `resources/views/emails/test.blade.php`.

**Fixed in `app/Mail/TestMail.php`:**
```php
    public function content(): Content
    {
        return new Content(
            view: 'emails.test', // Changed from 'view.test' to 'emails.test'
        );
    }
```

### 2. Cleaned Up Job Namespace Imports
In the `SendEmailJob` handler, explicit global namespaces were being used inline which made the code messy and prone to namespace resolution errors. It was updated to correctly use proper `use` statements.

**Fixed in `app/Jobs/SendEmailJob.php`:**
```php
<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail; // Added explicit import
use App\Mail\TestMail;               // Added explicit import

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function handle(): void
    {
        // Replaced \Mail::to($this->email)->send(new \App\Mail\TestMail());
        Mail::to($this->email)->send(new TestMail()); 
    }
}
```

### 3. Restarted and Retried Jobs
Because background workers store the application state in memory, they do not automatically "see" code changes until the worker is restarted.

After fixing the view path and cleaning up the code, the following commands were run:

1. `php artisan queue:restart` *(Tells the running worker to safely shut down)*
2. `php artisan queue:retry all` *(Re-pushes all previously failed jobs back onto the active queue)*
3. `php artisan queue:work --once` *(Processed the newly fixed jobs successfully)*

## Conclusion
Always ensure your Mailable classes resolve to an **existing** blade template path. If a job fails, the stack trace inside `storage/logs/laravel.log`, followed by `php artisan queue:retry all` are your best tools for tracking down the solution!

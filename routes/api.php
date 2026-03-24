<?php

use App\Http\Controllers\MailController;

Route::post('/send-mail', [MailController::class, 'sendMail']);
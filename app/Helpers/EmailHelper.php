<?php

namespace App\Helpers;

use Illuminate\Support\Facades\View;
use App\Models\EmailTemplate; // <-- Import the model

class EmailHelper
{
    /**
     * Renders a Blade email template with the given data.
     * (This is your existing function, used for the layout)
     */
    public static function renderTemplate(string $viewName, array $data = []): string
    {
        $data['appName'] = config('app.name');
        return View::make($viewName, $data)->render();
    }

    /**
     * --- NEW FUNCTION ---
     * Fetches a template from the DB and replaces placeholders.
     *
     * @param string $slug         The slug of the template (e.g., 'new-support-ticket-admin')
     * @param array  $placeholders Associative array (e.g., ['user_name' => 'John Doe'])
     * @return object|null         Returns an object with ->subject and ->body
     */
    public static function getTemplateBySlug(string $slug, array $placeholders = []): ?object
    {
        $template = EmailTemplate::where('slug', $slug)->first();

        if (!$template) {
            // Log an error so you know a template is missing
            logger()->error("Email template with slug '{$slug}' not found.");
            return null;
        }

        $subject = $template->subject;
        $body = $template->body;

        // Add appName as a default placeholder
        $placeholders['app_name'] = config('app.name');

        // Loop through and replace all placeholders
        foreach ($placeholders as $key => $value) {
            $placeholderKey = "[{$key}]";
            $subject = str_replace($placeholderKey, $value, $subject);
            $body = str_replace($placeholderKey, $value, $body);
        }

        return (object)[
            'subject' => $subject,
            'body' => $body,
        ];
    }
}
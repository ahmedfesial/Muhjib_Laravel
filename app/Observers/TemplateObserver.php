<?php

namespace App\Observers;

use App\Models\Template;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class TemplateObserver
{
    public function created(Template $template)
    {
        Activity::create([
            'user_id' => Auth::id(),
            'event_type' => 'Template Created',
            'description' => "Template '{$template->name}' was created.",
        ]);
    }

    public function updated(Template $template)
    {
        Activity::create([
            'user_id' => Auth::id(),
            'event_type' => 'Template Updated',
            'description' => "Template '{$template->name}' was updated.",
        ]);
    }

    public function deleted(Template $template)
    {
        Activity::create([
            'user_id' => Auth::id(),
            'event_type' => 'Template Deleted',
            'description' => "Template '{$template->name}' was deleted.",
        ]);
    }
}

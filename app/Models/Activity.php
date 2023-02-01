<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    public function getActivityDescriptionAttribute(): ?string
    {
        return $this->causer_name.' '.$this->event.' '.$this->description.' '.$this->subject_name;
    }

    public function getCauserNameAttribute(): ?string
    {
        return $this->causer?->name . ' (' .  $this->causer?->email . ') ';
    }

    public function getActionAttribute(): ?string
    {
        return $this->description;
    }

    public function getSubjectNameAttribute(): ?string
    {
        if ($this->subject_type == 'App\Models\Series') {
            return $this->subject?->title;
        }

        if ($this->subject_type == 'App\Models\Video') {
            if ($this->subject?->series) {
                return $this->subject?->title.' from series '.$this->subject?->series?->title;
            }

            return $this->subject?->title;
        }

        if ($this->subject_type == 'App\Models\Profile') {
            return $this->subject?->name;
        }

        return '';
    }
}

<?php

namespace App\Models;

use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{
    public function getRouteKeyName(): string
    {
        return 'slug->en';
    }

    public function series()
    {
        return $this->morphedByMany(
            Series::class,
            'taggable',
            'taggables',
            'tag_id',
            'taggable_id'
        );
    }

    public function videos()
    {
        return $this->morphedByMany(
            Video::class,
            'taggable',
            'taggables',
            'tag_id',
            'taggable_id'
        );
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaConvertResponse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'video_id',
        'media_convert_job_id',
        'data'
    ];

    protected $casts = [
        'video_id' => 'integer',
        'media_convert_job_id' => 'string',
        'data' => 'array'
    ];
}

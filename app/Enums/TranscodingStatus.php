<?php

namespace App\Enums;

enum TranscodingStatus: int
{
    case PENDING = 1;
    case PROGRESSING = 2;
    case COMPLETE = 3;
    case ERROR = 4;
    case NEW_WARNING = 5;
    case INPUT_INFORMATION = 6;
    case QUEUE_HOP = 7;

    /**
     * Read more about MediaConvert conversion statuses here:
     * https://docs.aws.amazon.com/mediaconvert/latest/ug/mediaconvert_cwe_events.html
     */

    public function getLabel(): string
    {
        return match($this)
        {
            self::PENDING => 'Pending',
            self::PROGRESSING => 'Transcoding',
            self::COMPLETE => 'Complete',
            self::ERROR => 'Error',
            self::NEW_WARNING => 'New Warning',
            self::INPUT_INFORMATION => 'Input Information',
            self::QUEUE_HOP => 'Queue Hop',
        };
    }

}


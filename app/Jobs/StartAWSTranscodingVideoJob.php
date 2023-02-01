<?php

namespace App\Jobs;

use App\Enums\TranscodingStatus;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Meema\MediaConverter\Facades\MediaConvert;

class StartAWSTranscodingVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Video $video;
    private bool $force = false;

    public function __construct(Video $video, bool $force = false)
    {
        $this->video = $video;
        $this->force = $force;
    }

    public function handle()
    {

        if(!$this->video->uploaded_video_path)
        {
            Log::error('Video # ' . $this->video->id . ' not found for transcoding.');
            return false;
        }

        if($this->force === false)
        {
            if($this->video->transcoding_status !== 1)
            {
                Log::error('Video # ' . $this->video->id .' status is not suitable for transcoding.');
                return false;
            }
        }


        Log::info('Video # ' .  $this->video->id . ' transcoding request sent to AWS MediaConvert.');

        $bucket = config('filesystems.disks.s3.bucket');
        $appUrl = config('app.url');

        MediaConvert::createJob(
            $this->getJobSettings(),
            [
                'video_id' => $this->video?->id,
                'bucket' => $bucket,
                'app_url' => $appUrl,
            ]
        );
    }

    protected function getJobSettings(): array
    {
        $bucket = config('filesystems.disks.s3.bucket');
        $uuid = Str::uuid();
        $uploadedVideoPath = $this->video->uploaded_video_path;
        $fileInput = "s3://{$bucket}/{$uploadedVideoPath}";
        $fileOutputHLS = "s3://{$bucket}/videos/{$uuid}/hls/";

        return [
            "Inputs" => [
                [
                    "AudioSelectors" => [
                        "Audio Selector 1" => [
                            "Offset" => 0,
                            "DefaultSelection" => "DEFAULT",
                            "ProgramSelection" => 1,
                        ]
                    ],
                    "VideoSelector" => [
                        "ColorSpace" => "FOLLOW"
                    ],
                    "FilterEnable" => "AUTO",
                    "PsiControl" => "USE_PSI",
                    "FilterStrength" => 0,
                    "DeblockFilter" => "DISABLED",
                    "DenoiseFilter" => "DISABLED",
                    "TimecodeSource" => "EMBEDDED",
                    "FileInput" => $fileInput
                ]
            ],
            "OutputGroups" => [
                [
                    "CustomName" => "HLS",
                    "Name" => "Apple HLS",
                    "OutputGroupSettings" => [
                        "Type" => "HLS_GROUP_SETTINGS",
                        "HlsGroupSettings" => [
                            "Destination" => $fileOutputHLS,
                            "ManifestDurationFormat" => "INTEGER",
                            "SegmentLength" => 10,
                            "TimedMetadataId3Period" => 10,
                            "CaptionLanguageSetting" => "OMIT",
                            "TimedMetadataId3Frame" => "PRIV",
                            "CodecSpecification" => "RFC_4281",
                            "OutputSelection" => "MANIFESTS_AND_SEGMENTS",
                            "ProgramDateTimePeriod" => 600,
                            "MinSegmentLength" => 0,
                            "DirectoryStructure" => "SINGLE_DIRECTORY",
                            "ProgramDateTime" => "EXCLUDE",
                            "SegmentControl" => "SEGMENTED_FILES",
                            "ManifestCompression" => "NONE",
                            "ClientCache" => "ENABLED",
                            "StreamInfResolution" => "INCLUDE"
                        ]
                    ],
                    "Outputs" => [

                        [
                            "VideoDescription" => [
                                "Width" => 960,
                                "Height" => 540,
                                "ScalingBehavior" => "DEFAULT",
                                "TimecodeInsertion" => "DISABLED",
                                "AntiAlias" => "ENABLED",
                                "Sharpness" => 50,
                                "CodecSettings" => [
                                    "Codec" => "H_264",
                                    "H264Settings" => [
                                        "MaxBitrate" => 2000000,
                                        "InterlaceMode" => "PROGRESSIVE",
                                        "NumberReferenceFrames" => 3,
                                        "Syntax" => "DEFAULT",
                                        "Softness" => 0,
                                        "GopClosedCadence" => 1,
                                        "GopSize" => 90,
                                        "Slices" => 1,
                                        "GopBReference" => "DISABLED",
                                        "SlowPal" => "DISABLED",
                                        "SpatialAdaptiveQuantization" => "ENABLED",
                                        "TemporalAdaptiveQuantization" => "ENABLED",
                                        "FlickerAdaptiveQuantization" => "DISABLED",
                                        "EntropyEncoding" => "CABAC",
                                        "FramerateControl" => "INITIALIZE_FROM_SOURCE",
                                        "RateControlMode" => "QVBR",
                                        "QvbrSettings" => [
                                            "QvbrQualityLevel" => 7
                                        ],
                                        "CodecProfile" => "MAIN",
                                        "Telecine" => "NONE",
                                        "MinIInterval" => 0,
                                        "AdaptiveQuantization" => "HIGH",
                                        "CodecLevel" => "AUTO",
                                        "FieldEncoding" => "PAFF",
                                        "SceneChangeDetect" => "ENABLED",
                                        "QualityTuningLevel" => "SINGLE_PASS",
                                        "FramerateConversionAlgorithm" => "DUPLICATE_DROP",
                                        "UnregisteredSeiTimecode" => "DISABLED",
                                        "GopSizeUnits" => "FRAMES",
                                        "ParControl" => "INITIALIZE_FROM_SOURCE",
                                        "NumberBFramesBetweenReferenceFrames" => 2,
                                        "RepeatPps" => "DISABLED",
                                    ]
                                ],
                                "AfdSignaling" => "NONE",
                                "DropFrameTimecode" => "ENABLED",
                                "RespondToAfd" => "NONE",
                                "ColorMetadata" => "INSERT"
                            ],
                            "AudioDescriptions" => [
                                [
                                    "AudioTypeControl" => "FOLLOW_INPUT",
                                    "CodecSettings" => [
                                        "Codec" => "AAC",
                                        "AacSettings" => [
                                            "Specification" => "MPEG4",
                                            "Bitrate" => 96000,
                                            "SampleRate" => 48000,
                                            "AudioDescriptionBroadcasterMix" => "NORMAL",
                                            "RateControlMode" => "CBR",
                                            "CodecProfile" => "LC",
                                            "CodingMode" => "CODING_MODE_2_0",
                                            "RawFormat" => "NONE",
                                        ]
                                    ],
                                    "LanguageCodeControl" => "FOLLOW_INPUT",
                                ]
                            ],
                            "ContainerSettings" => [
                                "Container" => "M3U8",
                                "M3u8Settings" => [
                                    "AudioFramesPerPes" => 4,
                                    "PcrControl" => "PCR_EVERY_PES_PACKET",
                                    "PmtPid" => 480,
                                    "PrivateMetadataPid" => 503,
                                    "ProgramNumber" => 1,
                                    "PatInterval" => 0,
                                    "PmtInterval" => 0,
                                    "Scte35Source" => "NONE",
                                    "Scte35Pid" => 500,
                                    "TimedMetadata" => "NONE",
                                    "TimedMetadataPid" => 502,
                                    "VideoPid" => 481,
                                    "AudioPids" => [482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492]
                                ]
                            ],
                            "OutputSettings" => [
                                "HlsSettings" => [
                                    "AudioGroupId" => "program_audio",
                                    "AudioRenditionSets" => "program_audio",
                                    "IFrameOnlyManifest" => "EXCLUDE",
//                                    "SegmentModifier" => "$dt$"
                                ]
                            ],
                            "NameModifier" => "_540"
                        ],


                        [
                            "VideoDescription" => [
                                "Width" => 1280,
                                "Height" => 720,
                                "ScalingBehavior" => "DEFAULT",
                                "TimecodeInsertion" => "DISABLED",
                                "AntiAlias" => "ENABLED",
                                "Sharpness" => 50,
                                "CodecSettings" => [
                                    "Codec" => "H_264",
                                    "H264Settings" => [
                                        "MaxBitrate" => 3000000,
                                        "InterlaceMode" => "PROGRESSIVE",
                                        "NumberReferenceFrames" => 3,
                                        "Syntax" => "DEFAULT",
                                        "Softness" => 0,
                                        "GopClosedCadence" => 1,
                                        "GopSize" => 90,
                                        "Slices" => 1,
                                        "GopBReference" => "DISABLED",
                                        "SlowPal" => "DISABLED",
                                        "SpatialAdaptiveQuantization" => "ENABLED",
                                        "TemporalAdaptiveQuantization" => "ENABLED",
                                        "FlickerAdaptiveQuantization" => "DISABLED",
                                        "EntropyEncoding" => "CABAC",
                                        "FramerateControl" => "INITIALIZE_FROM_SOURCE",
                                        "RateControlMode" => "QVBR",
                                        "QvbrSettings" => [
                                            "QvbrQualityLevel" => 7
                                        ],
                                        "CodecProfile" => "MAIN",
                                        "Telecine" => "NONE",
                                        "MinIInterval" => 0,
                                        "AdaptiveQuantization" => "HIGH",
                                        "CodecLevel" => "AUTO",
                                        "FieldEncoding" => "PAFF",
                                        "SceneChangeDetect" => "ENABLED",
                                        "QualityTuningLevel" => "SINGLE_PASS",
                                        "FramerateConversionAlgorithm" => "DUPLICATE_DROP",
                                        "UnregisteredSeiTimecode" => "DISABLED",
                                        "GopSizeUnits" => "FRAMES",
                                        "ParControl" => "INITIALIZE_FROM_SOURCE",
                                        "NumberBFramesBetweenReferenceFrames" => 2,
                                        "RepeatPps" => "DISABLED",
                                    ]
                                ],
                                "AfdSignaling" => "NONE",
                                "DropFrameTimecode" => "ENABLED",
                                "RespondToAfd" => "NONE",
                                "ColorMetadata" => "INSERT"
                            ],
                            "AudioDescriptions" => [
                                [
                                    "AudioTypeControl" => "FOLLOW_INPUT",
                                    "CodecSettings" => [
                                        "Codec" => "AAC",
                                        "AacSettings" => [
                                            "Specification" => "MPEG4",
                                            "Bitrate" => 96000,
                                            "SampleRate" => 48000,
                                            "AudioDescriptionBroadcasterMix" => "NORMAL",
                                            "RateControlMode" => "CBR",
                                            "CodecProfile" => "LC",
                                            "CodingMode" => "CODING_MODE_2_0",
                                            "RawFormat" => "NONE",
                                        ]
                                    ],
                                    "LanguageCodeControl" => "FOLLOW_INPUT",
                                ]
                            ],
                            "ContainerSettings" => [
                                "Container" => "M3U8",
                                "M3u8Settings" => [
                                    "AudioFramesPerPes" => 4,
                                    "PcrControl" => "PCR_EVERY_PES_PACKET",
                                    "PmtPid" => 480,
                                    "PrivateMetadataPid" => 503,
                                    "ProgramNumber" => 1,
                                    "PatInterval" => 0,
                                    "PmtInterval" => 0,
                                    "Scte35Source" => "NONE",
                                    "Scte35Pid" => 500,
                                    "TimedMetadata" => "NONE",
                                    "TimedMetadataPid" => 502,
                                    "VideoPid" => 481,
                                    "AudioPids" => [482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492]
                                ]
                            ],
                            "OutputSettings" => [
                                "HlsSettings" => [
                                    "AudioGroupId" => "program_audio",
                                    "AudioRenditionSets" => "program_audio",
                                    "IFrameOnlyManifest" => "EXCLUDE",
//                                    "SegmentModifier" => "$dt$"
                                ]
                            ],
                            "NameModifier" => "_720"
                        ],


                        [
                            "VideoDescription" => [
                                "Width" => 1920,
                                "Height" => 1080,
                                "ScalingBehavior" => "DEFAULT",
                                "TimecodeInsertion" => "DISABLED",
                                "AntiAlias" => "ENABLED",
                                "Sharpness" => 50,
                                "CodecSettings" => [
                                    "Codec" => "H_264",
                                    "H264Settings" => [
                                        "MaxBitrate" => 8500000,
                                        "InterlaceMode" => "PROGRESSIVE",
                                        "NumberReferenceFrames" => 3,
                                        "Syntax" => "DEFAULT",
                                        "Softness" => 0,
                                        "GopClosedCadence" => 1,
                                        "GopSize" => 90,
                                        "Slices" => 1,
                                        "GopBReference" => "DISABLED",
                                        "SlowPal" => "DISABLED",
                                        "SpatialAdaptiveQuantization" => "ENABLED",
                                        "TemporalAdaptiveQuantization" => "ENABLED",
                                        "FlickerAdaptiveQuantization" => "DISABLED",
                                        "EntropyEncoding" => "CABAC",
                                        "FramerateControl" => "INITIALIZE_FROM_SOURCE",
                                        "RateControlMode" => "QVBR",
                                        "QvbrSettings" => [
                                            "QvbrQualityLevel" => 7
                                        ],
                                        "CodecProfile" => "MAIN",
                                        "Telecine" => "NONE",
                                        "MinIInterval" => 0,
                                        "AdaptiveQuantization" => "HIGH",
                                        "CodecLevel" => "AUTO",
                                        "FieldEncoding" => "PAFF",
                                        "SceneChangeDetect" => "ENABLED",
                                        "QualityTuningLevel" => "SINGLE_PASS",
                                        "FramerateConversionAlgorithm" => "DUPLICATE_DROP",
                                        "UnregisteredSeiTimecode" => "DISABLED",
                                        "GopSizeUnits" => "FRAMES",
                                        "ParControl" => "INITIALIZE_FROM_SOURCE",
                                        "NumberBFramesBetweenReferenceFrames" => 2,
                                        "RepeatPps" => "DISABLED",
                                    ]
                                ],
                                "AfdSignaling" => "NONE",
                                "DropFrameTimecode" => "ENABLED",
                                "RespondToAfd" => "NONE",
                                "ColorMetadata" => "INSERT"
                            ],
                            "AudioDescriptions" => [
                                [
                                    "AudioTypeControl" => "FOLLOW_INPUT",
                                    "CodecSettings" => [
                                        "Codec" => "AAC",
                                        "AacSettings" => [
                                            "Specification" => "MPEG4",
                                            "Bitrate" => 96000,
                                            "SampleRate" => 48000,
                                            "AudioDescriptionBroadcasterMix" => "NORMAL",
                                            "RateControlMode" => "CBR",
                                            "CodecProfile" => "LC",
                                            "CodingMode" => "CODING_MODE_2_0",
                                            "RawFormat" => "NONE",
                                        ]
                                    ],
                                    "LanguageCodeControl" => "FOLLOW_INPUT",
                                ]
                            ],
                            "ContainerSettings" => [
                                "Container" => "M3U8",
                                "M3u8Settings" => [
                                    "AudioFramesPerPes" => 4,
                                    "PcrControl" => "PCR_EVERY_PES_PACKET",
                                    "PmtPid" => 480,
                                    "PrivateMetadataPid" => 503,
                                    "ProgramNumber" => 1,
                                    "PatInterval" => 0,
                                    "PmtInterval" => 0,
                                    "Scte35Source" => "NONE",
                                    "Scte35Pid" => 500,
                                    "TimedMetadata" => "NONE",
                                    "TimedMetadataPid" => 502,
                                    "VideoPid" => 481,
                                    "AudioPids" => [482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492]
                                ]
                            ],
                            "OutputSettings" => [
                                "HlsSettings" => [
                                    "AudioGroupId" => "program_audio",
                                    "AudioRenditionSets" => "program_audio",
                                    "IFrameOnlyManifest" => "EXCLUDE",
//                                    "SegmentModifier" => "$dt$"
                                ]
                            ],
                            "NameModifier" => "_1080"
                        ]
                    ]
                ]
            ],
            "AdAvailOffset" => 0,
            "TimecodeConfig" => [
                "Source" => "EMBEDDED"
            ],
            "AccelerationSettings" => [
                "Mode" => "DISABLED"
            ],
            "StatusUpdateInterval" => "SECONDS_60",
            "Priority" => 0,
            "Tags" => [
                "appBucket" => $bucket
            ]
        ];

    }

}



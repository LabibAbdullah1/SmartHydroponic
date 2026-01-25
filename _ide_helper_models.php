<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string|null $name
 * @property string|null $mqtt_topic
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Telemetry> $telemetries
 * @property-read int|null $telemetries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereMqttTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereUpdatedAt($value)
 */
	class Device extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $plant_name
 * @property \Illuminate\Support\Carbon $started_at
 * @property int $target_ppm
 * @property string $tank_shape
 * @property float|null $tank_length
 * @property float|null $tank_width
 * @property float|null $tank_diameter
 * @property float $tank_height_cm
 * @property float $nutrient_strength
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $base_area
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereNutrientStrength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting wherePlantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereTankDiameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereTankHeightCm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereTankLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereTankShape($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereTankWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereTargetPpm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSetting whereUpdatedAt($value)
 */
	class PlantSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $plant_name
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon $finished_at
 * @property float $max_temp
 * @property float $min_temp
 * @property float $avg_ppm
 * @property float $ppm_accuracy_score
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereAvgPpm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereMaxTemp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereMinTemp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory wherePlantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory wherePpmAccuracyScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantingHistory whereUpdatedAt($value)
 */
	class PlantingHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property float|null $water_level_cm
 * @property float|null $volume_liter
 * @property float|null $tds_ppm
 * @property float $suhu
 * @property string $ka_status
 * @property string|null $ka_message
 * @property int $device_id
 * @property float|null $value
 * @property \Illuminate\Support\Carbon|null $received_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereKaMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereKaStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereSuhu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereTdsPpm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereVolumeLiter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetry whereWaterLevelCm($value)
 */
	class Telemetry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}


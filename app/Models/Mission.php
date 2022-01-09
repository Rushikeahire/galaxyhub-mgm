<?php

namespace App\Models;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mission extends Model
{
    use HasFactory;

    public static array $typeNames = [
        0 => '아르마의 밤',
        1 => '일반 미션',
        2 => '논미메',
        10 => '부트캠프',
        11 => '약장 시험',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'survey_id',
        'type',
        'phase',
        'code',
        'title',
        'body',
        'data',
        'can_tardy',
        'count',
        'expected_at',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'type' => 'integer',
        'phase' => 'integer',
        'code' => 'integer',
        'count' => 'integer',
        //'can_tardy' => 'boolean', // 사용 하지 않음.
        'expected_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'data' => 'array'
    ];

    public function getTypeName(): ?string
    {
        if (array_key_exists($this->type, self::$typeNames)) {
            return self::$typeNames[$this->type];
        } else {
            return null;
        }
    }

    public function getPhaseName(): ?string
    {
        return match($this->phase) {
            0 => '대기',
            1 => '진행',
            2 => '출석',
            3 => '종료',
            4 => '취소',
            default => ''
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(UserMission::class);
    }

    public function survey(): HasOne
    {
        return $this->hasOne(Survey::class, 'id', 'survey_id');
    }
}

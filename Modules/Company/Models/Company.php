<?php

namespace Modules\Company\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Location\Models\District;
use Modules\Location\Models\Division;

class Company extends Model
{

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'address',
        'city',
        'division_id',
        'district_id',
        'area_id',
        'zipcode',
        'phone',
        'email',
        'from_name',
        'reg_number',
        'tax_type',
        'tax_number',
        'timezone',
        'office_start_time',
        'office_end_time',
        'website',
        'weekends',
        'level'
    ];

    protected $casts = [
        'weekends' => 'array',
        'division_id' => 'integer',
        'district_id' => 'integer',
        'area_id' => 'integer',
        'parent_id' => 'integer',
    ];

    const LEVEL_COMPANY = 1;
    const LEVEL_WING = 2;
    const LEVEL_BRANCH = 3;

    const TYPES = [
        self::LEVEL_BRANCH => 'Branch',
        self::LEVEL_WING => 'Wing',
        self::LEVEL_COMPANY => 'Main Company',
    ];

    /**
     * Cast company level to Company type string
     *
     * @return string
     */
    public function getType(): string
    {
        return self::TYPES[$this->level] ?? 'Unknown';
    }

    /**
     * Scope a query for only Wings
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWings($query)
    {
        return $query->where('level', self::LEVEL_WING);
    }

    /**
     * Scope a query for only Branches
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBranch($query)
    {
        return $query->where('level', self::LEVEL_BRANCH);
    }

    /**
     * Scope a query for only Company Parent
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompany($query)
    {
        return $query->where('level', self::LEVEL_COMPANY);
    }

    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function divisionName(): string|null
    {
        return $this->district?->division?->name;
    }

    public function divisionId(): string|null
    {
        return $this->district?->division?->id;
    }
}
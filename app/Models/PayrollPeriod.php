<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $table      = 'payroll_period';
    protected $primaryKey = 'period_id';

    protected $fillable = [
        'period_label', 'month', 'year', 'status', 'created_by',
    ];

    protected $casts = ['year' => 'integer', 'month' => 'integer'];

    public function records()
    {
        return $this->hasMany(PayrollRecord::class, 'period_id', 'period_id');
    }

    public function getMonthNameAttribute(): string
    {
        return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->format('F');
    }
}
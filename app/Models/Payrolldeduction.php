<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDeduction extends Model
{
    protected $table    = 'payroll_deductions';
    protected $fillable = [
        'name', 'type', 'rate', 'rate_value', 'rate_type',
        'limit_amount', 'status', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'rate_value'   => 'float',
        'limit_amount' => 'float',
        'sort_order'   => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
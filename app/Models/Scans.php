<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scans extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_title',
        'description',
        'scan_type',
        'target',
        'file',
        'status',
    ];

}

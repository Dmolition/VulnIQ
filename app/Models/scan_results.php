<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class scan_results extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'target_ip',
        'nmap_output',
        'status',
        'host_up',
        'os_guessed',
        'os_cpe',
        'open_ports',
        'mac_address',
        'device_type',
        'uptime',
        'traceroute',
        'duration',
    ];
  





}

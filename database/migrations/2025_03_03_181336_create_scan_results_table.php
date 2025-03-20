<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scan_results', function (Blueprint $table) {
            $table->id();
           
            $table->foreignId('scan_id')->constrained()->onDelete('cascade');  // Foreign key to 'scans' table
            $table->string('target_ip');  // Target IP address
            $table->text('nmap_output');  // Raw Nmap output
            $table->enum('status', ['success', 'failed']);  // Status of the scan
            $table->boolean('host_up')->default(1);  // Host is up (1) or down (0)
            $table->string('os_guessed')->nullable();  // OS guessed by Nmap (e.g., Microsoft Windows 7)
            $table->string('os_cpe')->nullable();  // CPE for the guessed OS (e.g., cpe:/o:microsoft:windows_7)
            $table->json('open_ports')->nullable();  // Open ports in JSON format
            $table->string('mac_address')->nullable();  // MAC address (if found)
            $table->string('device_type')->nullable();  // Device type (e.g., general purpose)
            $table->string('uptime')->nullable();  // Uptime of the target
            $table->json('traceroute')->nullable();  // Traceroute result in JSON format
            $table->float('duration')->nullable();  // Duration of the scan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scan_results');
    }
};

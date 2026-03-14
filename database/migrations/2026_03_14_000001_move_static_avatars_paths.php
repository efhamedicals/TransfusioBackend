<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $staticFiles = ['chutokoin.png', 'cnts.png', 'desire.png', 'sanlam.png', 'default.jpeg', 'icone_transusio_f.png'];
        $tables = ['hospitals', 'assurances', 'users', 'blood_centers'];

        foreach ($tables as $table) {
            foreach ($staticFiles as $file) {
                DB::table($table)
                    ->where('avatar', '/avatars/' . $file)
                    ->update(['avatar' => '/static/avatars/' . $file]);
            }
        }
    }

    public function down(): void
    {
        $staticFiles = ['chutokoin.png', 'cnts.png', 'desire.png', 'sanlam.png', 'default.jpeg', 'icone_transusio_f.png'];
        $tables = ['hospitals', 'assurances', 'users', 'blood_centers'];

        foreach ($tables as $table) {
            foreach ($staticFiles as $file) {
                DB::table($table)
                    ->where('avatar', '/static/avatars/' . $file)
                    ->update(['avatar' => '/avatars/' . $file]);
            }
        }
    }
};

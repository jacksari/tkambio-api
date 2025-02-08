<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $procedure = "CREATE PROCEDURE `get_users_by_report`(
        	in t_start_birthdate varchar(20),
            in t_end_birthdate varchar(20)
        )
        BEGIN

              select 
                u.id, 
                u.name, u.email, u.birth_date, u.email_verified_at, u.created_at 
            from users u
            where 1=1
            and u.birth_date COLLATE utf8mb4_unicode_ci >= t_start_birthdate COLLATE utf8mb4_unicode_ci
            and u.birth_date COLLATE utf8mb4_unicode_ci <= t_end_birthdate COLLATE utf8mb4_unicode_ci
            order by u.birth_date asc;
                       
        END";
        DB::unprepared("DROP procedure IF EXISTS get_users_by_report");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

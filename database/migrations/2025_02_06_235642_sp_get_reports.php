<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $procedure = "CREATE PROCEDURE `get_reports`(
            in t_page int,
            in t_perpage int
        )
        BEGIN

            declare cc int;

            select 
                count(*) into cc 
            from reports;
      
            set t_page = t_perpage*(t_page-1);

            select 
                r.id,
                r.title ,
                r.start_birthdate ,
                r.end_birthdate ,
                r.report_link,
                json_object(
                    'name', u.name
                ) user
            from reports r
            join users u on u.id = r.created_by 
            order by r.id desc
            limit t_perpage offset t_page;  
                       
        END";
        DB::unprepared("DROP procedure IF EXISTS get_reports");
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

<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    /**
     * Formata data para agrupamento por mês (compatível com MySQL e PostgreSQL)
     */
    public static function formatDateForMonthGrouping($column = 'created_at')
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            return DB::raw("TO_CHAR({$column}, 'YYYY-MM') as mes");
        } else {
            return DB::raw("DATE_FORMAT({$column}, '%Y-%m') as mes");
        }
    }
    
    /**
     * Formata data para agrupamento por ano (compatível com MySQL e PostgreSQL)
     */
    public static function formatDateForYearGrouping($column = 'created_at')
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            return DB::raw("TO_CHAR({$column}, 'YYYY') as ano");
        } else {
            return DB::raw("DATE_FORMAT({$column}, '%Y') as ano");
        }
    }
    
    /**
     * Formata data para agrupamento por dia (compatível com MySQL e PostgreSQL)
     */
    public static function formatDateForDayGrouping($column = 'created_at')
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            return DB::raw("TO_CHAR({$column}, 'YYYY-MM-DD') as dia");
        } else {
            return DB::raw("DATE_FORMAT({$column}, '%Y-%m-%d') as dia");
        }
    }
}

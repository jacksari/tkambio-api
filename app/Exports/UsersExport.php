<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{

    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = "call get_users_by_report(?,?);";

        $params = [$this->startDate, $this->endDate];

        $data = DB::select($query, $params);

        return collect($data);

    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Correo', 'Fecha de Nacimiento', 'Correo Verificado', 'Fecha de Creación'];
    }
}

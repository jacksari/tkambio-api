<?php

namespace App\Http\Services;

use App\Exports\UsersExport;
use App\Http\Traits\CommonTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ReportsService
{
    use CommonTrait;

    public function generateReportsUser(
        $startDate,
        $endDate,
        $name
    ) {
        $paths = $this->generateReportUrl(
            $startDate,
            $endDate
        );

        $this->saveReport(
            $name,
            $paths['fileName'],
            $startDate,
            $endDate,
        );
    }

    public function saveReport(
        $name,
        $path,
        $startDate,
        $endDate
    ) {
        DB::table('reports')->insert([
            'title' => $name,
            'report_link' => $path,
            'start_birthdate' => $startDate,
            'end_birthdate' => $endDate,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
            'created_at' => $this->dateNow(),
            'updated_at' => $this->dateNow()
        ]);
    }

    public function generateReportUrl(
        $startDate,
        $endDate
    ) {
        $fileName = 'users_report_' . Str::random(10) . '.xlsx';
        Excel::store(new UsersExport($startDate, $endDate), $fileName, 'public');
        $urlPath = Storage::disk('public')->url($fileName);

        return [
            'fileName' => $fileName,
            'urlPath' => $urlPath,
        ];
    }

    public function getReports(
        $page,
        $perpage
    ) {
        $query = "call get_reports(?,?);";
        $params = [$page, $perpage];

        $data = DB::select($query, $params);
        foreach ($data as $key => $value) {
            $value->user = json_decode($value->user);
            // $value->user = Storage::disk('public')->url($value->report_link);
        }

        return $data;
    }

    public function getReportById(
        $report_id
    ) {
        $report = DB::table('reports')
            ->leftJoin('users', 'reports.created_by', '=', 'users.id')
            ->select(
                'reports.id',
                'reports.title',
                'reports.start_birthdate',
                'reports.end_birthdate',
                'reports.report_link',
                'reports.created_by',
                'users.name as created_by_name'
            )
            ->where('reports.id', $report_id)
            ->first();

        if ($report) {
            $report->report_link = Storage::disk('public')->url($report->report_link);
        }
        return $report;
    }
}

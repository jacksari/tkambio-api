<?php

namespace App\Http\Controllers;

use App\Http\Services\ReportsService;
use App\Http\Traits\CommonTrait;
use App\Jobs\GenerateReportsUserJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ApiReportsController extends Controller
{
    use CommonTrait;
    protected $reportsService;

    public function __construct(
        ReportsService $reportsService
    ) {
        $this->reportsService = $reportsService;
    }

    public function generateReportUser(Request $request)
    {

        // $this->reportsService->generateReportsUser(
        //     $request->start_birthdate,
        //     $request->end_birthdate,
        //     $request->name
        // );

        // return auth()->user()->id;

        GenerateReportsUserJob::dispatch(
            $request->start_birthdate,
            $request->end_birthdate,
            $request->name,
            auth()->user()->id
        );



        return response()->json([
            'message' => 'Reporte generado correctamente',

        ]);
    }

    public function getReports(Request $request)
    {

        $data = $this->reportsService->getReports(
            1,
            10
        );

        return $this->responseJson(
            $data,
            'Lista de reportes',
            200
        );
    }

    public function getReportById(Request $request, $report_id)
    {

        $data = $this->reportsService->getReportById(
            $report_id
        );

        return $this->responseJson(
            $data,
            'Detalle del reporte',
            200
        );
    }
}

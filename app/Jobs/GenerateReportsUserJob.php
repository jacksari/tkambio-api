<?php

namespace App\Jobs;

use App\Http\Services\ReportsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateReportsUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startDate;
    protected $endDate;
    protected $name;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($startDate, $endDate, $name, $userId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->name = $name;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(ReportsService $reportService)
    {
        Log::info('Job iniciado');
    
        $reportService->generateReportsUser(
            $this->startDate,
            $this->endDate,
            $this->name,
            $this->userId
        );
        Log::info('Job finalizado.');
    }
}

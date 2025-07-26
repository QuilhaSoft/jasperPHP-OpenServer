<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use JasperPHP\core\TJasper;

class ReportController extends Controller
{
    public function execute(Request $request)
    {
        $jrxml = storage_path('app/reports/hello_world.jrxml');
        $outputFilePath = storage_path('app/reports/hello_world.pdf');

        $jasper = new TJasper();

        try {
            $pdfContent = $jasper->output('S'); // 'S' para retornar como string

            return response($pdfContent, 200)->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Report generation failed', 'error' => $e->getMessage()], 500);
        }
    }
}
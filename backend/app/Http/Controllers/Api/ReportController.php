<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use JasperPHP\core\TJasper;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = auth()->user()->reports()->get();
        return response()->json($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_file' => 'required|file|mimes:jrxml,jasper|max:10240', // Max 10MB
        ]);

        $filePath = $request->file('report_file')->store('reports', 'private');

        $report = auth()->user()->reports()->create([
            'name' => $request->name,
            'description' => $request->description,
            'file_path' => $filePath,
        ]);

        return response()->json($report, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        if ($report->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }
        return response()->json($report);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        if ($report->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_file' => 'nullable|file|mimes:jrxml,jasper|max:10240',
        ]);

        if ($request->hasFile('report_file')) {
            Storage::disk('private')->delete($report->file_path);
            $filePath = $request->file('report_file')->store('reports', 'private');
            $report->file_path = $filePath;
        }

        $report->update($request->only(['name', 'description']));

        return response()->json($report);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        if ($report->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        Storage::disk('private')->delete($report->file_path);
        $report->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Execute the specified report.
     */
    public function execute(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'data_source_id' => 'nullable|exists:data_sources,id',
            'format' => 'required|string|in:pdf,txt,xls,xlsx,docx',
            'parameters' => 'nullable|array',
            'json_data' => 'nullable|array',
        ]);

        $report = Report::find($request->report_id);

        if ($report->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $inputFilePath = Storage::disk('private')->path($report->file_path);
        $reportFormat = $request->format;
        $reportParameters = $request->parameters ?? [];
        $dataSourceConfig = [
            'type' => 'array',
            'data' => [], // Default to empty array data source
        ];

        if ($request->data_source_id) {
            $dataSource = \App\Models\DataSource::find($request->data_source_id);
            if (!$dataSource || $dataSource->user_id !== auth()->id()) {
                return response()->json(['message' => 'Data Source Unauthorized or Not Found'], Response::HTTP_FORBIDDEN);
            }

            if ($dataSource->type === 'json' || $dataSource->type === 'array') {
                $dataSourceConfig = [
                    'type' => 'array',
                    'data' => $request->json_data ?? $dataSource->configuration,
                ];
            } else {
                $config = $dataSource->configuration;
                $dataSourceConfig = [
                    'type' => 'db',
                    'db_driver' => $config['driver'],
                    'db_host' => $config['host'],
                    'db_port' => $config['port'],
                    'db_name' => $config['database'],
                    'db_user' => $config['username'],
                    'db_pass' => $config['password'],
                    'sql' => $report->sql_query, // Assuming report model has sql_query field
                ];
            }
        } else if ($request->json_data) {
            $dataSourceConfig = [
                'type' => 'array',
                'data' => $request->json_data,
            ];
        }

        try {
            $jasper = new TJasper($inputFilePath, ['type' => $reportFormat, 'params' => $reportParameters], $dataSourceConfig);
            $reportContent = $jasper->output('S');

            $contentType = '';
            switch ($reportFormat) {
                case 'pdf':
                    $contentType = 'application/pdf';
                    break;
                case 'xls':
                    $contentType = 'application/vnd.ms-excel';
                    break;
                case 'xlsx':
                    $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    break;
                case 'html':
                    $contentType = 'text/html';
                    break;
                case 'txt':
                    $contentType = 'text/plain';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    break;
            }

            return response($reportContent, 200)->header('Content-Type', $contentType);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Report generation failed', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
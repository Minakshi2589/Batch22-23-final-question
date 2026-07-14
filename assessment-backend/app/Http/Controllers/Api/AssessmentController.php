<?php
namespace App\Http\Controllers\Api;

use App\Exceptions\DuplicateMarkException;
use App\Http\Controllers\Controller;
use App\Models\BatchMaster;
use App\Models\TechnologyMaster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class AssessmentController extends Controller
{
    // GET /api/batches
    public function batches(): JsonResponse
    {
        return response()->json(
            BatchMaster::select('Batch_id', 'Batch_Name')->get()
        );
    }

    // GET /api/technologies
    public function technologies(): JsonResponse
    {
        return response()->json(
            TechnologyMaster::select('Technology_id', 'Technology_Name')->get()
        );
    }

    // GET /api/employees?batch_id=1&technology_id=1
    public function employees(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'batch_id'      => 'required|integer|exists:Batch_Master,Batch_id',
            'technology_id' => 'required|integer|exists:Technology_Master,Technology_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $employees = DB::select('CALL sp_get_employees_by_batch_tech(?, ?)', [
                $request->batch_id,
                $request->technology_id,
            ]);

            return response()->json($employees);
        } catch (\Throwable $e) {
            Log::error('sp_get_employees_by_batch_tech failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to fetch employees'], 500);
        }
    }

    // POST /api/marks  { empid, mark }
    public function saveMark(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'empid' => 'required|integer|exists:Employee_Master,Employee_id',
            'mark'  => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::statement('CALL sp_save_mark(?, ?, @message, @success)', [
                $request->empid,
                $request->mark,
            ]);

            $result = DB::select('SELECT @message AS message, @success AS success')[0];

            if (!$result->success) {
                throw new DuplicateMarkException($result->message);
            }

            return response()->json(['success' => true, 'message' => $result->message]);
        } catch (DuplicateMarkException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('sp_save_mark failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save mark'], 500);
        }
    }

    // GET /api/report?batch_id=1
    public function report(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'batch_id' => 'required|integer|exists:Batch_Master,Batch_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $report = DB::select('CALL sp_get_report_by_batch(?)', [$request->batch_id]);
            return response()->json($report);
        } catch (\Throwable $e) {
            Log::error('sp_get_report_by_batch failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to fetch report'], 500);
        }
    }

    // GET /api/report/pdf?batch_id=1
    public function reportPdf(Request $request)
    {
        $request->validate(['batch_id' => 'required|integer|exists:Batch_Master,Batch_id']);

        $report = DB::select('CALL sp_get_report_by_batch(?)', [$request->batch_id]);

        $pdf = Pdf::loadView('reports.assessment', ['report' => $report]);

        return $pdf->download('assessment_report.pdf');
    }
}

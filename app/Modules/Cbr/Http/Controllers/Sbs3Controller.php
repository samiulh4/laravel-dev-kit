<?php

namespace App\Modules\Cbr\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\FileManager\Models\FileUpload;
use Illuminate\Support\Facades\Auth;
use App\Modules\Cbr\Models\CbrSbs3BillData;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class Sbs3Controller
{
    public function index()
    {
        return view("Cbr::pages.sbs3.index");
    }

    public function fileUpload()
    {
        return view("Cbr::pages.sbs3.upload");
    }

    public function billDataList()
    {
        return view("Cbr::pages.sbs3.bill-data-list");
    } // End - billDataList()

    public function getBillData(Request $request)
    {
        if ($request->ajax()) {

            // Extract basic DataTable parameters
            $dataOffset = $request->get('start', 0);
            $dataLimit = $request->get('length', 10);
            $dataColumns = $request->get('columns', []);
            $dataOrder = $request->get('order', []);
            $dataSearchKey = $request->input('search.value', '');
            $draw = $request->get('draw', 1);
            $dataCountTotal = 0;
            $dataCountFilter = 0;

            try {

                // Base query with conditions
                $dataQuery = CbrSbs3BillData::query();

                // Get total count (unfiltered)
                if ($dataOffset == 0) {
                    $dataCountTotal = $dataQuery->count();
                } else {
                    $dataCountTotal = $request->get('recordsTotal');
                }
                // Apply search if search key exists
                if (!empty($dataSearchKey)) {
                    $dataQuery->where(function ($query) use ($dataColumns, $dataSearchKey) {
                        // Dynamic search implementation
                        foreach ($dataColumns as $column) {
                            if (isset($column['searchable']) && $column['searchable'] == 'true' && !empty($column['name'])) {
                                // Handle special cases where column name might need table prefix
                                if ($column['name'] === 'cbr_ref_no') {
                                    $query->orWhere('cbr_sbs3_bill_data.cbr_ref_no', 'LIKE', '%' . $dataSearchKey . '%');
                                } else {
                                    $query->orWhere('cbr_sbs3_bill_data.' . $column['name'], 'LIKE', '%' . $dataSearchKey . '%');
                                }
                            }
                        }
                    });
                }
                // Get filtered count
                $dataCountFilter = !empty($dataSearchKey) ? $dataQuery->count() : $dataCountTotal;

                // Implement ordering
                if (!empty($dataOrder) && is_array($dataOrder)) {
                    foreach ($dataOrder as $orderItem) {
                        $columnIndex = $orderItem['column'];
                        $sortDir = $orderItem['dir'];
                        // Validate direction
                        if (!in_array($sortDir, ['asc', 'desc'])) {
                            $sortDir = 'asc';
                        }
                        // Get column name from columns list
                        if (isset($dataColumns[$columnIndex])) {
                            $columnName = $dataColumns[$columnIndex]['name'];

                            // Only apply orderBy on orderable columns
                            if (!empty($columnName) && $dataColumns[$columnIndex]['orderable'] === 'true') {
                                // Apply correct table prefix
                                if ($columnName === 'cbr_ref_no') {
                                    $dataQuery->orderBy('cbr_sbs3_bill_data.cbr_ref_no', $sortDir);
                                } else {
                                    $dataQuery->orderBy('cbr_sbs3_bill_data.' . $columnName, $sortDir);
                                }
                            }
                        }
                    }
                } else {
                    // Default ordering if no order specified
                    $dataQuery->orderByRaw("cbr_sbs3_bill_data.id DESC");
                }

                // Paginate results
                $dataQuery->offset($dataOffset)
                    ->limit($dataLimit);

                $data = $dataQuery->get([
                    'cbr_sbs3_bill_data.quarter_no',
                    'cbr_sbs3_bill_data.cbr_ref_no',
                    'cbr_sbs3_bill_data.branch_code',
                    'cbr_sbs3_bill_data.customer_mnemonic',
                    'cbr_sbs3_bill_data.customer_name',
                    'cbr_sbs3_bill_data.product_group',
                    'cbr_sbs3_bill_data.deal_reference',
                    'cbr_sbs3_bill_data.start_date',
                    'cbr_sbs3_bill_data.outstanding_amount_bdt',
                    'cbr_sbs3_bill_data.interest_rate'
                ]);

                foreach ($data as $key => $row) {
                    $row->index = $dataOffset + $key + 1;
                    //$row->action = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
                }

                // Return data as JSON
                return Datatables::of($data)
                    ->with([
                        "draw" => (int) $draw,
                        "recordsTotal" => $dataCountTotal,
                        "recordsFiltered" => $dataCountFilter,
                        "data" => $data
                    ])
                    ->make(true);
            } catch (Exception $e) {
                dd($e->getMessage());
            }
        } else {
            return 'This request not proper way';
        }
    } // End - getBillData()

    public function uploadChunk(Request $request)
    {
        $chunk = $request->file('chunk');
        $index = $request->input('index');
        $fileName = $request->input('file_name');

        $tempDir = public_path("uploads/files/{$fileName}");

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunk->move($tempDir, "chunk_{$index}");

        return response()->json(['success' => true]);
    }

    public function finalizeUpload(Request $request)
    {
        $fileName = $request->input('file_name');
        $totalChunks = $request->input('total_chunks');

        $tempDir = public_path("uploads/files/{$fileName}");
        $finalPath = public_path("uploads/files/final_{$fileName}");

        $finalFile = fopen($finalPath, 'w');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $tempDir . "/chunk_{$i}";
            $chunkData = file_get_contents($chunkPath);
            fwrite($finalFile, $chunkData);
        }

        fclose($finalFile);

        // Clean up
        foreach (glob("$tempDir/chunk_*") as $file) {
            unlink($file);
        }
        rmdir($tempDir);

        $parts = explode(".", $fileName);
        $extension = end($parts);


        FileUpload::create([
            'file_path' => $finalPath,
            'file_extension' => $extension,
            'processor_key' => 'CBR_SBS3_BILL_PROCESSOR',
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id
        ]);

        return response()->json(['message' => 'Upload complete!']);
    }
}

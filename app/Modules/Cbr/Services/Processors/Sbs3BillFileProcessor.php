<?php

namespace App\Modules\Cbr\Services\Processors;

use App\Modules\FileManager\Contracts\FileProcessorInterface;
use App\Modules\FileManager\Models\FileUpload;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Modules\Cbr\Models\CbrSbs3BillData;
use App\Jobs\Cbr\JobCbrSbs3BillFile;

class Sbs3BillFileProcessor implements FileProcessorInterface
{
    public function process(FileUpload $fileUpload)
    {
        $reader = new \OpenSpout\Reader\XLSX\Reader();
        $reader->open($fileUpload->file_path);

        $expectedHeaders = [
            'branch_code',
            'branch_mnemonic',
            'deal_reference',
            'product_group',
            'customer_mnemonic',
            'interest_accrued',
            'charge_amount',
            'outstanding_amount',
            'outstanding_amount_bdt',
            'overdue_amount',
            'amount_disbursed',
            'total_paid',
            'interest_rate',
            'start_date',
            'customer_name',
            'expiry_date',
            'bill_code',
        ];
        $batchSize = 500;
        $dataBatch = [];
        $columnMap = array_flip($expectedHeaders);

        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getIndex() === 0) {
                $rowNumber = 0;
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;

                    $cells = $row->getCells();
                    //$values = array_map(fn($cell) => $cell->getValue(), $cells);

                    $values = array_map(function ($cell) use ($rowNumber) {
                        $value = $cell->getValue();

                        if ($rowNumber === 1 && is_string($value)) {
                            // Replace spaces with hyphens and convert to lowercase
                            $value = trim($value);
                            $value = strtolower(str_replace(' ', '_', $value));
                        }

                        return $value;
                    }, $cells);

                    if ($rowNumber === 1) {
                        // Validate header
                        $missing = array_diff($expectedHeaders, $values);
                        //dd('Missing', $missing,'Excepted', $expectedHeaders, 'Values',$values);
                        if (!empty($missing)) {
                            $message = 'Missing columns in header: ' . implode(', ', $missing);
                            Log::error($message);
                            throw new Exception($message);
                        }

                        // Optional: validate column order
                        $mismatch = false;
                        foreach ($expectedHeaders as $i => $header) {
                            if (!isset($values[$i]) || $values[$i] !== $header) {
                                $mismatch = true;
                                Log::error("Header mismatch at column $i. Expected '$header', found '" . ($values[$i] ?? 'NULL') . "'");
                            }
                        }

                        if ($mismatch) {
                            Log::error('Header columns do not match expected order.');
                            break; // Stop processing
                        }

                        continue;
                    }

                    // Map data to keys
                    $data = [];
                    foreach ($expectedHeaders as $index => $field) {

                        if(in_array($field, ['start_date', 'expiry_date'])){
                            $dateValue = $values[$index] ?? null;
                            $data[$field] = $this->formattingDate($dateValue);
                        }else{
                            $data[$field] = $values[$index] ?? null;
                        }

                    }

                    $data['cbr_ref_no'] = 'FEL' . (trim($values[0]) ?? '') . (trim($values[3]) ?? '') . (trim($values[2]) ?? '');
                    $data['quarter_no'] = '2021-Q4';
                    $data['month_no'] = '2021-12';
                    $data['created_at'] = now();
                    $data['updated_at'] = now();

                    // Example: log or save
                    // Log::info('Mapped Row:', $data);

                    $dataBatch[] = $data;

                    if (count($dataBatch) >= $batchSize) {
                        //CbrSbs3BillData::insert($dataBatch);
                        JobCbrSbs3BillFile::dispatch($dataBatch);
                        //Log::info("Inserted batch of 500 rows at row $rowNumber");
                        $dataBatch = []; // reset
                    }
                } // Row Iterator

                // Insert remaining rows
                if (!empty($dataBatch)) {
                    CbrSbs3BillData::insert($dataBatch);
                    //Log::info("Inserted remaining " . count($dataBatch) . " rows");
                }

                break; // only first sheet
            }
        }// Sheet Iterator

        $reader->close();
    }

    private function formattingDate($dt)
    {
        if (strlen($dt) == 7) {
            $date_y1 = substr($dt, 0, 1);
            $date_y2 = substr($dt, 1, 2);
            $date_m = substr($dt, 3, 2);
            $date_d = substr($dt, -2);
            $year = $date_y1;
            if ($year == 1) {
                $year = '20';
            } else {
                $year = '19';
            }
            $dt = $date_d . '-' . $date_m . '-' . $year . $date_y2;
            $dt = date('Y-m-d', strtotime($dt));
        } else if (strlen($dt) == 6) {
            $date_d = substr($dt, -2);
            $date_m = substr($dt, 2, 2);
            $date_y = substr($dt, 0, 2);
            $dt = $date_d . '-' . $date_m . '-' . '19' . $date_y;
            $dt = date('Y-m-d', strtotime($dt));
        }
        return $dt;
    } // end :: formattingDate()
}

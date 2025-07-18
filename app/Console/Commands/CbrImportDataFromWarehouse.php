<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CbrImportDataFromWarehouse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cbr-data-from-warehouse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting import...');

        // Mapping array
        $dbMapping = [
            'branch_code' => 'branch_code1',
            'branch_mnemonic' => 'branch_mnemonic',
            'deal_reference' => 'deal_reference',
            'product_group' => 'loan_type_code',
            'customer_mnemonic' => 'account_basic_number',
            'interest_accrued' => 'accrued_interest',
            'charge_amount' => 'interest_charge_during_the_quarter',
            'outstanding_amount' => 'present_quarter_month_outstanding',
            'outstanding_amount_bdt' => 'equivalent_outstanding',
            'overdue_amount' => 'overdue',
            'amount_disbursed' => 'disbursement_during_the_quarter',
            'total_paid' => 'recovery_during_the_quarter',
            'interest_rate' => 'rate_of_interest',
            'start_date' => 'date_of_original_first_disbursement',
            'customer_name' => 'name_of_the_borrower',
            'expiry_date' => 'date_of_maturity_or_expiry',
            'bill_code' => 'bill_code',
        ];

        
DB::table('cbr_sbs3_bill_data')->where('quarter_no', '=', '2022-Q1')->delete();
        // Fetch all records from temp table
        $rows = DB::connection('mysql')->table('quarterly_bill_data_temp')->get();

        $insertData = [];

        foreach ($rows as $row) {
            $record = [];

            foreach ($dbMapping as $destinationField => $sourceField) {
                $record[$destinationField] = $row->{$sourceField} ?? null;
            }

            $record['cbr_ref_no'] = 'FEL' . (trim($row->branch_code1) ?? '') . (trim($row->loan_type_code) ?? '') . (trim($row->deal_reference) ?? '');
            $record['quarter_no'] = '2022-Q1';
            $record['month_no'] = '2022-03';
            $record['created_at'] = now();
            $record['updated_at'] = now();

            $insertData[] = $record;
        }

        // Optional: chunk inserts for performance
        $chunks = array_chunk($insertData, 1000);

        foreach ($chunks as $chunk) {
            DB::table('cbr_sbs3_bill_data')->insert($chunk);
        }

        $this->info('Import completed successfully.');

        return 0;
    }
}

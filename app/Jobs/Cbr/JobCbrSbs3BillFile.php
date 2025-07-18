<?php

namespace App\Jobs\Cbr;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Modules\Cbr\Models\CbrSbs3BillData;
//use Illuminate\Support\Facades\DB;

class JobCbrSbs3BillFile implements ShouldQueue
{
     use Dispatchable, InteractsWithQueue, Queueable;

    protected $chunk;

    /**
     * Create a new job instance.
     */
    public function __construct($chunk)
    {
        $this->chunk = $chunk;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        CbrSbs3BillData::insert($this->chunk);
        //DB::table('cbr_sbs3_bill_data')->insert($this->chunk);
    }
}

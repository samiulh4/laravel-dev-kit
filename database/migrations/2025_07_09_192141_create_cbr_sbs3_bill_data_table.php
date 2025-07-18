<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cbr_sbs3_bill_data', function (Blueprint $table) {
            $table->id();

            $table->string('cbr_ref_no', 255)->nullable();

            $table->string('branch_code', 4)->nullable();
            $table->string('branch_mnemonic', 4)->nullable();
            $table->string('deal_reference', 30)->nullable();
            $table->string('product_group', 5)->nullable();
            $table->string('customer_mnemonic', 6)->nullable();
            $table->string('interest_accrued', 255)->nullable();
            $table->string('charge_amount', 255)->nullable();
            $table->string('outstanding_amount', 255)->nullable();
            $table->string('outstanding_amount_bdt', 255)->nullable();
            $table->string('overdue_amount', 255)->nullable();
            $table->string('amount_disbursed', 255)->nullable();
            $table->string('total_paid', 255)->nullable();
            $table->string('interest_rate', 255)->nullable();
            $table->string('start_date', 255)->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->string('expiry_date', 10)->nullable();
            $table->string('bill_code', 4)->nullable();

            $table->string('month_no', 7)->nullable();
            $table->string('quarter_no', 7)->nullable();

            $table->tinyInteger('is_active')->default(1);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();

            // 🔥 Add your unique constraint here:
            $table->unique(['cbr_ref_no', 'quarter_no'], 'CBR_REF_NO_AND_QUARTER');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbr_sbs3_bill_data');
    }
};

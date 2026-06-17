<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('subscriber_id');
            $table->foreign('subscriber_id')->references('id')->on('subscribers');



            $table->foreignId('subscription_type_id');
            $table->foreign('subscription_type_id')->references('id')->on('subscription_types');
         
            $table->foreignId('session_report_id');
            $table->foreign('session_report_id')->references('id')->on('session_reports');
            
            $table->date('start_date');

            $table->date('end_date');

            $table->string('created_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
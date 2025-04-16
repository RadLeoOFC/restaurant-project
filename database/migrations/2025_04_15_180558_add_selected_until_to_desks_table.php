<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_add_selected_until_to_desks_table.php

    public function up(): void
    {
        Schema::table('desks', function (Blueprint $table) {
            $table->timestamp('selected_until')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('desks', function (Blueprint $table) {
            $table->dropColumn('selected_until');
        });
    }

};

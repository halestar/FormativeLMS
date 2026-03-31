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
	    $tableNames = config('permission.table_names');
	    $columnNames = config('permission.column_names');
	    $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        Schema::create('default_roles', function (Blueprint $table) use ($tableNames, $pivotRole, $columnNames)
        {
	        $table->unsignedBigInteger($pivotRole);

	        $table->string('model_type');
	        $table->unsignedBigInteger($columnNames['model_morph_key']);
	        $table->index([$columnNames['model_morph_key'], 'model_type'],
		        'model_has_roles_model_id_model_type_index');

	        $table->foreign($pivotRole)
		        ->references('id') // role id
		        ->on($tableNames['roles'])
		        ->onDelete('cascade');
	        $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
		        'model_has_roles_role_model_type_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_roles');
    }
};

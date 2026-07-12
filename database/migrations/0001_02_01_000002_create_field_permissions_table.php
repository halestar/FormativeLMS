<?php

use App\Enums\FieldPermissionAction;
use App\Enums\FieldPermissionContext;
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
		Schema::create('field_permissions', function (Blueprint $table)
		{
			$table->id();
			$table->foreignId('viewer_role_id')->nullable()->constrained('roles')->cascadeOnDelete();
			$table->foreignId('target_role_id')->nullable()->constrained('roles')->cascadeOnDelete();
			$table->enum('context', FieldPermissionContext::values());
			$table->enum('action', FieldPermissionAction::values());
			$table->string('field');
			$table->boolean('allow');
			$table->unique([
				'viewer_role_id', 'target_role_id', 'context', 'action', 'field',
			], 'unique_field_permissions');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('field_permissions');
	}
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministrativeDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('administrative_documents', function (Blueprint $table) {
            $table->id();
            // Document kind: invoice | credit_note | delivery_order | terms | exit_order
            $table->string('type', 20);
            // Numeric correlative per type, formatted with prefix at render:
            //   1 → IN-0001 (invoice), NC-0001 (credit_note), OE-0001 (delivery_order),
            //   TC-0001 (terms), OS-0001 (exit_order).
            $table->unsignedInteger('number');
            // Emitting company (ve | us). Same values used by quotations.
            $table->string('company', 4)->default('ve');
            // Optional link to the parent Invoice (credit_note only).
            $table->foreignId('parent_document_id')->nullable()->constrained('administrative_documents')->nullOnDelete();
            // Full payload as JSON — client data + items + free text fields.
            // Regenerated to PDF on demand, no separate file is stored.
            $table->json('data');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['type', 'number']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('administrative_documents');
    }
}

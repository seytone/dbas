<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddClientSnapshotToQuotations extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Snapshot of the client's data at the time the quotation was
            // saved. Historically the PDF and edit form read directly from
            // the related Client, so editing any quotation mutated the
            // shared record and rewrote every past quotation for that client.
            $table->string('client_title', 100)->nullable()->after('client_id');
            $table->string('client_document', 20)->nullable()->after('client_title');
            $table->string('client_email', 100)->nullable()->after('client_document');
            $table->string('client_phone', 30)->nullable()->after('client_email');
            $table->string('client_address', 500)->nullable()->after('client_phone');
        });

        // Backfill existing quotations with the current state of their linked
        // client. Visually a no-op (that is what everything already shows),
        // but it locks each quotation to a stable snapshot going forward.
        DB::statement('
            UPDATE quotations q
            INNER JOIN clients c ON c.id = q.client_id
            SET q.client_title = c.title,
                q.client_document = c.document,
                q.client_email = c.email,
                q.client_phone = c.phone,
                q.client_address = c.address
        ');
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'client_title',
                'client_document',
                'client_email',
                'client_phone',
                'client_address',
            ]);
        });
    }
}

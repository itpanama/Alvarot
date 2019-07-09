<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTransporters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        $document_types = [
            'Aviso de Operaciones',
            'Póliza de Seguro',
            'Endoso Naviero',
            'Registro Vehicular',
        ];

        foreach ($document_types as $documentTypeName) {
            DB::table('document_type')->insert([
                [ 'description' => $documentTypeName ]
            ]);
        }

        Schema::create('transporters_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->timestamps();
        });

        $statusList = [
            'Pendiente',
            'Aprobado',
            'Rechazado'
        ];

        foreach ($statusList as $status) {
            DB::table('transporters_status')->insert([
                [ 'description' => $status ]
            ]);
        }

        Schema::create('transporters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name_operation')->nullable();
            $table->text('address_company')->nullable();
            $table->string('number_policy')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('email')->unique();
            $table->string('email_2')->nullable();

            $table->string('phone')->nullable();
            $table->string('phone_2')->nullable();

            $table->string('contact_name')->nullable();

            $table->integer('transporter_status_id')->unsigned()->default(1);

            $table->dateTime('transporter_status_date')->nullable();
            $table->integer('transporter_status_user_id')->unsigned()->nullable();
            $table->text('comments')->nullable();

            $table->integer('user_id')->unsigned()->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('transporter_status_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('transporter_status_id')
                ->references('id')
                ->on('transporters_status')
                ->onDelete('cascade');

            $table->index(['user_id']);
            $table->index(['transporter_status_id']);

            $table->timestamps();
        });

        Schema::create('transporters_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachment_name');
            $table->integer('attachment_size');
            $table->integer('transporter_id')->unsigned();
            $table->integer('document_type_id')->unsigned();

            $table->foreign('transporter_id')->references('id')->on('transporters')->onDelete('cascade');
            $table->foreign('document_type_id')->references('id')->on('document_type')->onDelete('cascade');
            $table->index(['transporter_id']);
            $table->index(['document_type_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporters', function (Blueprint $table) {
            $table->dropForeign('transporters_user_id_foreign');
            $table->dropForeign('transporters_transporter_status_id_foreign');
            $table->dropForeign('transporters_transporter_status_user_id_foreign');
        });

        Schema::table('transporters_documents', function (Blueprint $table) {
            $table->dropForeign('transporters_documents_document_type_id_foreign');
            $table->dropForeign('transporters_documents_transporter_id_foreign');
        });

        Schema::dropIfExists('transporters_documents');
        Schema::dropIfExists('document_type');
        Schema::dropIfExists('transporters');
        Schema::dropIfExists('transporters_status');
    }
}

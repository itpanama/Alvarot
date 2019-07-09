<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTransportersToTruckers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trucker_document_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        $trucker_document_types = [
            'Aviso de Operaciones',
            'Póliza de Seguro',
            'Endoso Naviero',
            'Registro Vehicular',
        ];

        foreach ($trucker_document_types as $trucker_status) {
            $truckerDocumentType = new App\TruckerDocumentType();
            $truckerDocumentType->description = $trucker_status;
            $truckerDocumentType->save();
        }

        Schema::create('trucker_status', function (Blueprint $table) {
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
            $documentType = new App\TruckerStatus;
            $documentType->description = $status;
            $documentType->save();
        }

        Schema::create('truckers', function (Blueprint $table) {
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

            $table->integer('trucker_status_id')->unsigned()->default(1);

            $table->dateTime('trucker_status_date')->nullable();
            $table->integer('trucker_status_user_id')->unsigned()->nullable();
            $table->text('comments')->nullable();

            $table->integer('user_id')->unsigned()->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('trucker_status_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('trucker_status_id')
                ->references('id')
                ->on('trucker_status')
                ->onDelete('cascade');

            $table->index(['user_id']);
            $table->index(['trucker_status_id']);

            $table->timestamps();
        });

        Schema::create('trucker_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachment_name');
            $table->integer('attachment_size');
            $table->integer('trucker_id')->unsigned();
            $table->integer('trucker_document_type_id')->unsigned();

            $table->foreign('trucker_id')->references('id')->on('truckers')->onDelete('cascade');
            $table->foreign('trucker_document_type_id')->references('id')->on('trucker_document_type')->onDelete('cascade');
            $table->index(['trucker_id']);
            $table->index(['trucker_document_type_id']);

            $table->timestamps();
        });


        Schema::table('trucker_documents', function (Blueprint $table) {
            DB::statement(<<<EOF
insert into truckers 
SELECT * FROM `transporters`;

EOF
            );

            DB::statement(<<<EOF
insert into `trucker_documents` 
select * from transporters_documents

EOF
            );
        });

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('truckers', function (Blueprint $table) {
            $table->dropForeign('truckers_trucker_status_id_foreign');
            $table->dropForeign('truckers_trucker_status_user_id_foreign');
            $table->dropForeign('truckers_user_id_foreign');
        });

        Schema::table('trucker_documents', function (Blueprint $table) {
            $table->dropForeign('trucker_documents_trucker_document_type_id_foreign');
            $table->dropForeign('trucker_documents_trucker_id_foreign');
        });

        Schema::dropIfExists('trucker_documents');
        Schema::dropIfExists('trucker_document_type');
        Schema::dropIfExists('truckers');
        Schema::dropIfExists('trucker_status');

        Schema::create('document_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        Schema::create('transporters_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->timestamps();
        });

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
}

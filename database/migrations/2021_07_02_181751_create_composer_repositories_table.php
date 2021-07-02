<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Latus\Plugins\Models\ComposerRepository;

class CreateComposerRepositoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('composer_repositories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->tinyInteger('status')->default(ComposerRepository::STATUS_ACTIVATED);
            $table->string('name');
            $table->string('type')->default('vcs');
            $table->string('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('composer_repositories');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// This migration was added during conversion but the Laravel scaffold already
// provides a users migration. Keep this file as a no-op so migrations remain
// ordered but won't re-create the users table and cause conflicts.

return new class extends Migration {
    public function up()
    {
        // intentionally left blank to avoid duplicating the scaffold users table
    }

    public function down()
    {
        // intentionally left blank
    }
};

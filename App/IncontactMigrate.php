<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncontactMigrate extends Model
{
    protected $guarded =[];
    protected $connection = "mysql2";
    protected $table = "incontacts";
}

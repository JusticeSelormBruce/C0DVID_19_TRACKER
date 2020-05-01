<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    protected $guarded=[];

    public function data()
    {
        return $this->belongsTo(Data::class);
    }
}

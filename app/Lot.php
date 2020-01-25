<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }
}

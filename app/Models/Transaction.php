<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Member;
use App\Models\Package;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    //

    use SoftDeletes;

    protected $guarded = ['id'];

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function package(){
       return $this->hasOne(Package::class, 'id', 'package_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Package;

class Member extends Model
{
    use SoftDeletes;
    //
    protected $guarded = ['id'];
}

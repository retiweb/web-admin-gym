<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    //
    protected $guarded = ['id'];

    public function member(){
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }
}

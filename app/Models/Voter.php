<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;



class Voter extends Model
{
   protected $fillable = ['name','email','phone','token','has_voted','voted_at','link_used_at'];
    protected $casts = ['has_voted'=>'boolean','voted_at'=>'datetime','link_used_at'=>'datetime'];

 // 👇 Add this method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($voter) {
            if (empty($voter->token)) {
                $voter->token = Str::random(40);
            }
        });
    }
}
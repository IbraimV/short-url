<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Url extends Model
{
    //
    protected $guarded = [];
    
    public function generateCode(){
        $code = Str::random(10);
        if(self::where('shortcode',$code)->count() > 0) {
            self::generateCode();
        } else {
            return $code;
        }
    }
}

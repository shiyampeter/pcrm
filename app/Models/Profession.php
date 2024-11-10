<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $appends = ['profession'];
    public function getProfessionAttribute()
    {
        $product = Profession::where('id', $this->id)->first();
        if ($product) {
            return $product->name;
        }
        return null;
    }
}

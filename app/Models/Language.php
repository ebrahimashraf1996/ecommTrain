<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';

    protected $fillable =['abbr', 'locale', 'name', 'direction', 'active', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function scopeActive($query) {
        return $query->where('active', 1);
    }

    public function scopeSelection($query) {
        return $query->select('id', 'abbr', 'name', 'direction', 'active');
    }
    public function scopeAdd($query, $request) {
        $request->active = isset($request->active) ? 1 : 0;
        return $query->create([
            'name' =>  $request->name,
            'abbr' => $request->abbr,
            'direction' => $request->direction,
            'active' => $request->active
        ]);
    }

    public function getActive() {
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';
    }

}

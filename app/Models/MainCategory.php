<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    protected $table = 'main_categories';

    protected $fillable =['translation_lang', 'translation_of', 'name', 'slug', 'photo', 'active', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function scopeActive($q) {
        return $q->where('active', 1);
    }

    public function scopeSelection($q) {
        return $q->select('id', 'translation_lang', 'name', 'slug', 'photo', 'active', 'translation_of');
    }

    public function getPhotoAttribute($val) {
        return $val != null ? asset('assets/' . $val) : "";
    }

    public function getActive() {
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';
    }

    public function translations() {
        return $this->hasMany(self::class, 'translation_of');
    }




}

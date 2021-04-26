<?php

use Illuminate\Support\Facades\Config;

function getAdminName() {
    $adminId = auth()->guard('admin')->id();
    $adminName = \App\Models\Admin::where('id', $adminId)->select(['name'])->get();
    $adminName = $adminName[0]['name'];
    return $adminName;
}

function get_languages() {
    return \App\Models\Language::active()->selection()->get();
}

function get_default_lang() {
    return Config::get('app.locale');
}

function uploadImage($folder, $image){
    $image->store('/', $folder);
    $filename = $image->hashName();
    $path = 'images/' . $folder . '/' . $filename;
    return $path;
}




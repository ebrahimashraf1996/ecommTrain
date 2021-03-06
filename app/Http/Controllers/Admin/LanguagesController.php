<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    public function index() {
        $languages = Language::select()->paginate(PAGINATION_COUNT);
        return view('admin.languages.index', compact('languages'));
    }


    public function create() {
        return view('admin.languages.create');
    }


    public function store(LanguageRequest $request) {
        try {
            Language::add($request);
            return redirect()->route('admin.languages')->with(['success' => 'تم حفظ اللغة بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => 'هناك خطأ ما يرجي المحاولة فيما بعد']);
        }
    }

    public function edit($id) {
        $language = Language::select()->find($id);
        if (!$language)
            return redirect()->route('admin.languages')->with(['error' => 'هذه اللغة غير موجودة']);
        return view('admin.languages.edit', compact('language'));
    }

    public function update($id, LanguageRequest $request) {
        try {
            $language = Language::find($id);
            if (!$language) {
                return redirect()->route('admin.languages.edit', $id)->with(['error' => 'هذه اللغة غير موجودة']);
            }
            // Update
            $request->active = isset($request->active) ? 1 : 0;
            $language->update([
                'name' =>  $request->name,
                'abbr' => $request->abbr,
                'direction' => $request->direction,
                'active' => $request->active
            ]);
            return redirect()->route('admin.languages')->with(['success' => 'تم تحديث اللغة بنجاح']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => 'هناك خطأ ما يرجي المحاولة فيما بعد']);
        }
    }

    public function destroy($id) {
        try {
            $language = Language::find($id);
            if (!$language) {
                return redirect()->route('admin.languages', $id)->with(['error' => 'هذه اللغة غير موجودة']);
            }
            // Update

            $language->delete();
            return redirect()->route('admin.languages')->with(['success' => 'تم حذف اللغة بنجاح']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => 'هناك خطأ ما يرجي المحاولة فيما بعد']);
        }
    }



}

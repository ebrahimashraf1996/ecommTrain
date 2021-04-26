<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class MainCategoriesController extends Controller
{
    ####################### start index Function ######################

    public function index()
    {
        $default_lang = get_default_lang();
        $categories = MainCategory::where('translation_lang', $default_lang)->selection()->get();
        return view('admin.maincategories.index', compact('categories'));
    }
    ####################### end index Function ######################


    ####################### start Create Function ######################
    public function create()
    {

        return view('admin.maincategories.create');
    }
    ####################### end Create Function ######################

    /*
        ############ How To Handel Ur Code When u have lots of more than one insert in the data base #################
        try {
            DB::beginTransaction();
               // Code Here
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
        }
     */

    ####################### start store Function ######################
    public function store(MainCategoryRequest $request)
    {

        try {

            $main_categories = collect($request->category);

            $filter = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] == get_default_lang();
            });

            $default_category = array_values($filter->all())[0];

            $filePath = "";
            if ($request->has('photo')) {
                $filePath = uploadImage('maincategories', $request->photo);
            }

            DB::beginTransaction();

            $default_category_id = MainCategory::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'photo' => $filePath
            ]);

            $categories = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] != get_default_lang();
            });

            if (isset($categories) && $categories->count() > 0) {
                $categories_arr = [];
                foreach ($categories as $category) {
                    $categories_arr[] = [
                        'translation_lang' => $category['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $category['name'],
                        'slug' => $category['name'],
                        'photo' => $filePath
                    ];
                }

                MainCategory::insert($categories_arr);
            }
            DB::commit();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم إضافة القسم بنجاح']);

        } catch (\Exception $exception) {
            DB::rollback();
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطأ ما برجاء المحاولة لاحقا']);

        }
    }
    ####################### end store Function ######################


    ####################### start edit Function ######################
    public function edit($mainCat_id)
    {
        try {
            // Get a specific Category with its Translations
            $mainCategory = MainCategory::with('translations')
                ->selection()
                ->find($mainCat_id);

            if (!$mainCategory)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);
            return view('admin.maincategories.edit', compact('mainCategory'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطأ ما برجاء المحاولة لاحقا']);
        }
    }
    ####################### end edit Function ######################

    ####################### start Update Function ######################
    public function update(MainCategoryRequest $request, $mainCat_id)
    {
        try {
            $main_Category = MainCategory::selection()->find($mainCat_id);
            if (!$main_Category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);

            $category = array_values($request->category)[0];


            if (isset($category['photo'])) {
                $filePath = uploadImage('maincategories', $category['photo']);
                MainCategory::where('id', $mainCat_id)
                    ->update([
                        'photo' => $filePath
                    ]);
            }
            if (!$request->has('category.0.active'))
            {
                $request->request->add(['active' => 0]);
            } else {
                $request->request->add(['active' => 1]);
            }

            MainCategory::where('id', $mainCat_id)
                ->update([
                    'name' => $category['name'],
                    'active' => $request->active
                ]);
            return redirect()->route('admin.maincategories')->with(['success' => 'تم التحديث بنجاح']);
        } catch (\Exception $exception) {
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطأ ما برجاء المحاولة لاحقا']);
        }
    }

    ####################### end Update Function ######################


}

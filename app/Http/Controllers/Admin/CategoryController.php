<?php

namespace Acelle\Http\Controllers\Admin;

use Acelle\Http\Controllers\Controller;
use Acelle\Model\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create()
    {
        return view('admin.category', [
            'category' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        Category::create([
            'name' => $request->name
        ]);
        $request->session()->flash('alert-success', trans('messages.catgory.created'));
        return back();
    }

    public function update(Request $request, Category $category)
    {
        $category->update([
            'name' => $request->name
        ]);
        $request->session()->flash('alert-success', trans('messages.category.updated'));
        return back();
    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();
        $request->session()->flash('alert-success', trans('messages.category.deleted'));
        return back();
    }
}

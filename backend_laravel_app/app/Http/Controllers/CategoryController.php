<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Authentication removed for local development — default to demo user id 1
        $userId = $request->user->id ?? 1;
        $rows = Category::where('user_id', $userId)->orderBy('name', 'asc')->get();
        return response()->json($rows);
    }

    public function store(Request $request)
    {
        $userId = $request->user->id ?? 1;
        $name = $request->input('name') ?? 'Uncategorized';
        $icon = $request->input('icon');
        $color = $request->input('color');

        // Create category without strict validation in dev
        $cat = Category::create([
            'user_id' => $userId,
            'name' => trim($name),
            'icon' => $icon,
            'color' => $color,
        ]);

        return response()->json(['id' => $cat->id, 'user_id' => $userId, 'name' => $cat->name, 'icon' => $cat->icon, 'color' => $cat->color]);
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user->id ?? 1;
        $cat = Category::where('id', $id)->where('user_id', $userId)->first();
        if (!$cat) return response()->json(['message' => 'Category not found'], 404);
        return response()->json($cat);
    }

    public function update(Request $request, $id)
    {
        $userId = $request->user->id ?? 1;
        $name = $request->input('name');
        $icon = $request->input('icon');
        $color = $request->input('color');

        if (!$name || trim($name) === '') {
            return response()->json(['message' => 'Name required'], 400);
        }

        $cat = Category::where('id', $id)->where('user_id', $userId)->first();
        if (!$cat) return response()->json(['message' => 'Category not found'], 404);

        $cat->name = trim($name);
        $cat->icon = $icon;
        $cat->color = $color;
        $cat->save();

        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $userId = $request->user->id ?? 1;
        $cat = Category::where('id', $id)->where('user_id', $userId)->first();
        if (!$cat) return response()->json(['message' => 'Category not found'], 404);
        $cat->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}

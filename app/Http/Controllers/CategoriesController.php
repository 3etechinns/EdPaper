<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \Input;
use App\User;
use App\Categorie;

class CategoriesController extends Controller
{
	public function listall(){
		if (!Auth::check()) return redirect('/login');
		$cats = Categorie::where('userId', Auth::user()->id)->orderBy('title', 'ASC')->get();
		return view('cat.listall', ['cats' => $cats]);
	}

	public function list($id){
		if (!Auth::check()) return redirect('/login');
		$cat = Categorie::where('id', $id)->firstOrFail();
		if ($cat->userId != Auth::user()->id) return redirect('/login');
		$docs = $cat->docs()->orderBy('title', 'ASC')->get();;
		return view('cat.list', ['docs' => $docs, 'cat' => $cat]);
	}

	public function add(Request $request){
		$this->validate($request, [
			'title' => 'required|max:20',
			]);
		if (!Auth::check()) return redirect('/login');
		$checkExists = Categorie::where([['userId', '=', Auth::user()->id], ['title', '=', Input::get('title')]])->first();
		if (!is_null($checkExists)) return redirect('/cat');

		Categorie::create([
			'userId' => Auth::user()->id,
			'title' => Input::get('title'),
			]);
		return redirect('/cat');
	}

	public function edit($id){
		if (!Auth::check()) return redirect('/login');
		$cat = Categorie::where('id', $id)->firstOrFail();
		if ($cat->userId != Auth::user()->id) return redirect('/login');
		return view('cat.edit', ['cat' => $cat]);
	}

	public function sendedit($id, Request $request){
		if (!Auth::check()) return redirect('/login');
		$this->validate($request, [
			'title' => 'max:20|required',
			]);
		$cat = Categorie::where('id', $id)->firstOrFail();
		if ($cat->userId != Auth::user()->id) return redirect('/login');
		$cat->title = Input::get('title');
		$cat->save();
		return redirect('/cat');
	}

	public function delete($id){
		if (!Auth::check()) return redirect('/login');
		$cat = Categorie::where('id', $id)->firstOrFail();
		if ($cat->userId != Auth::user()->id) return redirect('/login');
		$cat->forceDelete();
		return redirect('/cat');
	}
}

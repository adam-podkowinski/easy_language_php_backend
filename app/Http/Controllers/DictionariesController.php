<?php

namespace App\Http\Controllers;

use App\Http\Resources\DictionaryResource;
use App\Models\Dictionary;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DictionariesController extends Controller
{
    public function all()
    {
        if (Auth::user()->is_admin) {
            return Dictionary::all();
        } else {
            return auth()->user()->dictionaries;
        }
    }

    public function allWords()
    {
        $returnArray = [];

        foreach (auth()->user()->dictionaries as $dict) {
            array_push($returnArray, new DictionaryResource($dict));
        }

        return $returnArray;
    }

    public function index()
    {
        return auth()->user()->dictionaries;
    }

    public function show($lang)
    {
        $dict = $this->findDictFromIdOrISO($lang);

        if (empty($dict)) {
            return response('not found dictionary', 404);
        }

        return $dict;
    }

    public function showWords($lang)
    {
        $dict = $this->findDictFromIdOrISO($lang);

        if (empty($dict)) {
            return response('not found dictionary', 404);
        }

        return new DictionaryResource($dict);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (empty($user)) {
            return response(['error' => 'forbidden'], 403);
        }

        $request->validate([
                'language' => [
                    'required',
                    'string',
                    Rule::unique('dictionaries')->where('user_id', auth()->user()->id)
                ],
            ]
        );

        return Dictionary::create([
            'language' => $request['language'],
            'user_id' => $user->id,
        ]);
    }

    public function destroy($lang)
    {
        $dict = $this->findDictFromIdOrISO($lang);

        if (!Gate::allows('access-dictionary', $dict)) {
            return response(['error' => 'forbidden'], 403);
        }

        return $dict->delete();
    }

    private function findDictFromIdOrISO($lang)
    {
        $dict = null;
        if (gettype($lang) == 'string') {
            $dict = auth()->user()->dictionaries->where('language', $lang)->first();
        } else {
            $dict = Dictionary::find($lang);
        }

        return $dict;
    }
}

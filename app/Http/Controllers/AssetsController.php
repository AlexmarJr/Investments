<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assets;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;

class AssetsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $assets = Assets::where('user_id', $user->id)->where('type', 'asset')->get();
        $liabilities = Assets::where('user_id', $user->id)->where('type', 'liability')->get();

        return response()->json([
            'assets' => $assets,
            'liabilities' => $liabilities
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|numeric',
            'type' => 'required|string|max:255',
        ]);

        $asset = Assets::create([
            'name' => $request->name,
            'value' => $request->value,
            'type' => $request->type,
            'user_id' => Auth::id(),
        ]);

        return Redirect::route('patrimony')->with('success', 'Patrimônio adicionado com sucesso!');    
    }

    public function destroy($id): RedirectResponse
    {
        $asset = Assets::findOrFail($id);
        if ($asset->user_id !== Auth::id()) {
            return Redirect::route('patrimony')->with('error', 'Você não tem permissão para excluir este patrimônio.');
        }
        
        $asset->delete();
        
        return Redirect::route('patrimony')->with('success', 'Patrimônio excluído com sucesso!');
    }
}

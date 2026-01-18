<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CotisationController extends Controller
{
    /**
     * Display a listing of the resource (For Jeune).
     */
    public function index()
    {
        $user = Auth::user();

        // If user is collecteur, redirect to their dashboard
        if ($user->isCollecteur()) {
            return redirect()->route('collecteur.dashboard');
        }

        $cotisations = $user->cotisations()->orderBy('date_paiement', 'desc')->get();
        $totalPaye = $cotisations->where('statut', 'payé')->sum('montant');

        return view('dashboard', compact('cotisations', 'totalPaye'));
    }

    /**
     * Store a newly created resource in storage (For Collecteur).
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_compte' => 'required|exists:users,numero_compte',
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date',
        ]);

        $jeune = User::where('numero_compte', $request->numero_compte)->firstOrFail();

        Cotisation::create([
            'user_id' => $jeune->id,
            'montant' => $request->montant,
            'date_paiement' => $request->date_paiement,
            'collecteur_id' => Auth::id(),
            'statut' => 'payé',
        ]);

        return redirect()->back()->with('success', 'Cotisation enregistrée avec succès.');
    }

    /**
     * Dashboard for Collecteur.
     */
    public function collecteurDashboard()
    {
        $jeunes = User::where('role', 'jeune')->get();
        // Calculate total collected by this collector or global? 
        // Prompt says "Total collecté, Mon solde". 
        // "Total collecté" might be global or by this collector. I'll show by this collector for "Mon solde" context maybe?
        // Or "Total collecté" = Total collected by everyone, "Mon solde" = ? 
        // Let's simplified: Total collected by this user.

        $totalCollecte = Cotisation::where('collecteur_id', Auth::id())->sum('montant');

        $historique = Cotisation::where('collecteur_id', Auth::id())
            ->with('user')
            ->orderBy('date_enregistrement', 'desc')
            ->take(10)
            ->get();

        return view('collecteur.dashboard', compact('jeunes', 'totalCollecte', 'historique'));
    }

    /**
     * Show form to create a new Jeune.
     */
    public function createJeune()
    {
        return view('collecteur.create_jeune');
    }

    /**
     * Store a new Jeune.
     */
    public function storeJeune(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'numero_compte' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'numero_compte' => $request->numero_compte,
            'role' => 'jeune',
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return redirect()->route('collecteur.dashboard')->with('success', 'Compte Jeune créé avec succès.');
    }

    /**
     * Show form to edit a Jeune.
     */
    public function editJeune(User $user)
    {
        return view('collecteur.edit_jeune', compact('user'));
    }

    /**
     * Update a Jeune.
     */
    public function updateJeune(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'numero_compte' => ['required', 'string', 'max:255', 'unique:users,numero_compte,' . $user->id],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'numero_compte' => $request->numero_compte,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            ]);
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
        }

        return redirect()->route('collecteur.dashboard')->with('success', 'Compte Jeune mis à jour avec succès.');
    }

    /**
     * Delete a Jeune.
     */
    public function destroyJeune(User $user)
    {
        // Optional: Check if user has payments before deleting?
        // For now, let's allow deletion but payments will remain or cascade depending on DB setup.
        // Assuming cascade on delete is NOT set by default in Laravel migrations for polymorphic/foreign keys often unless specified.
        // But let's keep it simple: delete user.

        $user->cotisations()->delete(); // Delete associated cotisations first
        $user->delete();

        return redirect()->route('collecteur.dashboard')->with('success', 'Compte Jeune supprimé avec succès.');
    }
}

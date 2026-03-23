<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CotisationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // If user is collecteur, redirect to their dashboard
        if ($user->isCollecteur()) {
            return redirect()->route('collecteur.dashboard');
        }

        $cotisations = $user->cotisations()->orderBy('date_paiement', 'desc')->get();
        
        $totalPaye = $cotisations->where('type', 'versement')->sum('montant');
        $totalRetires = $cotisations->where('type', 'retrait')->sum('montant');
        $solde = $totalPaye - $totalRetires;

        return view('dashboard', compact('cotisations', 'totalPaye', 'totalRetires', 'solde'));
    }

    /**
     * Store a newly created resource in storage (For Collecteur).
     */
    public function store(Request $request, FirebaseService $firebase)
    {
        $request->validate([
            'numero_compte' => 'required|exists:users,numero_compte',
            'montant' => 'required|numeric|min:1',
            'type' => 'required|in:versement,retrait',
            'date_paiement' => 'required|date',
        ]);

        $jeune = User::where('numero_compte', $request->numero_compte)->firstOrFail();

        $cotisation = Cotisation::create([
            'user_id' => $jeune->id,
            'montant' => $request->montant,
            'type' => $request->type,
            'date_paiement' => $request->date_paiement,
            'collecteur_id' => Auth::id(),
            'statut' => 'payé',
        ]);

        // Optionnel : Envoyer vers Firebase pour le temps réel
        try {
            $firebase->notifyCotisation($cotisation);
        } catch (\Exception $e) {
            // Log error or ignore if firebase fails
            \Log::error("Erreur Firebase : " . $e->getMessage());
        }

        $msg = ($request->type == 'versement') ? 'Versement enregistré avec succès.' : 'Retrait enregistré avec succès.';
        return redirect()->back()->with('success', $msg);
    }

    public function collecteurDashboard()
    {
        $jeunes = User::where('role', 'jeune')->get();
        
        $stats = Cotisation::selectRaw("
            SUM(CASE WHEN type = 'versement' THEN montant ELSE 0 END) as total_versements,
            SUM(CASE WHEN type = 'retrait' THEN montant ELSE 0 END) as total_retraits
        ")->first();

        $totalCollecteBrut = $stats->total_versements ?? 0;
        $totalRetraits = $stats->total_retraits ?? 0;
        $soldeGlobal = $totalCollecteBrut - $totalRetraits;

        $historique = Cotisation::with('user')
            ->orderBy('date_enregistrement', 'desc')
            ->take(10)
            ->get();

        return view('collecteur.dashboard', compact('jeunes', 'totalCollecteBrut', 'totalRetraits', 'soldeGlobal', 'historique'));
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
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'numero_compte' => User::generateUniqueNumeroCompte(),
            'role' => 'jeune',
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return redirect()->route('collecteur.dashboard')->with('success', 'Compte Jeune créé avec succès.');
    }

    /**
     * Show details of a Jeune (Transactions and balance).
     */
    public function showJeune(User $user)
    {
        $cotisations = $user->cotisations()->orderBy('date_paiement', 'desc')->get();
        
        $totalPaye = $user->cotisations()->where('type', 'versement')->sum('montant');
        $totalRetires = $user->cotisations()->where('type', 'retrait')->sum('montant');
        $solde = $totalPaye - $totalRetires;

        return view('collecteur.show_jeune', compact('user', 'cotisations', 'totalPaye', 'totalRetires', 'solde'));
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

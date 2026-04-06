import React, { useState, useEffect } from 'react';
import { auth, UserProfile, Cotisation, Retrait, generateNumeroCompte } from '../firebase';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import { Plus, Users, Search, CreditCard, CheckCircle2, AlertCircle, Loader2, ArrowDownCircle, ArrowUpCircle, History, UserPlus, X } from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { api } from '../services/api';

export default function CollectorDashboard() {
  const [users, setUsers] = useState<UserProfile[]>([]);
  const [cotisations, setCotisations] = useState<Cotisation[]>([]);
  const [retraits, setRetraits] = useState<Retrait[]>([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [showModal, setShowModal] = useState<'cotisation' | 'retrait' | null>(null);
  const [selectedUser, setSelectedUser] = useState<UserProfile | null>(null);
  const [montant, setMontant] = useState('');
  const [type, setType] = useState('Mensuelle');
  const [motif, setMotif] = useState('');
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [activeTab, setActiveTab] = useState<'cotisations' | 'retraits'>('cotisations');
  const [showHistory, setShowHistory] = useState(false);
  const [showCreateUserModal, setShowCreateUserModal] = useState(false);
  const [newUser, setNewUser] = useState({ name: '', email: '', numero_compte: generateNumeroCompte() });
  const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' } | null>(null);

  const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
    setToast({ msg, type });
    setTimeout(() => setToast(null), 4000);
  };

  const fetchData = async () => {
    try {
      const [allUsers, allCots, allRets] = await Promise.all([
        api.getUsers(),
        api.getCotisations(),
        api.getRetraits()
      ]);
      setUsers(allUsers);
      setCotisations(allCots);
      setRetraits(allRets);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
    const interval = setInterval(fetchData, 30000);
    return () => clearInterval(interval);
  }, []);

  const filteredUsers = users.filter(u => 
    u.name.toLowerCase().includes(searchTerm.toLowerCase()) || 
    u.numero_compte.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleAddCotisation = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedUser || !montant) return;

    setSubmitting(true);
    try {
      await api.addCotisation({
        user_id: selectedUser.uid,
        collecteur_id: auth.currentUser?.uid || '',
        montant: parseFloat(montant),
        type,
        statut: 'valide',
      });
      setShowModal(null);
      resetForm();
      fetchData();
      showToast('Cotisation enregistrée avec succès !');
    } catch (error: any) {
      console.error(error);
      showToast(error?.message || 'Erreur lors de l\'enregistrement de la cotisation.', 'error');
    } finally {
      setSubmitting(false);
    }
  };

  const handleAddRetrait = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedUser || !montant) return;

    setSubmitting(true);
    try {
      await api.addRetrait({
        user_id: selectedUser.uid,
        admin_id: auth.currentUser?.uid || '',
        montant: parseFloat(montant),
        motif,
        statut: 'valide',
      });
      setShowModal(null);
      resetForm();
      fetchData();
      showToast('Retrait enregistré avec succès !');
    } catch (error: any) {
      console.error(error);
      showToast(error?.message || 'Erreur lors de l\'enregistrement du retrait.', 'error');
    } finally {
      setSubmitting(false);
    }
  };

  const resetForm = () => {
    setSelectedUser(null);
    setMontant('');
    setType('Mensuelle');
    setMotif('');
    setSearchTerm('');
  };

  const handleCreateUser = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      const profile: UserProfile = {
        uid: `manual_${Date.now()}`,
        name: newUser.name,
        email: newUser.email,
        role: 'user',
        numero_compte: newUser.numero_compte || generateNumeroCompte(),
      };
      await api.syncUser(profile);
      setShowCreateUserModal(false);
      setNewUser({ name: '', email: '', numero_compte: generateNumeroCompte() });
      fetchData();
      showToast('Membre créé avec succès !');
    } catch (error: any) {
      console.error(error);
      showToast(error?.message || 'Erreur lors de la création du membre.', 'error');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="space-y-8">
      {/* Toast */}
      <AnimatePresence>
        {toast && (
          <motion.div
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className={`fixed top-5 right-5 z-[100] flex items-center gap-3 px-5 py-4 rounded-2xl shadow-xl text-white font-medium ${
              toast.type === 'success' ? 'bg-emerald-600' : 'bg-red-600'
            }`}
          >
            {toast.type === 'success' ? <CheckCircle2 className="w-5 h-5" /> : <AlertCircle className="w-5 h-5" />}
            {toast.msg}
          </motion.div>
        )}
      </AnimatePresence>

      <header className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Espace Collecteur</h1>
          <p className="text-slate-500">Enregistrez les cotisations et les retraits des membres.</p>
        </div>
        <div className="flex gap-3 flex-wrap">
          <button
            onClick={() => setShowCreateUserModal(true)}
            className="flex items-center justify-center gap-2 bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 active:scale-95"
          >
            <UserPlus className="w-5 h-5" />
            <span>Nouveau Membre</span>
          </button>
          <button
            onClick={() => setShowModal('retrait')}
            className="flex items-center justify-center gap-2 bg-white border border-red-200 text-red-600 px-6 py-3 rounded-xl font-bold hover:bg-red-50 transition-all shadow-sm active:scale-95"
          >
            <ArrowDownCircle className="w-5 h-5" />
            <span>Nouveau Retrait</span>
          </button>
          <button
            onClick={() => setShowModal('cotisation')}
            className="flex items-center justify-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 active:scale-95"
          >
            <Plus className="w-5 h-5" />
            <span>Nouvelle Cotisation</span>
          </button>
        </div>
      </header>

      {/* Tabs */}
      <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="p-2 border-b border-slate-100 flex gap-2">
          <button
            onClick={() => setActiveTab('cotisations')}
            className={`flex-1 py-3 px-4 rounded-xl font-medium transition-all ${
              activeTab === 'cotisations' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'
            }`}
          >
            Collectes Récentes
          </button>
          <button
            onClick={() => setActiveTab('retraits')}
            className={`flex-1 py-3 px-4 rounded-xl font-medium transition-all ${
              activeTab === 'retraits' ? 'bg-red-50 text-red-600' : 'text-slate-500 hover:bg-slate-50'
            }`}
          >
            Retraits Récents
          </button>
        </div>
        
        <div className="overflow-x-auto">
          <table className="w-full text-left">
            <thead>
              <tr className="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <th className="px-6 py-4 font-semibold">Membre</th>
                <th className="px-6 py-4 font-semibold">Date</th>
                <th className="px-6 py-4 font-semibold">{activeTab === 'cotisations' ? 'Type' : 'Motif'}</th>
                <th className="px-6 py-4 font-semibold">Montant</th>
                <th className="px-6 py-4 font-semibold">Statut</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {activeTab === 'cotisations' ? (
                cotisations.length > 0 ? (
                  cotisations.map((c) => {
                    const user = users.find(u => u.uid === c.user_id);
                    return (
                      <tr key={c.id} className="hover:bg-slate-50 transition-colors">
                        <td className="px-6 py-4">
                          <div className="flex flex-col">
                            <span className="text-sm font-medium text-slate-900">{user?.name || 'Inconnu'}</span>
                            <span className="text-xs text-slate-500 font-mono">{user?.numero_compte}</span>
                          </div>
                        </td>
                        <td className="px-6 py-4 text-sm text-slate-600">
                          {format(c.date_paiement.toDate(), 'dd/MM/yyyy HH:mm')}
                        </td>
                        <td className="px-6 py-4 text-sm font-medium text-slate-900">{c.type}</td>
                        <td className="px-6 py-4 text-sm font-bold text-slate-900">
                          {c.montant.toLocaleString()} FCFA
                        </td>
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-2 text-emerald-600 text-sm">
                            <CheckCircle2 className="w-4 h-4" />
                            <span>Validé</span>
                          </div>
                        </td>
                      </tr>
                    );
                  })
                ) : (
                  <tr>
                    <td colSpan={5} className="px-6 py-12 text-center text-slate-400 italic">
                      {loading ? <Loader2 className="w-6 h-6 animate-spin mx-auto" /> : 'Aucune collecte enregistrée.'}
                    </td>
                  </tr>
                )
              ) : (
                retraits.length > 0 ? (
                  retraits.map((r) => {
                    const user = users.find(u => u.uid === r.user_id);
                    return (
                      <tr key={r.id} className="hover:bg-slate-50 transition-colors">
                        <td className="px-6 py-4">
                          <div className="flex flex-col">
                            <span className="text-sm font-medium text-slate-900">{user?.name || 'Inconnu'}</span>
                            <span className="text-xs text-slate-500 font-mono">{user?.numero_compte}</span>
                          </div>
                        </td>
                        <td className="px-6 py-4 text-sm text-slate-600">
                          {format(r.date_retrait.toDate(), 'dd/MM/yyyy HH:mm')}
                        </td>
                        <td className="px-6 py-4 text-sm font-medium text-slate-900">{r.motif}</td>
                        <td className="px-6 py-4 text-sm font-bold text-red-600">
                          -{r.montant.toLocaleString()} FCFA
                        </td>
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-2 text-emerald-600 text-sm">
                            <CheckCircle2 className="w-4 h-4" />
                            <span>Validé</span>
                          </div>
                        </td>
                      </tr>
                    );
                  })
                ) : (
                  <tr>
                    <td colSpan={5} className="px-6 py-12 text-center text-slate-400 italic">
                      {loading ? <Loader2 className="w-6 h-6 animate-spin mx-auto" /> : 'Aucun retrait enregistré.'}
                    </td>
                  </tr>
                )
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Modal */}
      <AnimatePresence>
        {showModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
            >
              <div className="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 className="text-xl font-bold text-slate-900">
                  {showModal === 'cotisation' ? 'Enregistrer une Cotisation' : 'Enregistrer un Retrait'}
                </h3>
                <button onClick={() => setShowModal(null)} className="text-slate-400 hover:text-slate-600">
                  <Plus className="w-6 h-6 rotate-45" />
                </button>
              </div>

              <form onSubmit={showModal === 'cotisation' ? handleAddCotisation : handleAddRetrait} className="p-6 space-y-6">
                {/* User Search */}
                <div className="space-y-2">
                  <div className="flex items-center justify-between">
                    <label className="text-sm font-medium text-slate-700">Sélectionner un Membre</label>
                    {selectedUser && (
                      <button
                        type="button"
                        onClick={() => setShowHistory(!showHistory)}
                        className="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1"
                      >
                        <History className="w-3 h-3" />
                        <span>{showHistory ? 'Masquer l\'historique' : 'Voir l\'historique'}</span>
                      </button>
                    )}
                  </div>
                  <div className="relative">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <input
                      type="text"
                      placeholder="Rechercher par nom ou numéro..."
                      className="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                    />
                  </div>
                  
                  {!showHistory && (
                    <div className="max-h-40 overflow-y-auto border border-slate-100 rounded-xl mt-2 divide-y divide-slate-50">
                      {filteredUsers.map(u => (
                        <button
                          key={u.uid}
                          type="button"
                          onClick={() => {
                            setSelectedUser(u);
                            setSearchTerm(u.name);
                          }}
                          className={`w-full flex items-center justify-between p-3 text-left hover:bg-blue-50 transition-colors ${selectedUser?.uid === u.uid ? 'bg-blue-50 border-l-4 border-blue-600' : ''}`}
                        >
                          <div>
                            <p className="text-sm font-medium text-slate-900">{u.name}</p>
                            <p className="text-xs text-slate-500 font-mono">{u.numero_compte}</p>
                          </div>
                          {selectedUser?.uid === u.uid && <CheckCircle2 className="w-4 h-4 text-blue-600" />}
                        </button>
                      ))}
                    </div>
                  )}

                  {showHistory && selectedUser && (
                    <div className="mt-2 border border-slate-100 rounded-xl overflow-hidden bg-slate-50">
                      <div className="p-3 bg-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider flex justify-between">
                        <span>Historique de {selectedUser.name}</span>
                        <span className="text-blue-600">
                          Solde: {
                            (cotisations.filter(c => c.user_id === selectedUser.uid).reduce((s, c) => s + c.montant, 0) - 
                             retraits.filter(r => r.user_id === selectedUser.uid).reduce((s, r) => s + r.montant, 0)).toLocaleString()
                          } FCFA
                        </span>
                      </div>
                      <div className="max-h-48 overflow-y-auto divide-y divide-slate-200">
                        {[
                          ...cotisations.filter(c => c.user_id === selectedUser.uid).map(c => ({ ...c, type_t: 'C' })),
                          ...retraits.filter(r => r.user_id === selectedUser.uid).map(r => ({ ...r, type_t: 'R' }))
                        ].sort((a, b) => {
                          const dA = 'date_paiement' in a ? a.date_paiement : a.date_retrait;
                          const dB = 'date_paiement' in b ? b.date_paiement : b.date_retrait;
                          return dB.toMillis() - dA.toMillis();
                        }).map((t, i) => (
                          <div key={i} className="p-3 flex justify-between items-center text-xs">
                            <div className="flex flex-col">
                              <span className="text-slate-500">{format(('date_paiement' in t ? t.date_paiement : t.date_retrait).toDate(), 'dd/MM/yy HH:mm')}</span>
                              <span className="font-medium text-slate-700">{t.type_t === 'C' ? (t as Cotisation).type : (t as Retrait).motif}</span>
                            </div>
                            <span className={`font-bold ${t.type_t === 'C' ? 'text-emerald-600' : 'text-red-600'}`}>
                              {t.type_t === 'C' ? '+' : '-'}{t.montant.toLocaleString()}
                            </span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-slate-700">Montant (FCFA)</label>
                    <input
                      type="number"
                      required
                      min="1"
                      className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                      value={montant}
                      onChange={(e) => setMontant(e.target.value)}
                    />
                  </div>
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-slate-700">
                      {showModal === 'cotisation' ? 'Type' : 'Motif'}
                    </label>
                    {showModal === 'cotisation' ? (
                      <select
                        className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                        value={type}
                        onChange={(e) => setType(e.target.value)}
                      >
                        <option value="Mensuelle">Mensuelle</option>
                        <option value="Inscription">Inscription</option>
                        <option value="Exceptionnelle">Exceptionnelle</option>
                        <option value="Amende">Amende</option>
                      </select>
                    ) : (
                      <input
                        type="text"
                        required
                        placeholder="Ex: Urgence médicale"
                        className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                        value={motif}
                        onChange={(e) => setMotif(e.target.value)}
                      />
                    )}
                  </div>
                </div>

                <div className="pt-4">
                  <button
                    type="submit"
                    disabled={!selectedUser || !montant || submitting}
                    className={`w-full text-white p-4 rounded-xl font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 ${
                      showModal === 'cotisation' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-red-600 hover:bg-red-700'
                    }`}
                  >
                    {submitting ? <Loader2 className="w-5 h-5 animate-spin" /> : 'Confirmer l\'opération'}
                  </button>
                </div>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      {/* Create User Modal */}
      <AnimatePresence>
        {showCreateUserModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
            >
              <div className="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 className="text-xl font-bold text-slate-900">Nouveau Membre</h3>
                <button onClick={() => setShowCreateUserModal(false)} className="text-slate-400 hover:text-slate-600">
                  <X className="w-6 h-6" />
                </button>
              </div>
              <form onSubmit={handleCreateUser} className="p-6 space-y-4">
                <div className="space-y-2">
                  <label className="text-sm font-medium text-slate-700">Nom Complet</label>
                  <input
                    type="text"
                    required
                    className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                    value={newUser.name}
                    onChange={(e) => setNewUser({ ...newUser, name: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-slate-700">Email (Optionnel)</label>
                  <input
                    type="email"
                    className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                    value={newUser.email}
                    onChange={(e) => setNewUser({ ...newUser, email: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-slate-700">Numéro de Compte</label>
                  <input
                    type="text"
                    placeholder="Laisser vide pour générer"
                    className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                    value={newUser.numero_compte}
                    onChange={(e) => setNewUser({ ...newUser, numero_compte: e.target.value })}
                  />
                </div>
                <button
                  type="submit"
                  disabled={submitting}
                  className="w-full bg-emerald-600 text-white p-4 rounded-xl font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 active:scale-95 flex items-center justify-center gap-2 mt-4"
                >
                  {submitting ? <Loader2 className="w-5 h-5 animate-spin" /> : 'Créer le Membre'}
                </button>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}

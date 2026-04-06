import React, { useState, useEffect } from 'react';
import { UserProfile, Cotisation, Retrait, generateNumeroCompte } from '../firebase';
import { Shield, UserCog, Trash2, Search, CheckCircle2, UserPlus, ShieldAlert, Loader2, Wallet, ArrowUpCircle, ArrowDownCircle, Users, X, History, User } from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { format } from 'date-fns';
import { api } from '../services/api';

export default function AdminDashboard() {
  const [users, setUsers] = useState<UserProfile[]>([]);
  const [cotisations, setCotisations] = useState<Cotisation[]>([]);
  const [retraits, setRetraits] = useState<Retrait[]>([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [loading, setLoading] = useState(true);
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [showTransactionsModal, setShowTransactionsModal] = useState<UserProfile | null>(null);
  const [newUser, setNewUser] = useState({ name: '', email: '', phone: '', numero_compte: '', role: 'user' as const });
  const [submitting, setSubmitting] = useState(false);

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

  const handleUpdateRole = async (uid: string, newRole: 'admin' | 'collecteur' | 'user') => {
    try {
      await api.updateUserRole(uid, newRole);
      fetchData();
    } catch (error) {
      console.error(error);
    }
  };

  const handleDeleteUser = async (uid: string) => {
    if (!window.confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) return;
    try {
      await api.deleteUser(uid);
      fetchData();
    } catch (error) {
      console.error(error);
    }
  };

  const handleCreateUser = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      const profile: UserProfile = {
        uid: `manual_${Date.now()}`,
        name: newUser.name,
        email: newUser.email || undefined,
        phone: newUser.phone || undefined,
        role: newUser.role,
        numero_compte: newUser.numero_compte || generateNumeroCompte(),
      };
      await api.syncUser(profile);
      setShowCreateModal(false);
      setNewUser({ name: '', email: '', phone: '', numero_compte: '', role: 'user' });
      fetchData();
    } catch (error) {
      console.error(error);
    } finally {
      setSubmitting(false);
    }
  };

  const filteredUsers = users.filter(u => 
    u.name.toLowerCase().includes(searchTerm.toLowerCase()) || 
    u.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
    u.numero_compte.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const totalCollected = cotisations
    .filter(c => c.statut === 'valide')
    .reduce((sum, c) => sum + c.montant, 0);

  const totalWithdrawn = retraits
    .filter(r => r.statut === 'valide')
    .reduce((sum, r) => sum + r.montant, 0);

  const netBalance = totalCollected - totalWithdrawn;

  const getUserTransactions = (userId: string) => {
    const userCotisations = cotisations
      .filter(c => c.user_id === userId)
      .map(c => ({ ...c, type_transaction: 'cotisation' as const }));
    const userRetraits = retraits
      .filter(r => r.user_id === userId)
      .map(r => ({ ...r, type_transaction: 'retrait' as const }));
    
    return [...userCotisations, ...userRetraits].sort((a, b) => {
      const dateA = 'date_paiement' in a ? a.date_paiement : a.date_retrait;
      const dateB = 'date_paiement' in b ? b.date_paiement : b.date_retrait;
      return dateB.toMillis() - dateA.toMillis();
    });
  };

  return (
    <div className="space-y-8">
      <header className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Administration</h1>
          <p className="text-slate-500">Gestion globale des utilisateurs et des finances.</p>
        </div>
        <button
          onClick={() => setShowCreateModal(true)}
          className="flex items-center justify-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 active:scale-95"
        >
          <UserPlus className="w-5 h-5" />
          <span>Nouvel Utilisateur</span>
        </button>
      </header>

      {/* Stats Overview */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
          <div className="flex items-center gap-2 text-blue-600 mb-2">
            <Wallet className="w-4 h-4" />
            <p className="text-xs font-bold uppercase tracking-wider">Solde Net</p>
          </div>
          <p className="text-2xl font-bold text-slate-900">{netBalance.toLocaleString()} FCFA</p>
        </div>
        <div className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
          <div className="flex items-center gap-2 text-emerald-600 mb-2">
            <ArrowUpCircle className="w-4 h-4" />
            <p className="text-xs font-bold uppercase tracking-wider">Total Cotisé</p>
          </div>
          <p className="text-2xl font-bold text-slate-900">{totalCollected.toLocaleString()} FCFA</p>
        </div>
        <div className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
          <div className="flex items-center gap-2 text-red-600 mb-2">
            <ArrowDownCircle className="w-4 h-4" />
            <p className="text-xs font-bold uppercase tracking-wider">Total Retiré</p>
          </div>
          <p className="text-2xl font-bold text-slate-900">{totalWithdrawn.toLocaleString()} FCFA</p>
        </div>
        <div className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
          <div className="flex items-center gap-2 text-slate-600 mb-2">
            <Users className="w-4 h-4" />
            <p className="text-xs font-bold uppercase tracking-wider">Utilisateurs</p>
          </div>
          <p className="text-2xl font-bold text-slate-900">{users.length}</p>
        </div>
      </div>

      {/* User Management */}
      <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <h2 className="text-lg font-bold text-slate-900">Gestion des Utilisateurs</h2>
          <div className="relative w-full md:w-72">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
            <input
              type="text"
              placeholder="Rechercher un utilisateur..."
              className="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
        </div>
        
        <div className="overflow-x-auto">
          <table className="w-full text-left">
            <thead>
              <tr className="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <th className="px-6 py-4 font-semibold">Utilisateur</th>
                <th className="px-6 py-4 font-semibold">Email</th>
                <th className="px-6 py-4 font-semibold">Rôle</th>
                <th className="px-6 py-4 font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {loading ? (
                <tr>
                  <td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                    <Loader2 className="w-6 h-6 animate-spin mx-auto" />
                  </td>
                </tr>
              ) : filteredUsers.length > 0 ? (
                filteredUsers.map((u) => (
                  <tr key={u.uid} className="hover:bg-slate-50 transition-colors">
                    <td className="px-6 py-4">
                      <div className="flex flex-col">
                        <span className="text-sm font-medium text-slate-900">{u.name}</span>
                        <span className="text-xs text-slate-500 font-mono">{u.numero_compte}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm text-slate-600">
                      <div className="flex flex-col">
                        <span>{u.email || <span className="text-slate-300 italic">—</span>}</span>
                        {u.phone && <span className="text-xs text-slate-400">{u.phone}</span>}
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <select
                        value={u.role}
                        onChange={(e) => handleUpdateRole(u.uid, e.target.value as any)}
                        className={`text-xs font-bold px-3 py-1 rounded-full border-0 outline-none cursor-pointer ${
                          u.role === 'admin' ? 'bg-purple-100 text-purple-700' :
                          u.role === 'collecteur' ? 'bg-blue-100 text-blue-700' :
                          'bg-slate-100 text-slate-700'
                        }`}
                      >
                        <option value="user">Utilisateur</option>
                        <option value="collecteur">Collecteur</option>
                        <option value="admin">Admin</option>
                      </select>
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <button
                          onClick={() => setShowTransactionsModal(u)}
                          className="p-2 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                          title="Voir les transactions"
                        >
                          <History className="w-4 h-4" />
                        </button>
                        <button
                          onClick={() => handleDeleteUser(u.uid)}
                          className="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                          title="Supprimer l'utilisateur"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                    Aucun utilisateur trouvé.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Create User Modal */}
      <AnimatePresence>
        {showCreateModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
            >
              <div className="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 className="text-xl font-bold text-slate-900">Nouvel Utilisateur</h3>
                <button onClick={() => setShowCreateModal(false)} className="text-slate-400 hover:text-slate-600">
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
                  <label className="text-sm font-medium text-slate-700">Email <span className="text-slate-400 font-normal">(Optionnel)</span></label>
                  <input
                    type="email"
                    className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                    value={newUser.email}
                    onChange={(e) => setNewUser({ ...newUser, email: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-slate-700">Téléphone <span className="text-slate-400 font-normal">(Optionnel)</span></label>
                  <input
                    type="tel"
                    placeholder="Ex: +229 97 00 00 00"
                    className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                    value={newUser.phone}
                    onChange={(e) => setNewUser({ ...newUser, phone: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-slate-700">Numéro de Compte (Optionnel)</label>
                  <input
                    type="text"
                    placeholder="Ex: COT-1234"
                    className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                    value={newUser.numero_compte}
                    onChange={(e) => setNewUser({ ...newUser, numero_compte: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-slate-700">Rôle</label>
                  <select
                    className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                    value={newUser.role}
                    onChange={(e) => setNewUser({ ...newUser, role: e.target.value as any })}
                  >
                    <option value="user">Utilisateur</option>
                    <option value="collecteur">Collecteur</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
                <button
                  type="submit"
                  disabled={submitting}
                  className="w-full bg-blue-600 text-white p-4 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 active:scale-95 flex items-center justify-center gap-2"
                >
                  {submitting ? <Loader2 className="w-5 h-5 animate-spin" /> : 'Créer l\'utilisateur'}
                </button>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      {/* Transactions Modal */}
      <AnimatePresence>
        {showTransactionsModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden"
            >
              <div className="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                  <h3 className="text-xl font-bold text-slate-900">Historique des Transactions</h3>
                  <p className="text-sm text-slate-500">{showTransactionsModal.name} ({showTransactionsModal.numero_compte})</p>
                </div>
                <button onClick={() => setShowTransactionsModal(null)} className="text-slate-400 hover:text-slate-600">
                  <X className="w-6 h-6" />
                </button>
              </div>
              <div className="p-0 max-h-[60vh] overflow-y-auto">
                <table className="w-full text-left">
                  <thead className="sticky top-0 bg-white shadow-sm">
                    <tr className="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                      <th className="px-6 py-4 font-semibold">Date</th>
                      <th className="px-6 py-4 font-semibold">Type</th>
                      <th className="px-6 py-4 font-semibold">Détails</th>
                      <th className="px-6 py-4 font-semibold text-right">Montant</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {getUserTransactions(showTransactionsModal.uid).length > 0 ? (
                      getUserTransactions(showTransactionsModal.uid).map((t, idx) => {
                        const date = 'date_paiement' in t ? t.date_paiement : t.date_retrait;
                        const isCotisation = t.type_transaction === 'cotisation';
                        return (
                          <tr key={idx} className="hover:bg-slate-50 transition-colors">
                            <td className="px-6 py-4 text-sm text-slate-600">
                              {format(date.toDate(), 'dd/MM/yyyy HH:mm')}
                            </td>
                            <td className="px-6 py-4">
                              <span className={`text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full ${
                                isCotisation ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'
                              }`}>
                                {isCotisation ? 'Dépôt' : 'Retrait'}
                              </span>
                            </td>
                            <td className="px-6 py-4 text-sm text-slate-600">
                              {isCotisation ? (t as Cotisation).type : (t as Retrait).motif}
                            </td>
                            <td className={`px-6 py-4 text-sm font-bold text-right ${
                              isCotisation ? 'text-emerald-600' : 'text-red-600'
                            }`}>
                              {isCotisation ? '+' : '-'}{t.montant.toLocaleString()} FCFA
                            </td>
                          </tr>
                        );
                      })
                    ) : (
                      <tr>
                        <td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                          Aucune transaction enregistrée pour cet utilisateur.
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
              <div className="p-6 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
                <span className="text-sm font-medium text-slate-600">Solde Actuel:</span>
                <span className="text-lg font-bold text-slate-900">
                  {getUserTransactions(showTransactionsModal.uid).reduce((sum, t) => 
                    t.type_transaction === 'cotisation' ? sum + t.montant : sum - t.montant, 0
                  ).toLocaleString()} FCFA
                </span>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}

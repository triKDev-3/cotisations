import { useState, useEffect } from 'react';
import { UserProfile, Cotisation, Retrait } from '../firebase';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import { CheckCircle2, Clock, XCircle, ArrowDownCircle, ArrowUpCircle, Wallet, Phone, Mail, Edit2, Save, X, AlertCircle } from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { api } from '../services/api';

interface DashboardProps {
  profile: UserProfile | null;
}

export default function Dashboard({ profile }: DashboardProps) {
  const [cotisations, setCotisations] = useState<Cotisation[]>([]);
  const [retraits, setRetraits] = useState<Retrait[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'cotisations' | 'retraits'>('cotisations');
  const [editMode, setEditMode] = useState(false);
  const [editName, setEditName] = useState(profile?.name || '');
  const [editPhone, setEditPhone] = useState(profile?.phone || '');
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' } | null>(null);

  const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
    setToast({ msg, type });
    setTimeout(() => setToast(null), 4000);
  };

  useEffect(() => {
    if (!profile) return;
    setEditName(profile.name);
    setEditPhone(profile.phone || '');

    const fetchData = async () => {
      try {
        const [cots, rets] = await Promise.all([
          api.getCotisations(profile.uid),
          api.getRetraits(profile.uid)
        ]);
        setCotisations(cots);
        setRetraits(rets);
      } catch (error) {
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
    const interval = setInterval(fetchData, 30000);
    return () => clearInterval(interval);
  }, [profile]);

  const handleSaveProfile = async () => {
    if (!profile) return;
    setSaving(true);
    try {
      await api.syncUser({ ...profile, name: editName, phone: editPhone });
      showToast('Profil mis à jour avec succès !');
      setEditMode(false);
    } catch (error) {
      showToast('Erreur lors de la mise à jour du profil.', 'error');
    } finally {
      setSaving(false);
    }
  };

  const totalCotise = cotisations.filter(c => c.statut === 'valide').reduce((sum, c) => sum + c.montant, 0);
  const totalRetire = retraits.filter(r => r.statut === 'valide').reduce((sum, r) => sum + r.montant, 0);
  const solde = totalCotise - totalRetire;

  const getStatusIcon = (statut: string) => {
    switch (statut) {
      case 'valide': return <CheckCircle2 className="w-4 h-4 text-emerald-500" />;
      case 'en_attente': return <Clock className="w-4 h-4 text-amber-500" />;
      case 'annule': return <XCircle className="w-4 h-4 text-red-500" />;
      default: return null;
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

      {/* Main Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {/* Profile Card */}
        <div className="md:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-base font-bold text-slate-900">Mon Profil</h2>
            {!editMode ? (
              <button
                onClick={() => setEditMode(true)}
                className="flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg transition-colors"
              >
                <Edit2 className="w-3 h-3" />
                Modifier
              </button>
            ) : (
              <button onClick={() => setEditMode(false)} className="text-slate-400 hover:text-slate-600">
                <X className="w-4 h-4" />
              </button>
            )}
          </div>

          <div className="flex flex-col items-center mb-5">
            <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-3 shadow-lg shadow-blue-200">
              {profile?.name?.charAt(0).toUpperCase()}
            </div>
            {!editMode ? (
              <>
                <p className="font-bold text-slate-900 text-lg text-center">{profile?.name}</p>
                <span className="text-xs font-mono bg-blue-50 text-blue-600 px-3 py-1 rounded-full mt-1">
                  {profile?.numero_compte}
                </span>
                <span className={`text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mt-2 ${
                  profile?.role === 'admin' ? 'bg-purple-100 text-purple-700' :
                  profile?.role === 'collecteur' ? 'bg-blue-100 text-blue-700' :
                  'bg-slate-100 text-slate-600'
                }`}>{profile?.role}</span>
              </>
            ) : (
              <div className="w-full space-y-3 mt-2">
                <div>
                  <label className="text-xs font-medium text-slate-500 mb-1 block">Nom</label>
                  <input
                    type="text"
                    className="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    value={editName}
                    onChange={(e) => setEditName(e.target.value)}
                  />
                </div>
                <div>
                  <label className="text-xs font-medium text-slate-500 mb-1 block">Téléphone</label>
                  <input
                    type="tel"
                    placeholder="+229 97 00 00 00"
                    className="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    value={editPhone}
                    onChange={(e) => setEditPhone(e.target.value)}
                  />
                </div>
                <button
                  onClick={handleSaveProfile}
                  disabled={saving}
                  className="w-full bg-blue-600 text-white p-2.5 rounded-xl text-sm font-bold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
                >
                  <Save className="w-4 h-4" />
                  {saving ? 'Enregistrement...' : 'Sauvegarder'}
                </button>
              </div>
            )}
          </div>

          {!editMode && (
            <div className="space-y-3 border-t border-slate-100 pt-4">
              <div className="flex items-center gap-3 text-sm text-slate-600">
                <Mail className="w-4 h-4 text-slate-400 shrink-0" />
                <span className="truncate">{profile?.email || <span className="italic text-slate-300">Non renseigné</span>}</span>
              </div>
              <div className="flex items-center gap-3 text-sm text-slate-600">
                <Phone className="w-4 h-4 text-slate-400 shrink-0" />
                <span>{profile?.phone || <span className="italic text-slate-300">Non renseigné</span>}</span>
              </div>
            </div>
          )}
        </div>

        {/* Stats */}
        <div className="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 content-start">
          <motion.div whileHover={{ y: -4 }} className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div className="flex items-center gap-2 text-blue-600 mb-3">
              <Wallet className="w-4 h-4" />
              <p className="text-xs font-bold uppercase tracking-wider">Solde</p>
            </div>
            <p className="text-2xl font-bold text-slate-900">{solde.toLocaleString()}</p>
            <p className="text-xs text-slate-400 mt-1">FCFA</p>
          </motion.div>

          <motion.div whileHover={{ y: -4 }} className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div className="flex items-center gap-2 text-emerald-600 mb-3">
              <ArrowUpCircle className="w-4 h-4" />
              <p className="text-xs font-bold uppercase tracking-wider">Total Cotisé</p>
            </div>
            <p className="text-2xl font-bold text-slate-900">{totalCotise.toLocaleString()}</p>
            <p className="text-xs text-slate-400 mt-1">FCFA</p>
          </motion.div>

          <motion.div whileHover={{ y: -4 }} className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div className="flex items-center gap-2 text-red-500 mb-3">
              <ArrowDownCircle className="w-4 h-4" />
              <p className="text-xs font-bold uppercase tracking-wider">Total Retiré</p>
            </div>
            <p className="text-2xl font-bold text-slate-900">{totalRetire.toLocaleString()}</p>
            <p className="text-xs text-slate-400 mt-1">FCFA</p>
          </motion.div>
        </div>
      </div>

      {/* Historique */}
      <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="p-2 border-b border-slate-100 flex gap-2">
          <button
            onClick={() => setActiveTab('cotisations')}
            className={`flex-1 py-3 px-4 rounded-xl font-medium transition-all ${
              activeTab === 'cotisations' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'
            }`}
          >
            Cotisations ({cotisations.length})
          </button>
          <button
            onClick={() => setActiveTab('retraits')}
            className={`flex-1 py-3 px-4 rounded-xl font-medium transition-all ${
              activeTab === 'retraits' ? 'bg-red-50 text-red-600' : 'text-slate-500 hover:bg-slate-50'
            }`}
          >
            Retraits ({retraits.length})
          </button>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left">
            <thead>
              <tr className="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <th className="px-6 py-4 font-semibold">Date</th>
                <th className="px-6 py-4 font-semibold">{activeTab === 'cotisations' ? 'Type' : 'Motif'}</th>
                <th className="px-6 py-4 font-semibold">Montant</th>
                <th className="px-6 py-4 font-semibold">Statut</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {activeTab === 'cotisations' ? (
                cotisations.length > 0 ? cotisations.map((c) => (
                  <tr key={c.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-6 py-4 text-sm text-slate-600">
                      {format(c.date_paiement.toDate(), 'dd MMMM yyyy', { locale: fr })}
                    </td>
                    <td className="px-6 py-4 text-sm font-medium text-slate-900">{c.type}</td>
                    <td className="px-6 py-4 text-sm font-bold text-emerald-600">+{c.montant.toLocaleString()} FCFA</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2 text-sm capitalize">
                        {getStatusIcon(c.statut)}
                        <span className={c.statut === 'valide' ? 'text-emerald-600' : c.statut === 'en_attente' ? 'text-amber-600' : 'text-red-600'}>
                          {c.statut.replace('_', ' ')}
                        </span>
                      </div>
                    </td>
                  </tr>
                )) : (
                  <tr><td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                    {loading ? 'Chargement...' : 'Aucune cotisation enregistrée.'}
                  </td></tr>
                )
              ) : (
                retraits.length > 0 ? retraits.map((r) => (
                  <tr key={r.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-6 py-4 text-sm text-slate-600">
                      {format(r.date_retrait.toDate(), 'dd MMMM yyyy', { locale: fr })}
                    </td>
                    <td className="px-6 py-4 text-sm font-medium text-slate-900">{r.motif}</td>
                    <td className="px-6 py-4 text-sm font-bold text-red-600">-{r.montant.toLocaleString()} FCFA</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2 text-sm capitalize">
                        {getStatusIcon(r.statut)}
                        <span className={r.statut === 'valide' ? 'text-emerald-600' : r.statut === 'en_attente' ? 'text-amber-600' : 'text-red-600'}>
                          {r.statut.replace('_', ' ')}
                        </span>
                      </div>
                    </td>
                  </tr>
                )) : (
                  <tr><td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                    {loading ? 'Chargement...' : 'Aucun retrait effectué.'}
                  </td></tr>
                )
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}

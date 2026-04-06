import { useState, useEffect } from 'react';
import { UserProfile, Cotisation, Retrait } from '../firebase';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import { CreditCard, History, TrendingUp, Wallet, CheckCircle2, Clock, XCircle, ArrowDownCircle, ArrowUpCircle } from 'lucide-react';
import { motion } from 'motion/react';
import { api } from '../services/api';

interface DashboardProps {
  profile: UserProfile | null;
}

export default function Dashboard({ profile }: DashboardProps) {
  const [cotisations, setCotisations] = useState<Cotisation[]>([]);
  const [retraits, setRetraits] = useState<Retrait[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'cotisations' | 'retraits'>('cotisations');

  useEffect(() => {
    if (!profile) return;

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

  const totalCotise = cotisations
    .filter(c => c.statut === 'valide')
    .reduce((sum, c) => sum + c.montant, 0);

  const totalRetire = retraits
    .filter(r => r.statut === 'valide')
    .reduce((sum, r) => sum + r.montant, 0);

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
      <header className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Bonjour, {profile?.name}</h1>
          <p className="text-slate-500">Compte: <span className="font-mono font-medium text-blue-600">{profile?.numero_compte}</span></p>
        </div>
      </header>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <motion.div
          whileHover={{ y: -4 }}
          className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4"
        >
          <div className="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
            <Wallet className="w-6 h-6" />
          </div>
          <div>
            <p className="text-sm text-slate-500">Solde Actuel</p>
            <p className="text-2xl font-bold text-slate-900">{solde.toLocaleString()} FCFA</p>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ y: -4 }}
          className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4"
        >
          <div className="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
            <ArrowUpCircle className="w-6 h-6" />
          </div>
          <div>
            <p className="text-sm text-slate-500">Total Cotisé</p>
            <p className="text-2xl font-bold text-slate-900">{totalCotise.toLocaleString()} FCFA</p>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ y: -4 }}
          className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4"
        >
          <div className="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
            <ArrowDownCircle className="w-6 h-6" />
          </div>
          <div>
            <p className="text-sm text-slate-500">Total Retiré</p>
            <p className="text-2xl font-bold text-slate-900">{totalRetire.toLocaleString()} FCFA</p>
          </div>
        </motion.div>
      </div>

      {/* Tabs */}
      <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="p-2 border-b border-slate-100 flex gap-2">
          <button
            onClick={() => setActiveTab('cotisations')}
            className={`flex-1 py-3 px-4 rounded-xl font-medium transition-all ${
              activeTab === 'cotisations' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'
            }`}
          >
            Cotisations
          </button>
          <button
            onClick={() => setActiveTab('retraits')}
            className={`flex-1 py-3 px-4 rounded-xl font-medium transition-all ${
              activeTab === 'retraits' ? 'bg-red-50 text-red-600' : 'text-slate-500 hover:bg-slate-50'
            }`}
          >
            Retraits
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
                cotisations.length > 0 ? (
                  cotisations.map((c) => (
                    <tr key={c.id} className="hover:bg-slate-50 transition-colors">
                      <td className="px-6 py-4 text-sm text-slate-600">
                        {format(c.date_paiement.toDate(), 'dd MMMM yyyy', { locale: fr })}
                      </td>
                      <td className="px-6 py-4 text-sm font-medium text-slate-900">{c.type}</td>
                      <td className="px-6 py-4 text-sm font-bold text-slate-900">
                        {c.montant.toLocaleString()} FCFA
                      </td>
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-2 text-sm capitalize">
                          {getStatusIcon(c.statut)}
                          <span className={
                            c.statut === 'valide' ? 'text-emerald-600' :
                            c.statut === 'en_attente' ? 'text-amber-600' : 'text-red-600'
                          }>
                            {c.statut.replace('_', ' ')}
                          </span>
                        </div>
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                      Aucune cotisation enregistrée.
                    </td>
                  </tr>
                )
              ) : (
                retraits.length > 0 ? (
                  retraits.map((r) => (
                    <tr key={r.id} className="hover:bg-slate-50 transition-colors">
                      <td className="px-6 py-4 text-sm text-slate-600">
                        {format(r.date_retrait.toDate(), 'dd MMMM yyyy', { locale: fr })}
                      </td>
                      <td className="px-6 py-4 text-sm font-medium text-slate-900">{r.motif}</td>
                      <td className="px-6 py-4 text-sm font-bold text-red-600">
                        -{r.montant.toLocaleString()} FCFA
                      </td>
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-2 text-sm capitalize">
                          {getStatusIcon(r.statut)}
                          <span className={
                            r.statut === 'valide' ? 'text-emerald-600' :
                            r.statut === 'en_attente' ? 'text-amber-600' : 'text-red-600'
                          }>
                            {r.statut.replace('_', ' ')}
                          </span>
                        </div>
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                      Aucun retrait effectué.
                    </td>
                  </tr>
                )
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}

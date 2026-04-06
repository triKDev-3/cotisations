import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { onAuthStateChanged, User as FirebaseUser, signInWithPopup } from 'firebase/auth';
import { auth, googleProvider, UserProfile, generateNumeroCompte } from './firebase';
import Layout from './components/Layout';
import Dashboard from './components/Dashboard';
import CollectorDashboard from './components/CollectorDashboard';
import AdminDashboard from './components/AdminDashboard';
import Login from './components/Login';
import { Loader2 } from 'lucide-react';

import { api } from './services/api';

export default function App() {
  const [user, setUser] = useState<FirebaseUser | null>(null);
  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, async (firebaseUser) => {
      setUser(firebaseUser);
      if (firebaseUser) {
        try {
          const newProfile: UserProfile = {
            uid: firebaseUser.uid,
            name: firebaseUser.displayName || 'Utilisateur',
            email: firebaseUser.email || undefined,
            numero_compte: generateNumeroCompte(),
            role: (firebaseUser.email === 'kodokoffikevin@gmail.com' || firebaseUser.email === 'kodokoffkevin@gmail.com') ? 'admin' : 'user',
          };
          const syncedProfile = await api.syncUser(newProfile);
          setProfile(syncedProfile);
        } catch (error) {
          console.error('Erreur sync utilisateur:', error);
          // On définit quand même un profil de base pour ne pas bloquer l'app
          setProfile({
            uid: firebaseUser.uid,
            name: firebaseUser.displayName || 'Utilisateur',
            email: firebaseUser.email || undefined,
            numero_compte: '...',
            role: 'user',
          });
        }
      } else {
        setProfile(null);
      }
      setLoading(false);
    });

    return () => unsubscribe();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-slate-50">
        <Loader2 className="w-8 h-8 animate-spin text-blue-600" />
      </div>
    );
  }

  return (
    <Router>
      <Routes>
        <Route path="/login" element={!user ? <Login /> : <Navigate to="/" />} />
        
        <Route path="/" element={user ? <Layout profile={profile} /> : <Navigate to="/login" />}>
          <Route index element={
            profile?.role === 'admin' || profile?.email === 'kodokoffikevin@gmail.com' ? <AdminDashboard /> :
            profile?.role === 'collecteur' ? <CollectorDashboard /> :
            <Dashboard profile={profile} />
          } />
          
          {/* Admin & Collector Routes */}
          {(profile?.role !== 'user' || profile?.email === 'kodokoffikevin@gmail.com') && (
            <Route path="collecteur" element={<CollectorDashboard />} />
          )}
          
          {/* Admin Routes */}
          {(profile?.role === 'admin' || profile?.email === 'kodokoffikevin@gmail.com') && (
            <Route path="admin" element={<AdminDashboard />} />
          )}
        </Route>
        
        <Route path="*" element={<Navigate to="/" />} />
      </Routes>
    </Router>
  );
}

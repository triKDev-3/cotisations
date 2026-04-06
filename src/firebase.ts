import { initializeApp } from 'firebase/app';
import { getAuth, GoogleAuthProvider, signInWithPopup, onAuthStateChanged, User as FirebaseUser } from 'firebase/auth';
import firebaseConfig from '../firebase-applet-config.json';

// Initialize Firebase
const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);
export const googleProvider = new GoogleAuthProvider();

// Types
export interface UserProfile {
  uid: string;
  name: string;
  email: string;
  numero_compte: string;
  role: 'admin' | 'collecteur' | 'user';
}

export interface Cotisation {
  id?: string;
  user_id: string;
  collecteur_id: string;
  montant: number;
  type: string;
  date_paiement: { toDate: () => Date; toMillis: () => number };
  statut: 'valide' | 'en_attente' | 'annule';
}

export interface Retrait {
  id?: string;
  user_id: string;
  admin_id: string;
  montant: number;
  motif: string;
  date_retrait: { toDate: () => Date; toMillis: () => number };
  statut: 'valide' | 'en_attente' | 'annule';
}

// Helper to generate account number
export function generateNumeroCompte(): string {
  return 'COT-' + Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
}

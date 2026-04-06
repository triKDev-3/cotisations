import { initializeApp } from 'firebase/app';
import { getAuth, GoogleAuthProvider, signInWithPopup, onAuthStateChanged, User as FirebaseUser } from 'firebase/auth';
import { getAnalytics } from "firebase/analytics";

// Config Firebase exacte
const firebaseConfig = {
  apiKey: "AIzaSyDwTB-5YSoRHqmAYLZm8KPYrHI3dCL_8vE",
  authDomain: "cotisation-6a6ab.firebaseapp.com",
  projectId: "cotisation-6a6ab",
  storageBucket: "cotisation-6a6ab.firebasestorage.app",
  messagingSenderId: "466490997212",
  appId: "1:466490997212:web:f5a6166a76a28f65c27719",
  measurementId: "G-HXDMNJ5SP1"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);
export const googleProvider = new GoogleAuthProvider();

// Safe initialization of analytics (only works in browser environments)
export const analytics = typeof window !== 'undefined' ? getAnalytics(app) : null;

// Types
export interface UserProfile {
  uid: string;
  name: string;
  email?: string;
  phone?: string;
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

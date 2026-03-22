import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getFirestore, collection, query, where, orderBy, onSnapshot, limit, addDoc, serverTimestamp } from "firebase/firestore";
import { getAuth } from "firebase/auth";

// Your web app's Firebase configuration
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
const analytics = typeof window !== 'undefined' ? getAnalytics(app) : null;
const db = getFirestore(app);
const auth = getAuth(app);

export { 
  app, analytics, db, auth, 
  collection, query, where, orderBy, onSnapshot, limit, addDoc, serverTimestamp 
};

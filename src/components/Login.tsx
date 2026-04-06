import { signInWithPopup } from 'firebase/auth';
import { auth, googleProvider } from '../firebase';
import { CreditCard, LogIn } from 'lucide-react';
import { motion } from 'motion/react';

export default function Login() {
  const handleLogin = async () => {
    try {
      await signInWithPopup(auth, googleProvider);
    } catch (error) {
      console.error('Login failed:', error);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-50 p-6">
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        className="max-w-md w-full bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 text-center"
      >
        <div className="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-white shadow-lg shadow-blue-200">
          <CreditCard className="w-8 h-8" />
        </div>
        
        <h1 className="text-2xl font-bold text-slate-900 mb-2">Bienvenue sur CotisApp</h1>
        <p className="text-slate-500 mb-8">
          Gérez vos cotisations en toute simplicité et suivez vos contributions en temps réel.
        </p>

        <button
          onClick={handleLogin}
          className="w-full flex items-center justify-center gap-3 bg-white border border-slate-200 p-4 rounded-xl font-medium text-slate-700 hover:bg-slate-50 transition-all hover:border-slate-300 active:scale-[0.98]"
        >
          <img src="https://www.google.com/favicon.ico" className="w-5 h-5" alt="Google" />
          <span>Se connecter avec Google</span>
        </button>

        <div className="mt-8 pt-8 border-t border-slate-100">
          <p className="text-xs text-slate-400">
            En vous connectant, vous acceptez nos conditions d'utilisation et notre politique de confidentialité.
          </p>
        </div>
      </motion.div>
    </div>
  );
}

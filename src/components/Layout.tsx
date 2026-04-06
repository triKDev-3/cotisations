import React, { useState } from 'react';
import { Outlet, Link, useNavigate, useLocation } from 'react-router-dom';
import { auth, UserProfile } from '../firebase';
import { signOut } from 'firebase/auth';
import { LayoutDashboard, Users, CreditCard, LogOut, User, ShieldCheck, Menu, X } from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';

interface LayoutProps {
  profile: UserProfile | null;
}

export default function Layout({ profile }: LayoutProps) {
  const navigate = useNavigate();
  const location = useLocation();
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const handleLogout = async () => {
    await signOut(auth);
    navigate('/login');
  };

  const navLinks = [
    { to: '/', label: 'Tableau de bord', icon: LayoutDashboard, show: true },
    { to: '/collecteur', label: 'Collecteur', icon: Users, show: profile?.role !== 'user' || profile?.email === 'kodokoffikevin@gmail.com' },
    { to: '/admin', label: 'Administration', icon: ShieldCheck, show: profile?.role === 'admin' || profile?.email === 'kodokoffikevin@gmail.com' },
  ];

  const SidebarContent = () => (
    <>
      <div className="p-6 border-b border-slate-100">
        <div className="flex items-center gap-2 text-blue-600 font-bold text-xl">
          <CreditCard className="w-8 h-8" />
          <span>CotisApp</span>
          {profile?.role === 'admin' && (
            <span className="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full uppercase tracking-widest">Admin</span>
          )}
        </div>
      </div>

      <nav className="flex-1 p-4 space-y-2">
        {navLinks.filter(link => link.show).map((link) => (
          <Link
            key={link.to}
            to={link.to}
            onClick={() => setIsMobileMenuOpen(false)}
            className={`flex items-center gap-3 p-3 rounded-lg transition-colors ${
              location.pathname === link.to
                ? 'bg-blue-50 text-blue-600 font-bold'
                : 'hover:bg-slate-50 text-slate-700'
            }`}
          >
            <link.icon className="w-5 h-5" />
            <span>{link.label}</span>
          </Link>
        ))}
      </nav>

      <div className="p-4 border-t border-slate-100">
        <div className="flex items-center gap-3 p-3 mb-4">
          <div className="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
            <User className="w-6 h-6" />
          </div>
          <div className="flex-1 min-w-0">
            <p className="text-sm font-medium text-slate-900 truncate">{profile?.name}</p>
            <p className="text-xs text-slate-500 truncate capitalize">{profile?.role}</p>
          </div>
        </div>
        <button
          onClick={handleLogout}
          className="flex items-center gap-3 w-full p-3 rounded-lg hover:bg-red-50 text-red-600 transition-colors"
        >
          <LogOut className="w-5 h-5" />
          <span>Déconnexion</span>
        </button>
      </div>
    </>
  );

  return (
    <div className="flex min-h-screen bg-slate-50">
      {/* Desktop Sidebar */}
      <aside className="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col">
        <SidebarContent />
      </aside>

      {/* Mobile Menu Overlay */}
      <AnimatePresence>
        {isMobileMenuOpen && (
          <div className="fixed inset-0 z-50 md:hidden">
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setIsMobileMenuOpen(false)}
              className="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
            />
            <motion.aside
              initial={{ x: '-100%' }}
              animate={{ x: 0 }}
              exit={{ x: '-100%' }}
              transition={{ type: 'spring', damping: 25, stiffness: 200 }}
              className="absolute inset-y-0 left-0 w-64 bg-white shadow-2xl flex flex-col"
            >
              <SidebarContent />
            </motion.aside>
          </div>
        )}
      </AnimatePresence>

      {/* Main Content */}
      <main className="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header className="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 md:hidden">
          <button onClick={() => setIsMobileMenuOpen(true)} className="text-slate-500">
            <Menu className="w-6 h-6" />
          </button>
          <div className="flex items-center gap-2 text-blue-600 font-bold text-lg">
            <CreditCard className="w-6 h-6" />
            <span>CotisApp</span>
            {profile?.role === 'admin' && (
              <span className="text-[8px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full uppercase tracking-widest">Admin</span>
            )}
          </div>
          <div className="w-6" /> {/* Spacer */}
        </header>

        <div className="flex-1 overflow-y-auto p-6">
          <motion.div
            key={location.pathname}
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.3 }}
          >
            <Outlet />
          </motion.div>
        </div>
      </main>
    </div>
  );
}

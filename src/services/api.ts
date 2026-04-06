import { UserProfile, Cotisation, Retrait } from '../firebase';

const API_URL = '/api';

export const api = {
  // Users
  getUsers: async (): Promise<UserProfile[]> => {
    const res = await fetch(`${API_URL}/users`);
    return res.json();
  },
  
  syncUser: async (profile: UserProfile): Promise<UserProfile> => {
    const res = await fetch(`${API_URL}/users/sync`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        uid: profile.uid,
        name: profile.name,
        email: profile.email,
        phone: profile.phone,
        numero_compte: profile.numero_compte,
        role: profile.role,
      }),
    });
    return res.json();
  },
  
  updateUserRole: async (uid: string, role: string): Promise<void> => {
    await fetch(`${API_URL}/users/${uid}/role`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ role }),
    });
  },
  
  deleteUser: async (uid: string): Promise<void> => {
    await fetch(`${API_URL}/users/${uid}`, {
      method: 'DELETE',
    });
  },
  
  // Cotisations
  getCotisations: async (userId?: string): Promise<Cotisation[]> => {
    const url = userId ? `${API_URL}/cotisations?userId=${userId}` : `${API_URL}/cotisations`;
    const res = await fetch(url);
    const data = await res.json();
    if (!Array.isArray(data)) {
      console.error('Expected array from /cotisations, got:', data);
      return [];
    }
    return data.map((c: any) => ({
      ...c,
      date_paiement: { toDate: () => new Date(c.date_paiement), toMillis: () => new Date(c.date_paiement).getTime() },
    }));
  },
  
  addCotisation: async (cotisation: any): Promise<Cotisation> => {
    const res = await fetch(`${API_URL}/cotisations`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(cotisation),
    });
    return res.json();
  },
  
  // Retraits
  getRetraits: async (userId?: string): Promise<Retrait[]> => {
    const url = userId ? `${API_URL}/retraits?userId=${userId}` : `${API_URL}/retraits`;
    const res = await fetch(url);
    const data = await res.json();
    if (!Array.isArray(data)) {
      console.error('Expected array from /retraits, got:', data);
      return [];
    }
    return data.map((r: any) => ({
      ...r,
      date_retrait: { toDate: () => new Date(r.date_retrait), toMillis: () => new Date(r.date_retrait).getTime() },
    }));
  },
  
  addRetrait: async (retrait: any): Promise<Retrait> => {
    const res = await fetch(`${API_URL}/retraits`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(retrait),
    });
    return res.json();
  },
  
  // Stats
  getStats: async (): Promise<{ totalCollected: number; totalWithdrawn: number }> => {
    const res = await fetch(`${API_URL}/stats`);
    return res.json();
  },
};

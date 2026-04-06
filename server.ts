import express from 'express';
import { createServer as createViteServer } from 'vite';
import path from 'path';
import { fileURLToPath } from 'url';
import { db } from './src/db/index';
import { users, cotisations, retraits } from './src/db/schema';
import { eq, desc, sql } from 'drizzle-orm';
import cors from 'cors';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = parseInt(process.env.PORT || '3000', 10);

app.use(express.json());
app.use(cors());

// API Routes
  
  // Users
  app.get('/api/users', async (req, res) => {
    try {
      const allUsers = await db.query.users.findMany();
      res.json(allUsers || []);
    } catch (error) {
      console.error('API Error /users:', error);
      res.json([]);
    }
  });

  app.post('/api/users/sync', async (req, res) => {
    const { uid, name, email, numero_compte, role } = req.body;
    const finalRole = (email === 'kodokoffikevin@gmail.com' || email === 'kodokoffkevin@gmail.com') ? 'admin' : role;
    try {
      const existing = await db.query.users.findFirst({ where: eq(users.uid, uid) });
      if (existing) {
        // Update if needed
        await db.update(users).set({ name, email, numero_compte, role: finalRole }).where(eq(users.uid, uid));
        res.json({ ...existing, role: finalRole });
      } else {
        const newUser = await db.insert(users).values({ uid, name, email, numero_compte, role: finalRole }).returning();
        res.json(newUser[0]);
      }
    } catch (error) {
      console.error('API Error /users/sync:', error);
      res.status(500).json({ error: 'Failed to sync user' });
    }
  });

  app.patch('/api/users/:uid/role', async (req, res) => {
    const { uid } = req.params;
    const { role } = req.body;
    try {
      await db.update(users).set({ role }).where(eq(users.uid, uid));
      res.json({ success: true });
    } catch (error) {
      res.status(500).json({ error: 'Failed to update role' });
    }
  });

  app.delete('/api/users/:uid', async (req, res) => {
    const { uid } = req.params;
    try {
      await db.delete(users).where(eq(users.uid, uid));
      res.json({ success: true });
    } catch (error) {
      res.status(500).json({ error: 'Failed to delete user' });
    }
  });

  // Cotisations
  app.get('/api/cotisations', async (req, res) => {
    const { userId } = req.query;
    try {
      let results;
      if (userId) {
        results = await db.select().from(cotisations).where(eq(cotisations.user_id, userId as string)).orderBy(desc(cotisations.date_paiement));
      } else {
        results = await db.select().from(cotisations).orderBy(desc(cotisations.date_paiement));
      }
      res.json(results || []);
    } catch (error) {
      console.error('API Error /cotisations:', error);
      res.json([]);
    }
  });

  app.post('/api/cotisations', async (req, res) => {
    try {
      const newCotisation = await db.insert(cotisations).values(req.body).returning();
      res.json(newCotisation[0]);
    } catch (error) {
      console.error('API Error /cotisations (POST):', error);
      res.status(500).json({ error: 'Failed to create cotisation' });
    }
  });

  // Retraits
  app.get('/api/retraits', async (req, res) => {
    const { userId } = req.query;
    try {
      let results;
      if (userId) {
        results = await db.select().from(retraits).where(eq(retraits.user_id, userId as string)).orderBy(desc(retraits.date_retrait));
      } else {
        results = await db.select().from(retraits).orderBy(desc(retraits.date_retrait));
      }
      res.json(results || []);
    } catch (error) {
      console.error('API Error /retraits:', error);
      res.json([]);
    }
  });

  app.post('/api/retraits', async (req, res) => {
    try {
      const newRetrait = await db.insert(retraits).values(req.body).returning();
      res.json(newRetrait[0]);
    } catch (error) {
      res.status(500).json({ error: 'Failed to create retrait' });
    }
  });

  // Stats
  app.get('/api/stats', async (req, res) => {
    try {
      const totalCotisations = await db.select({ sum: sql<number>`sum(montant)` }).from(cotisations).where(eq(cotisations.statut, 'valide'));
      const totalRetraits = await db.select({ sum: sql<number>`sum(montant)` }).from(retraits).where(eq(retraits.statut, 'valide'));
      
      res.json({
        totalCollected: totalCotisations[0].sum || 0,
        totalWithdrawn: totalRetraits[0].sum || 0,
      });
    } catch (error) {
      res.status(500).json({ error: 'Failed to fetch stats' });
    }
  });

  // Vite middleware for development
  if (process.env.NODE_ENV !== 'production') {
    createViteServer({
      server: { middlewareMode: true },
      appType: 'spa',
    }).then((vite) => {
      app.use(vite.middlewares);
      app.listen(PORT, '0.0.0.0', () => {
        console.log(`Server running on http://localhost:${PORT}`);
      });
    });
  } else {
    const distPath = path.join(process.cwd(), 'dist');
    app.use(express.static(distPath));
    app.get('*', (_req, res) => {
      res.sendFile(path.join(distPath, 'index.html'));
    });
  }

export default app;

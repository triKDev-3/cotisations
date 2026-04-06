import { pgTable, text, timestamp, doublePrecision, uuid } from 'drizzle-orm/pg-core';

export const users = pgTable('users', {
  uid: text('uid').primaryKey(),
  name: text('name').notNull(),
  email: text('email').notNull().unique(),
  numero_compte: text('numero_compte').notNull().unique(),
  role: text('role', { enum: ['admin', 'collecteur', 'user'] }).notNull().default('user'),
  createdAt: timestamp('created_at').defaultNow(),
});

export const cotisations = pgTable('cotisations', {
  id: uuid('id').primaryKey().defaultRandom(),
  user_id: text('user_id').references(() => users.uid, { onDelete: 'cascade' }).notNull(),
  collecteur_id: text('collecteur_id').references(() => users.uid).notNull(),
  montant: doublePrecision('montant').notNull(),
  type: text('type').notNull(),
  date_paiement: timestamp('date_paiement').defaultNow().notNull(),
  statut: text('statut', { enum: ['valide', 'en_attente', 'annule'] }).notNull().default('valide'),
  createdAt: timestamp('created_at').defaultNow(),
});

export const retraits = pgTable('retraits', {
  id: uuid('id').primaryKey().defaultRandom(),
  user_id: text('user_id').references(() => users.uid, { onDelete: 'cascade' }).notNull(),
  admin_id: text('admin_id').references(() => users.uid).notNull(),
  montant: doublePrecision('montant').notNull(),
  motif: text('motif').notNull(),
  date_retrait: timestamp('date_retrait').defaultNow().notNull(),
  statut: text('statut', { enum: ['valide', 'en_attente', 'annule'] }).notNull().default('valide'),
  createdAt: timestamp('created_at').defaultNow(),
});

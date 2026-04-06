-- Migration: rendre email nullable et ajouter la colonne phone

-- 1. Rendre email nullable (supprime la contrainte NOT NULL)
ALTER TABLE "users" ALTER COLUMN "email" DROP NOT NULL;

-- 2. Recréer la contrainte unique sur email pour ignorer les NULL
DROP INDEX IF EXISTS "users_email_unique";
CREATE UNIQUE INDEX "users_email_unique" ON "users"("email") WHERE "email" IS NOT NULL;

-- 3. Ajouter la colonne téléphone (si elle n'existe pas encore)
ALTER TABLE "users" ADD COLUMN IF NOT EXISTS "phone" text;

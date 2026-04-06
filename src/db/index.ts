import { drizzle } from 'drizzle-orm/node-postgres';
import pg from 'pg';
import * as schema from './schema';
import dotenv from 'dotenv';

dotenv.config();

const connectionString = process.env.DATABASE_URL;

if (!connectionString) {
  console.error('DATABASE_URL is not defined in the environment variables.');
}

const pool = new pg.Pool({
  connectionString,
  ssl: connectionString?.includes('sslmode=require') ? { rejectUnauthorized: false } : false,
});

export const db = drizzle(pool, { schema });

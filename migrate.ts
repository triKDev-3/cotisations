import pg from 'pg';
import fs from 'fs';
import dotenv from 'dotenv';

dotenv.config();

const pool = new pg.Pool({
  connectionString: process.env.DATABASE_URL,
  ssl: { rejectUnauthorized: false }
});

async function run() {
  try {
    console.log('Reading migration file...');
    const sql = fs.readFileSync('drizzle/0000_sour_blue_shield.sql', 'utf8');
    
    console.log('Clearing old tables if any...');
    await pool.query(`DROP TABLE IF EXISTS "cotisations" CASCADE; DROP TABLE IF EXISTS "retraits" CASCADE; DROP TABLE IF EXISTS "users" CASCADE;`);
    
    console.log('Executing Drizzle generated statements...');
    const statements = sql.split('--> statement-breakpoint');
    for (const stmt of statements) {
      const cleanStmt = stmt.trim();
      if (cleanStmt) {
        console.log(`Running: ${cleanStmt.substring(0, 50)}...`);
        await pool.query(cleanStmt);
      }
    }
    
    console.log('Database successfully initialized!');
  } catch (error) {
    console.error('Migration failed:', error);
  } finally {
    await pool.end();
  }
}

run();

CREATE TABLE "cotisations" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"user_id" text NOT NULL,
	"collecteur_id" text NOT NULL,
	"montant" double precision NOT NULL,
	"type" text NOT NULL,
	"date_paiement" timestamp DEFAULT now() NOT NULL,
	"statut" text DEFAULT 'valide' NOT NULL,
	"created_at" timestamp DEFAULT now()
);
--> statement-breakpoint
CREATE TABLE "retraits" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"user_id" text NOT NULL,
	"admin_id" text NOT NULL,
	"montant" double precision NOT NULL,
	"motif" text NOT NULL,
	"date_retrait" timestamp DEFAULT now() NOT NULL,
	"statut" text DEFAULT 'valide' NOT NULL,
	"created_at" timestamp DEFAULT now()
);
--> statement-breakpoint
CREATE TABLE "users" (
	"uid" text PRIMARY KEY NOT NULL,
	"name" text NOT NULL,
	"email" text NOT NULL,
	"numero_compte" text NOT NULL,
	"role" text DEFAULT 'user' NOT NULL,
	"created_at" timestamp DEFAULT now(),
	CONSTRAINT "users_email_unique" UNIQUE("email"),
	CONSTRAINT "users_numero_compte_unique" UNIQUE("numero_compte")
);
--> statement-breakpoint
ALTER TABLE "cotisations" ADD CONSTRAINT "cotisations_user_id_users_uid_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("uid") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "cotisations" ADD CONSTRAINT "cotisations_collecteur_id_users_uid_fk" FOREIGN KEY ("collecteur_id") REFERENCES "public"."users"("uid") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retraits" ADD CONSTRAINT "retraits_user_id_users_uid_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("uid") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retraits" ADD CONSTRAINT "retraits_admin_id_users_uid_fk" FOREIGN KEY ("admin_id") REFERENCES "public"."users"("uid") ON DELETE no action ON UPDATE no action;
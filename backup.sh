#!/bin/bash

# Nome del tuo database
DB_NAME="ConnessionePHP"
DB_USER="postgres"
# Nome del file dove vuoi salvare il backup
BACKUP_FILE="database.sql"
PGPASSWORD="html"
# Esegui il dump del database e salva il risultato nel file SQL
pg_dump -U $DB_USER $DB_NAME > $BACKUP_FILE
# Mostra un messaggio di successo
echo "Backup del database eseguito e salvato in $BACKUP_FILE"

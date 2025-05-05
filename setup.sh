#!/bin/bash

# Crea il database se non esiste
psql -U postgres -tc "SELECT 1 FROM pg_database WHERE datname = 'ConnessionePHP'" | grep -q 1 || psql -U postgres -c "CREATE DATABASE \"ConnessionePHP\""

# Esegue gli script SQL nel database
psql -U postgres -d ConnessionePHP -f database.sql

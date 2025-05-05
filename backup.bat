@echo off
set DB_NAME=ConnessionePHP
set DB_USER=postgres
set BACKUP_FILE=database.sql
set PGPASSWORD=html

REM Percorso completo di pg_dump.exe (modifica se necessario)
set PATH_TO_PG="C:\Program Files\PostgreSQL\17\bin"

REM Esegui il backup
"%PATH_TO_PG%\pg_dump.exe" -U %DB_USER% %DB_NAME% > %BACKUP_FILE%

echo Backup completato: %BACKUP_FILE%
pause

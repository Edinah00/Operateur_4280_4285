## Importatin de data.sql dans sqlite3
sqlite3 writable/database.db < base.sql
## Tester
sqlite3 database.db
## Si il y a modif dans base.sql
On supprime database.db, puis on refais l importation
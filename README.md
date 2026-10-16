# EOQ APSI Handover

Web-based EOQ inventory analysis dashboard for APSI teaching-assistant handover.

## Stack
- PHP native
- MySQL / MariaDB
- HTML, CSS, JavaScript
- Chart.js for dynamic charts

## Local setup
1. Place the project folder inside your XAMPP `htdocs` directory, or configure Apache to serve this folder.
2. Create an empty database named `handover_eoq_apsi` in phpMyAdmin.
3. Import `database/schema.sql` to create the tables. No sample records are inserted.
4. Set local database credentials in `config/database.php`.
5. Open the project through Apache, e.g. `http://localhost/Handover-asdos-apsi/`.

## Main modules
- Dashboard summary and charts
- Item master data
- EOQ calculator
- Analysis history
- Reports
- EOQ guide

## Notes
- Dashboard figures must come from MySQL, never hardcoded sample statistics.
- EOQ calculations use annual demand (D), ordering cost (S), and annual holding cost per unit (H).
- Keep database credentials local and do not commit secrets.

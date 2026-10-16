# EOQ APSI Handover

A PHP + MySQL workspace for EOQ inventory analysis and APSI teaching-assistant handover.

## Current foundation
- Responsive dashboard shell and navigation.
- Empty MySQL schema for categories, items, and EOQ analyses.
- PDO configuration with environment-variable overrides.
- Dashboard KPI queries and recent-analysis query.
- Chart.js cost trend chart driven by analysis records, with a proper empty state.
- Placeholder pages for item management, EOQ calculator, analysis history, reports, and guide.

## Stack
- PHP 8+ recommended
- MySQL / MariaDB
- HTML, CSS, JavaScript
- Chart.js (CDN)

## Local setup (XAMPP)
1. Clone the repository into `D:\\Code\\Handover-asdos-apsi` or copy the files there.
2. Make the folder accessible through Apache. Easiest option is to put it under `C:\\xampp\\htdocs\\Handover-asdos-apsi`; otherwise configure an Apache virtual host for `D:\\Code\\Handover-asdos-apsi`.
3. Start Apache and MySQL in XAMPP.
4. In phpMyAdmin, import `database/schema.sql`. It creates the `handover_eoq_apsi` database and tables but inserts no data.
5. Review `config/database.php`. Defaults are host `127.0.0.1`, port `3306`, database `handover_eoq_apsi`, user `root`, and blank password. You can override them with `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` environment variables.
6. Open `http://localhost/Handover-asdos-apsi/` if the project folder is under `htdocs`.

## EOQ variables
- `D`: annual demand
- `S`: ordering cost per order
- `H`: holding cost per unit per year
- `EOQ = sqrt((2 * D * S) / H)`

## Important
- Do not commit real credentials.
- Dashboard values are queried from MySQL and start at zero when the tables are empty; no dummy statistics are seeded.
- Item management and calculation forms are scaffold pages only at this stage. Complete those modules before using this as a live workflow.

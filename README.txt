RainMeter - PHP + MySQL (ProFreeHost-ready)

Setup steps:
1. Upload all files to your site's folder (htdocs/rainmeter).
2. Create a MySQL database & user via ProFreeHost control panel.
3. In phpMyAdmin, run these SQL commands to create tables:

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE rain_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    reading_date DATE,
    rainfall_mm DECIMAL(7,2),
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

4. Edit config.php to set DB credentials.
5. Place your logo file at assets/my_logo.png (or upload via file manager). You already mentioned placing my_logo.png in htdocs; move it into assets/ or update path.
6. Optional: If you want server-side PDF export, download TCPDF (https://tcpdf.org) and place it in the /tcpdf folder.
7. Login via index.php, add readings, or import old data via Import CSV page.
   - You can upload a CSV or place the CSV into /uploads and enter its filename on the Import page.
   - CSV format: date,rainfall_mm,notes (date as YYYY-MM-DD)

Notes:
- Charts page (graphs.php) shows daily/monthly/yearly aggregated views.
- Client-side PDF export available on Dashboard (uses jsPDF & html2canvas). Server-side export (export_pdf.php) requires TCPDF.

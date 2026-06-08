# **EXECUTIVE INSTRUCTION GUIDE: KMI PROCUREMENT SYSTEM**

## **1\. Project Context & Rules**

* **Stack:** VILT (Vue 3, Inertia.js, Laravel 11, Tailwind CSS).  
* **Design System:** Strict Corporate Standard. Do NOT add Neo-Brutalism elements (no thick black borders, no hard shadows). Follow the provided design perfectly.  
* **Data Integrity:** Preserve ALL typos and date anomalies from the raw data. Do not auto-correct "Januari 2025" to "2026" or "Solustion" to "Solution".

## **2\. PHASE 1: Database Restructuring**

* **Cleanup:** Delete existing migrations and seeders related to the procurements table to avoid conflicts.  
* **New Migration:** Create a new migration for procurements.  
* **Schema Strategy (CRITICAL):** Because the raw data contains human errors (e.g., "20 agustus 2025", "Thusrday, April 30"), **ALL date/tracking columns MUST be created as string (VARCHAR)**, not date or datetime.  
* **Columns:**  
  id, no (string), rp\_number (string), description (string), date\_created (string), te\_in (string, nullable), te\_out (string, nullable), send\_for\_approval\_general\_director (string, nullable), re\_te (string, nullable), buyer (string, nullable), po (string, nullable), so (string, nullable), qc (string, nullable), delivery (string, nullable), rr (string, nullable), vendor (string, nullable), timestamps().

## **3\. PHASE 2: Export/Import Synchronization**

* **Export (app/Exports/ProcurementExport.php):** Update the headings() array to strictly match: \['No', 'RP', 'Description', 'Date Created', 'TE In', 'TE Out', 'Send for Approval General Director', 'RE-TE', 'Buyer', 'PO', 'SO', 'QC', 'Delivery', 'RR', 'Vendor'\].  
* **Import (app/Imports/ProcurementImport.php):** Update the row mapping to match the new database schema based on the headings above.

## **4\. PHASE 3: Data Seeding**

* Create database/seeders/ProcurementSeeder.php.  
* Insert all 48 rows of data exactly as extracted from the PDF, preserving all spelling mistakes and illogical dates.  
* Example structure to include:  
  \[  
      'no' \=\> '2', 'rp\_number' \=\> '1000003895', 'description' \=\> 'Asus zen book for SM',   
      'date\_created' \=\> 'Wednesday, April 23, 2025', 'te\_in' \=\> 'Wednesday, April 23, 2025',   
      'te\_out' \=\> 'SU', 'send\_for\_approval\_general\_director' \=\> 'Monday, May 19, 2025',   
      're\_te' \=\> 'Wednesday, May 21, 2025', 'buyer' \=\> 'Tuesday, May 27, 2025',   
      'po' \=\> 'Tuesday, May 27, 2025', 'so' \=\> 'Monday, June 16, 2025',   
      'qc' \=\> 'PT Technology Solustion Indo', 'delivery' \=\> 'Thursday, July 17, 2025',   
      'rr' \=\> 'Monday, July 21, 2025', 'vendor' \=\> null  
  \]

## **5\. PHASE 4: UI/UX Cloning**

Update resources/js/Pages/Dashboard/Index.vue and resources/js/Layouts/AuthenticatedLayout.vue:

* **Sidebar:** bg-slate-800. Top: KMI Logo \+ "Kaltim Methanol Industri". Bottom: Red (bg-red-500) "Logout" button with "Admin" text below it.  
* **Main Background:** Use public/images/kmi1.jpg as the container background. Apply a translucent white overlay (bg-white/80 or similar) so the tables/cards on top are legible.  
* **Header:** "Selamat Datang, Admin".  
* **Summary Cards (5 rounded-lg cards with light shadow-md):**  
  1. Semua Data (bg-slate-800)  
  2. Total RP (bg-blue-600)  
  3. Total TE (bg-green-500)  
  4. Total Re-TE (bg-orange-400)  
  5. Total PO (bg-pink-500)  
* **Table ("Data Procurement"):** Header bg-slate-800 (text-white). Columns: No, Kode, Barang, Status, Tanggal, Aksi. Add search input and blue "Cari" button top right. Action buttons: Edit (bg-yellow-400), Hapus (bg-red-500).
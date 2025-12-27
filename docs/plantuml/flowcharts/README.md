# 📊 Flowcharts (Activity Diagrams)

## Sistem Informasi Kedisiplinan Siswa SMK Negeri 1

### Deskripsi

Folder ini berisi flowchart (activity diagrams) dalam format PlantUML yang menggambarkan alur proses utama sistem.

---

## 📁 Daftar File

| File                        | Deskripsi                         | Swimlanes                     |
| --------------------------- | --------------------------------- | ----------------------------- |
| `flowchart_pencatatan.puml` | Alur Pencatatan Pelanggaran Siswa | Guru, Rules Engine, Sistem    |
| `flowchart_approval.puml`   | Alur Approval Tindak Lanjut       | Pembina, Approver, Sistem     |
| `flowchart_pembinaan.puml`  | Alur Proses Pembinaan Internal    | Rules Engine, Pembina, Sistem |

---

## 🎨 Fitur Styling

Semua flowchart menggunakan:

-   ✅ **Theme `materia-outline`** - Tampilan modern dan profesional
-   ✅ **Swimlanes** - Partition berdasarkan aktor/komponen
-   ✅ **Color-coded sections** - Warna berbeda per aktor
-   ✅ **Decision diamonds** - Keputusan Ya/Tidak dengan warna
-   ✅ **Notes** - Catatan penjelasan di sisi diagram
-   ✅ **Legend** - Legenda simbol dan warna
-   ✅ **Fork/Join** - Untuk proses paralel
-   ✅ **Partition** - Pengelompokan fase proses

---

## 🚀 Cara Render

### Export ke SVG (Recommended untuk Laporan)

```bash
# Single file ke SVG
java -jar plantuml.jar -tsvg flowchart_pencatatan.puml

# Semua file ke SVG
java -jar plantuml.jar -tsvg *.puml

# Output ke folder tertentu
java -jar plantuml.jar -tsvg -o output/ *.puml
```

### Export ke PNG (High Resolution)

```bash
# PNG dengan scale 2x untuk kualitas tinggi
java -jar plantuml.jar -tpng -scale 2 flowchart_pencatatan.puml

# PNG dengan DPI tinggi
java -jar plantuml.jar -tpng -Sdpi=300 flowchart_pencatatan.puml
```

### VS Code Extension

1. Buka file `.puml`
2. Tekan `Alt + D` untuk preview
3. `Ctrl + Shift + P` → "PlantUML: Export Current Diagram"
4. Pilih format: **SVG** atau **PNG**

---

## 📏 Tips untuk Laporan

### Mengapa SVG?

-   ✅ **Tidak pecah/blur** saat di-zoom
-   ✅ **Scalable** untuk berbagai ukuran
-   ✅ **Ukuran file kecil**
-   ✅ **Dapat di-edit** di vector editor

### Cara Embed di Word/LaTeX:

**Microsoft Word:**

1. Export ke SVG
2. Insert → Pictures → From File → pilih .svg
3. Atau convert SVG ke EMF menggunakan Inkscape

**LaTeX:**

```latex
\usepackage{svg}
\includesvg[width=\textwidth]{flowchart_pencatatan}
```

**Google Docs:**

1. Export ke PNG dengan high DPI
2. Insert → Image → Upload

---

## 📋 Struktur Flowchart

### 1. Flowchart Pencatatan Pelanggaran

```
FASE: Pencatatan
├── Guru buka form
├── Pilih siswa & jenis pelanggaran
├── Rules Engine cek frequency rules
├── Input keterangan & bukti
└── Simpan → Trigger tindak lanjut/pembinaan
```

### 2. Flowchart Approval Tindak Lanjut

```
FASE 1: Auto-Generated
├── Rules Engine trigger kasus
├── Generate TindakLanjut + Surat
└── Notifikasi ke Approver

FASE 2: Review & Decision
├── Approver terima notifikasi
├── Review detail & surat
├── Approve → Lanjut ke FASE 3
└── Reject → DITOLAK (FINAL, tidak bisa diubah)

FASE 3: Penanganan (jika Approved)
├── Pembina mulai tangani
├── Cetak surat
└── Selesaikan → SELESAI (FINAL)
```

### 3. Flowchart Pembinaan Internal

```
FASE 1: Trigger
├── Pelanggaran dicatat
├── Hitung total poin
└── Cek range pembinaan rules

FASE 2: Proses
├── Pembina terima notifikasi
├── Mulai pembinaan
└── Lakukan sesi pembinaan

FASE 3: Penyelesaian
├── Input hasil pembinaan
├── Selesaikan
└── Monitoring berkala
```

---

**Dibuat untuk Sistem Informasi Kedisiplinan Siswa SMK Negeri 1**  
**Tanggal: 27 Desember 2024**

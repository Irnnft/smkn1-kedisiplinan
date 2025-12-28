# 🔄 State Diagram

## Sistem Informasi Kedisiplinan Siswa SMK Negeri 1

### Deskripsi

State Diagram menggambarkan berbagai state (kondisi/status) dari entitas dalam sistem dan transisi yang dapat terjadi antar state.

---

## 1. State Diagram: Tindak Lanjut

```mermaid
stateDiagram-v2
    [*] --> Baru : Pelanggaran trigger rules

    Baru --> Disetujui : Approver approve
    Baru --> Ditolak : Approver reject

    Ditolak --> [*] : FINAL

    Disetujui --> Ditangani : Pembina mulai tangani

    Ditangani --> Selesai : Pembina selesaikan

    Selesai --> [*] : FINAL

    state Baru {
        [*] --> WaitingAction
        WaitingAction : Menunggu pembina submit
        WaitingAction : Entry: Generate surat draft
    }

    state MenungguPersetujuan {
        [*] --> WaitingApproval
        WaitingApproval : Menunggu Kepsek/Waka
        WaitingApproval : Entry: Notifikasi approver
    }

    state Disetujui {
        [*] --> ReadyToHandle
        ReadyToHandle : Siap ditangani
        ReadyToHandle : Entry: Set penyetuju_id
    }

    state Ditangani {
        [*] --> InProgress
        InProgress : Pembinaan berlangsung
        InProgress : Entry: Set ditangani_by
        InProgress : Entry: Set ditangani_at
    }

    state Selesai {
        [*] --> Closed
        Closed : Kasus selesai
        Closed : Entry: Set selesai_by
        Closed : Entry: Log hasil
    }
```

---

## 2. State Diagram: Pembinaan Status

```mermaid
stateDiagram-v2
    [*] --> PerluPembinaan : Poin masuk range rule

    PerluPembinaan --> SedangDibina : mulaiPembinaan()

    SedangDibina --> Selesai : selesaikanPembinaan()

    Selesai --> [*]

    state PerluPembinaan {
        [*] --> Waiting
        Waiting : Menunggu pembina
        Waiting : Entry: Set rule_id
        Waiting : Entry: Set poin_trigger
        Waiting : Entry: Set pembina_roles
        Waiting : Do: Muncul di dashboard
    }

    state SedangDibina {
        [*] --> Active
        Active : Proses pembinaan
        Active : Entry: Set dibina_oleh_user_id
        Active : Entry: Set dibina_at
        Active : Do: Input catatan
    }

    state Selesai {
        [*] --> Done
        Done : Pembinaan selesai
        Done : Entry: Set selesai_at
        Done : Entry: Set hasil_pembinaan
    }
```

---

## 3. State Diagram: Siswa Lifecycle

```mermaid
stateDiagram-v2
    [*] --> Aktif : Operator tambah siswa

    state Aktif {
        [*] --> Active
        Active : deleted_at = null
        Active : Do: Dapat dicatat pelanggaran
        Active : Do: Muncul di daftar
        Active : Do: Dapat naik kelas
    }

    Aktif --> Aktif : Update data
    Aktif --> Aktif : Naik kelas

    Aktif --> TidakAktif : Soft delete

    state TidakAktif {
        [*] --> Inactive
        Inactive : deleted_at != null
        Inactive : Entry: Set alasan_keluar
        Inactive : Do: Hidden dari daftar
        Inactive : Do: Riwayat tetap ada
    }

    TidakAktif --> Aktif : Restore

    TidakAktif --> [*] : Force delete
```

---

## 4. State Diagram: User Activation

```mermaid
stateDiagram-v2
    [*] --> Aktif : Operator buat user

    state Aktif {
        [*] --> Active
        Active : is_active = true
        Active : Entry: Set password default
        Active : Do: Dapat login
        Active : Do: Akses sesuai role
    }

    Aktif --> TidakAktif : toggleActivation()

    state TidakAktif {
        [*] --> Inactive
        Inactive : is_active = false
        Inactive : Entry: Terminate session
        Inactive : Do: Tidak bisa login
        Inactive : Do: Hidden dari dropdown
    }

    TidakAktif --> Aktif : toggleActivation()
```

---

## 5. State Diagram: Jenis Pelanggaran

```mermaid
stateDiagram-v2
    [*] --> Aktif : Operator buat jenis

    state Aktif {
        [*] --> Active
        Active : is_active = true
        Active : Do: Muncul di form catat
        Active : Do: Rules aktif
        Active : Do: Dapat dicatat
    }

    Aktif --> TidakAktif : toggleActive()

    state TidakAktif {
        [*] --> Inactive
        Inactive : is_active = false
        Inactive : Do: Hidden dari form
        Inactive : Do: Riwayat lama tetap ada
        Inactive : Do: Rules non-aktif
    }

    TidakAktif --> Aktif : toggleActive()
```

---

## 6. State Diagram: Tipe Surat Panggilan

```mermaid
stateDiagram-v2
    direction LR

    [*] --> DetermineSurat : Frequency rule matched

    DetermineSurat --> Surat1 : pembina_roles.count = 1
    DetermineSurat --> Surat2 : pembina_roles.count = 2
    DetermineSurat --> Surat3 : pembina_roles.count = 3
    DetermineSurat --> Surat4 : pembina_roles.count >= 4

    state Surat1 {
        [*] --> S1
        S1 : Pembina: Wali Kelas
        S1 : Level: Ringan
    }

    state Surat2 {
        [*] --> S2
        S2 : Pembina: WK + Kaprodi
        S2 : Level: Sedang
    }

    state Surat3 {
        [*] --> S3
        S3 : Pembina: WK + Kaprodi + Waka
        S3 : Level: Berat
    }

    state Surat4 {
        [*] --> S4
        S4 : Pembina: Semua + Kepsek
        S4 : Level: Sangat Berat
    }

    Surat1 --> [*]
    Surat2 --> [*]
    Surat3 --> [*]
    Surat4 --> [*]
```

---

## 7. Composite State: Alur Lengkap Sistem

```mermaid
stateDiagram-v2
    [*] --> Pelanggaran

    state Pelanggaran {
        [*] --> Dicatat
        Dicatat --> CekRules
        CekRules --> TriggerTL : frequency match
        CekRules --> Done : no match
    }

    state TindakLanjutFlow {
        [*] --> TL_Baru
        TL_Baru --> TL_Approved
        TL_Baru --> TL_Rejected
        TL_Rejected --> TL_Final : FINAL
        TL_Approved --> TL_Handled
        TL_Handled --> TL_Done
    }

    state PembinaanFlow {
        [*] --> PB_Check
        PB_Check --> PB_Perlu : poin match rule
        PB_Check --> NoPembinaan : no match
        PB_Perlu --> PB_Sedang
        PB_Sedang --> PB_Selesai
    }

    Pelanggaran --> TindakLanjutFlow : trigger
    Pelanggaran --> PembinaanFlow : parallel check

    TindakLanjutFlow --> [*]
    PembinaanFlow --> [*]
```

---

## Ringkasan State

| Entitas          | States | Final State         | Keterangan             |
| ---------------- | ------ | ------------------- | ---------------------- |
| TindakLanjut     | 4      | Selesai / Ditolak   | Ditolak = FINAL        |
| PembinaanStatus  | 3      | Selesai             | Linear flow            |
| Siswa            | 2      | [End]               | Ya (Restore possible)  |
| User             | 2      | -                   | Ya (Toggle activation) |
| JenisPelanggaran | 2      | -                   | Ya (Toggle active)     |
| SuratPanggilan   | 4 tipe | Determined by rules | One-time determination |

---

**Dokumen ini menggunakan sintaks Mermaid.js**  
**Terakhir diupdate: 27 Desember 2024**

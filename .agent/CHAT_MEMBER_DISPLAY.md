# Fitur Tampilan Member Grup Chat

## Deskripsi Perubahan

Menampilkan daftar nama anggota grup di header chat, sehingga user dapat melihat siapa saja yang ada di dalam grup (kecuali diri sendiri).

**Update:** Elemen member list sekarang disembunyikan secara default dan hanya muncul ketika user memilih grup.

## Detail Implementasi

### 1. Perubahan UI (HTML)
- Menambahkan elemen `<div id="group-members">` di header chat untuk menampilkan daftar member
- Element ini berada di bawah nama grup dengan styling yang sesuai
- **Default state: `display: none`** (tersembunyi)

### 2. Styling (CSS)
```css
#group-members {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

#group-members:before {
    content: "👥";
    font-size: 0.875rem;
}
```
- Menggunakan icon 👥 untuk menandakan member
- Font size kecil (0.875rem) agar tidak terlalu dominan
- Warna abu-abu (#95a5a6) untuk secondary text
- Default: `display: none` di inline style HTML

### 3. JavaScript Logic
- Fungsi baru: `loadGroupDetails(groupId)` 
  - Mengambil detail grup dari API `/chat/groups/{id}`
  - Filter member untuk mengecualikan user yang sedang login
  - Menampilkan nama member yang di-join dengan koma
  - **Show element:** `membersDiv.style.display = 'flex'` ketika data berhasil dimuat
  - **Hide element:** `membersDiv.style.display = 'none'` ketika error atau tidak ada grup

- Fungsi yang dimodifikasi: `selectGroup(groupId)`
  - Memanggil `loadGroupDetails()` sebelum load messages
  - Memastikan nama grup dan member selalu update

### 4. Behavior
| Kondisi | Tampilan Member List |
|---------|---------------------|
| Belum pilih grup | ❌ Hidden (`display: none`) |
| Sudah pilih grup (ada member lain) | ✅ Visible - Menampilkan nama member |
| Sudah pilih grup (hanya sendiri) | ✅ Visible - "Only you in this group" |
| Error loading grup | ❌ Hidden (`display: none`) |

### 5. API Endpoint yang Digunakan
- **GET** `/chat/groups/{id}`
  - Response: 
    ```json
    {
      "success": true,
      "group": {
        "id": 1,
        "name": "TSC",
        "description": "...",
        "members": [
          {"id": 1, "name": "User 1", "email": "..."},
          {"id": 2, "name": "User 2", "email": "..."}
        ]
      }
    }
    ```

## Contoh Tampilan

**Sebelum pilih grup:**
```
[Header] Group Chat
         (member list tidak terlihat)
```

**Setelah pilih grup TSC:**
```
[Header] TSC
         👥 Alice, Bob, Charlie
```

## Catatan
- ✅ **Hidden by default** - Member list tersembunyi sampai user klik grup
- ✅ **Auto show** - Otomatis muncul saat grup dipilih
- ✅ **Auto hide** - Otomatis sembunyi saat error atau tidak ada data
- Jika hanya ada user sendiri di grup, akan muncul: "Only you in this group"
- Member list akan di-refresh setiap kali user switch grup

<?php 
include 'koneksi.php'; 

// Fitur Aksi Backend PHP (Hapus & Kosongkan Data)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        mysqli_query($conn, "DELETE FROM pendaftar WHERE id = $id");
        header("Location: index.php?status=deleted"); // redirect agar form tidak resubmit
        exit;
    }
    if ($_GET['action'] == 'clearall') {
        mysqli_query($conn, "TRUNCATE TABLE pendaftar");
        header("Location: index.php?status=cleared");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Lomba HUT RI</title>
    <!-- Tambahkan CSS Anda di sini -->
    <style>
        /* Gaya dasar style Anda sebelumnya */
        .selected { border: 2px solid red; background-color: #ffe6eb; }
        #teamSection { display: none; }
        #teamSection.active { display: block; }
        .no-data { text-align: center; color: #888; }
    </style>
</head>
<body>

    <button id="btnToggle" onclick="toggleMode()">Menu Panitia/Admin 🔒</button>

    <!-- Halaman Formulir Pendaftaran -->
    <div id="userFormSection">
        <h2>Formulir Pendaftaran</h2>
        <form id="lombaForm" onsubmit="handleSubmit(event)">
            <input type="text" id="nama" placeholder="Nama Lengkap" required><br><br>
            <input type="number" id="umur" placeholder="Umur" required><br><br>
            
            <label id="label-l"><input type="radio" name="jk" value="Laki-laki" onclick="selectGender('Laki-laki')" required> Laki-laki</label>
            <label id="label-p"><input type="radio" name="jk" value="Perempuan" onclick="selectGender('Perempuan')"> Perempuan</label><br><br>

            <select id="kategori" onchange="handleKategoriChange()" required>
                <option value="" disabled selected>-- Pilih Kategori --</option>
                <option value="k1">Kategori 1 (3-4 Th)</option>
                <option value="k2">Kategori 2 (5-7 Th)</option>
                <option value="k3">Kategori 3 (8-10 Th)</option>
                <option value="kremaja">Kategori Remaja</option>
                <option value="kibu">Kategori Ibu-Ibu</option>
            </select><br><br>

            <div id="lombaWrapper" style="display:none;">
                <select id="lomba" onchange="handleLombaChange()" required></select><br><br>
            </div>

            <div id="teamSection">
                <input type="text" id="namaTim" placeholder="Nama Tim"><br><br>
                <div id="anggotaContainer"></div>
            </div>

            <button type="submit">Daftar Sekarang 🚀</button>
        </form>
    </div>

    <!-- Halaman Admin Panel -->
    <div id="adminSection" style="display:none;">
        <div id="adminLoginArea">
            <h2>Login Panitia</h2>
            <input type="password" id="adminPassword" placeholder="Masukkan Password">
            <button onclick="loginAdmin()">Masuk</button>
        </div>

        <div id="adminPanelArea" style="display:none;">
            <h2>Panel Kontrol Data Peserta (Realtime Database)</h2>
            <button onclick="exportToCSV()">Ekspor ke Excel (CSV)</button>
            <button onclick="clearAllData()" style="background-color:red; color:white;">Kosongkan Semua Data</button>
            <br><br>
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Umur</th>
                        <th>Jenis Kelamin</th>
                        <th>Kategori Usia</th>
                        <th>Nama Lomba</th>
                        <th>Info Kelompok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pesertaTableBody">
                    <!-- Data dari PHP akan dimuat secara realtime di sini -->
                </tbody>
            </table>
        </div>
    </div>

<script>
const dataLomba = {
    k1: [
        { id: "mewarnai", nama: "Mewarnai (17 Agustus)", beregu: false },
        { id: "sepeda_hias", nama: "Sepeda Hias (17 Agustus)", beregu: false },
        { id: "makan_kerupuk_1", nama: "Makan Kerupuk", beregu: false }
    ],
    k2: [
        { id: "tusuk_balon", nama: "Tusuk Balon (15 Agustus)", beregu: false },
        { id: "sedotan_botol", nama: "Masukin Sedotan ke Botol (15 Agustus)", beregu: false },
        { id: "bola_keranjang", nama: "Masukin Bola ke Keranjang (16 Agustus)", beregu: false },
        { id: "makan_kerupuk_2", nama: "Makan Kerupuk (16 Agustus)", beregu: false }
    ],
    k3: [
        { id: "estafet_air_3", nama: "Estafet Air (Lomba Kelompok) – 15 Agustus", beregu: true, anggota: 5 },
        { id: "gurita_3", nama: "Gurita (5 orang per tim) – 16 Agustus", beregu: true, anggota: 5 },
        { id: "ambil_koin", nama: "Ambil Koin di Buah – 16 Agustus", beregu: false },
        { id: "pakai_baju", nama: "Lomba Pakai Baju (Kemeja) – 15 Agustus", beregu: false }
    ],
    kremaja: [
        { id: "gurita_rem", nama: "Gurita (5 orang per tim) – 16 Agustus", beregu: true, anggota: 5 },
        { id: "estafet_air_rem", nama: "Estafet Air (5 orang per tim) – 15 Agustus", beregu: true, anggota: 5 },
        { id: "futsal_rem", nama: "Mini Futsal (5 orang per tim) – 15–17 Agustus", beregu: true, anggota: 5 },
        { id: "balap_karung", nama: "Balap Karung – 16 Agustus", beregu: false }
    ],
    kibu: [
        { id: "gurita_ibu", nama: "Gurita (5 orang per tim) – 17 Agustus", beregu: true, anggota: 5 },
        { id: "voli_jumbo", nama: "Voli Bola Jumbo – 17 Agustus", beregu: true, anggota: 5 },
        { id: "suit_kardus", nama: "Suit Kardus – 17 Agustus", beregu: false }
    ]
};

const namaKategori = {
    k1: "Kategori 1 (3-4 Th)",
    k2: "Kategori 2 (5-7 Th)",
    k3: "Kategori 3 (8-10 Th)",
    kremaja: "Kategori Remaja",
    kibu: "Kategori Ibu-Ibu"
};

let isUserMode = true;
let isAdminLoggedIn = false;

function toggleMode() {
    const userSection = document.getElementById("userFormSection");
    const adminSection = document.getElementById("adminSection");
    const toggleBtn = document.getElementById("btnToggle");

    if (isUserMode) {
        userSection.style.display = "none";
        adminSection.style.display = "block";
        toggleBtn.textContent = "Kembali ke Formulir Pendaftaran 📝";
        isUserMode = false;
        if (isAdminLoggedIn) {
            document.getElementById("adminLoginArea").style.display = "none";
            document.getElementById("adminPanelArea").style.display = "block";
            loadAdminData();
        }
    } else {
        userSection.style.display = "block";
        adminSection.style.display = "none";
        toggleBtn.textContent = "Menu Panitia/Admin 🔒";
        isUserMode = true;
    }
}

function loginAdmin() {
    const passInput = document.getElementById("adminPassword");
    if (passInput.value === "HUT RI 81") {
        isAdminLoggedIn = true;
        document.getElementById("adminLoginArea").style.display = "none";
        document.getElementById("adminPanelArea").style.display = "block";
        passInput.value = "";
        loadAdminData();
    } else {
        alert("❌ Password salah!");
    }
}

function selectGender(gender) {
    const labelL = document.getElementById('label-l');
    const labelP = document.getElementById('label-p');
    if (gender === 'Laki-laki') {
        labelL.classList.add('selected');
        labelP.classList.remove('selected');
    } else {
        labelP.classList.add('selected');
        labelL.classList.remove('selected');
    }
}

function handleKategoriChange() {
    const kategoriSelect = document.getElementById("kategori");
    const lombaWrapper = document.getElementById("lombaWrapper");
    const lombaSelect = document.getElementById("lomba");
    const selectedKategori = kategoriSelect.value;

    lombaSelect.innerHTML = '<option value="" disabled selected>-- Pilih Lomba --</option>';
    document.getElementById("teamSection").classList.remove("active");

    if (selectedKategori && dataLomba[selectedKategori]) {
        dataLomba[selectedKategori].forEach(lomba => {
            const option = document.createElement("option");
            option.value = lomba.id;
            option.textContent = lomba.nama;
            option.dataset.beregu = lomba.beregu;
            option.dataset.anggota = lomba.anggota || 0;
            lombaSelect.appendChild(option);
        });
        lombaWrapper.style.display = "block";
    } else {
        lombaWrapper.style.display = "none";
    }
}

function handleLombaChange() {
    const lombaSelect = document.getElementById("lomba");
    const selectedOption = lombaSelect.options[lombaSelect.selectedIndex];
    const isBeregu = selectedOption.dataset.beregu === "true";
    const jumlahAnggota = parseInt(selectedOption.dataset.anggota);
    
    const teamSection = document.getElementById("teamSection");
    const anggotaContainer = document.getElementById("anggotaContainer");
    const namaTimInput = document.getElementById("namaTim");

    if (isBeregu) {
        teamSection.classList.add("active");
        namaTimInput.required = true;
        anggotaContainer.innerHTML = "";
        for (let i = 1; i < jumlahAnggota; i++) {
            const input = document.createElement("input");
            input.type = "text";
            input.className = "team-member-input";
            input.placeholder = `Nama Anggota ${i + 1}`;
            input.required = true;
            anggotaContainer.appendChild(input);
        }
    } else {
        teamSection.classList.remove("active");
        namaTimInput.required = false;
        anggotaContainer.innerHTML = "";
        namaTimInput.value = "";
    }
}

// ------------------------------------
// AJAX PENGIRIMAN DATA FORMULIR KE PHP
// ------------------------------------
function handleSubmit(event) {
    event.preventDefault();

    const nama = document.getElementById("nama").value;
    const umur = document.getElementById("umur").value;
    const jk = document.querySelector('input[name="jk"]:checked').value;
    const kategoriVal = document.getElementById("kategori").value;
    const lombaSelect = document.getElementById("lomba");
    const lombaText = lombaSelect.options[lombaSelect.selectedIndex].text;
    const isBeregu = lombaSelect.options[lombaSelect.selectedIndex].dataset.beregu === "true";
    
    let infoKelompok = "Individu";
    if (isBeregu) {
        const namaTim = document.getElementById("namaTim").value;
        const anggotaInputs = document.querySelectorAll(".team-member-input");
        let anggotaList = [];
        anggotaInputs.forEach(input => {
            if(input.value.trim() !== "") anggotaList.push(input.value.trim());
        });
        infoKelompok = `Tim: ${namaTim} (Anggota: ${anggotaList.join(", ")})`;
    }

    const payload = {
        nama: nama,
        umur: umur,
        jk: jk,
        kategori: namaKategori[kategoriVal] || kategoriVal,
        lomba: lombaText,
        kelompok: infoKelompok
    };

    // Kirim data ke backend PHP simpan.php menggunakan fetch API
    fetch('simpan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success") {
            alert("🎉 " + data.message);
            document.getElementById("lombaForm").reset();
            document.getElementById("lombaWrapper").style.display = "none";
            document.getElementById("teamSection").classList.remove("active");
            document.getElementById('label-l').classList.remove('selected');
            document.getElementById('label-p').classList.remove('selected');
        } else {
            alert("❌ " + data.message);
        }
    })
    .catch(err => alert("Terjadi kesalahan koneksi internet: " + err));
}

// ------------------------------------
// FETCH AMBIL DATA DATABASE KE TABEL
// ------------------------------------
function loadAdminData() {
    const tbody = document.getElementById("pesertaTableBody");
    tbody.innerHTML = `<tr><td colspan="8" class="no-data">Memuat data dari server database...</td></tr>`;

    // Ambil data melalui jembatan API data kecil dari php
    fetch('ambil_data.php')
    .then(response => response.json())
    .then(listPendaftar => {
        tbody.innerHTML = "";
        if (listPendaftar.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="no-data">Belum ada peserta yang mendaftar.</td></tr>`;
            return;
        }

        listPendaftar.forEach((peserta, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${index + 1}</td>
                <td><strong>${peserta.nama}</strong></td>
                <td>${peserta.umur} Th</td>
                <td>${peserta.jk}</td>
                <td>${peserta.kategori}</td>
                <td>${peserta.lomba}</td>
                <td><small>${peserta.kelompok}</small></td>
                <td>
                    <a href="index.php?action=delete&id=${peserta.id}" onclick="return confirm('Hapus peserta ini?')">
                        <button style="background-color:red; color:white; padding: 5px 10px;">Hapus</button>
                    </a>
                </td>
            `;
            tbody.appendChild(row);
        });
    });
}

function deletePeserta(id) {
    if (confirm("Apakah Anda yakin ingin menghapus peserta ini?")) {
        window.location.href = `index.php?action=delete&id=${id}`;
    }
}

function clearAllData() {
    if (confirm("⚠️ PERINGATAN! Anda akan menghapus seluruh database secara permanen. Lanjutkan?")) {
        window.location.href = "index.php?action=clearall";
    }
}

function exportToCSV() {
    fetch('ambil_data.php')
    .then(response => response.json())
    .then(listPendaftar => {
        if (listPendaftar.length === 0) {
            alert("Tidak ada data untuk diunduh.");
            return;
        }
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "No,Nama Lengkap,Umur,Jenis Kelamin,Kategori Usia,Nama Lomba,Info Kelompok\n";

        listPendaftar.forEach((p, index) => {
            const namaClean = p.nama.replace(/,/g, " ");
            const kelompokClean = p.kelompok.replace(/,/g, " | ");
            const row = `${index + 1},${namaClean},${p.umur},${p.jk},${p.kategori},${p.lomba},${kelompokClean}\n`;
            csvContent += row;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "pendaftar_lomba_hut_ri.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
}
</script>
</body>
</html>
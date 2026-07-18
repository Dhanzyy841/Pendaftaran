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

// Beralih antara Halaman Formulir dan Halaman Admin
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

// Verifikasi Login Admin
function loginAdmin() {
    const passInput = document.getElementById("adminPassword");
    if (passInput.value === "HUT RI 81") {
        isAdminLoggedIn = true;
        document.getElementById("adminLoginArea").style.display = "none";
        document.getElementById("adminPanelArea").style.display = "block";
        passInput.value = "";
        loadAdminData();
    } else {
        alert("❌ Password salah! Silakan hubungi ketua panitia.");
    }
}

// Fitur Pembuat QR Code Otomatis
function generateQRCode() {
    const urlInput = document.getElementById("websiteUrlInput").value.trim();
    const qrContainer = document.getElementById("qrResultContainer");
    const qrImg = document.getElementById("qrCodeImg");

    if (!urlInput) {
        alert("Silakan masukkan alamat URL website Anda terlebih dahulu!");
        return;
    }

    const encodedUrl = encodeURIComponent(urlInput);
    qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodedUrl}&color=255-0-43&bgcolor=255-255-255`;
    
    qrContainer.style.display = "block";
}

// Mengatur Desain Kartu Gender yang Dipilih
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

// Mengubah Pilihan Daftar Lomba Berdasarkan Kategori yang Dipilih
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

// Menampilkan / Menyembunyikan Input Tim dan Anggota
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

// Load Data dari Browser Storage (Local Storage)
function getStoredData() {
    const data = localStorage.getItem("pendaftarLomba");
    return data ? JSON.parse(data) : [];
}

// Menyimpan Data Baru ke Browser Storage
function saveStoredData(data) {
    localStorage.setItem("pendaftarLomba", JSON.stringify(data));
}

// Menangani Pengiriman Formulir Pendaftaran
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
            if(input.value.trim() !== "") {
                anggotaList.push(input.value.trim());
            }
        });
        infoKelompok = `Tim: ${namaTim} (Anggota: ${anggotaList.join(", ")})`;
    }

    const pendaftarBaru = {
        id: Date.now(),
        nama: nama,
        umur: umur,
        jk: jk,
        kategori: namaKategori[kategoriVal] || kategoriVal,
        lomba: lombaText,
        kelompok: infoKelompok
    };

    const listPendaftar = getStoredData();
    listPendaftar.push(pendaftarBaru);
    saveStoredData(listPendaftar);

    alert("🎉 Selamat! Pendaftaran Anda berhasil dikirim dan disimpan.");
    
    document.getElementById("lombaForm").reset();
    document.getElementById("lombaWrapper").style.display = "none";
    document.getElementById("teamSection").classList.remove("active");
    document.getElementById('label-l').classList.remove('selected');
    document.getElementById('label-p').classList.remove('selected');
}

// Menggambar Data Peserta ke dalam Tabel Admin
function loadAdminData() {
    const listPendaftar = getStoredData();
    const tbody = document.getElementById("pesertaTableBody");
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
                <button class="btn-action btn-danger" style="padding: 5px 10px; font-size: 0.75rem;" onclick="deletePeserta(${peserta.id})">Hapus</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Menghapus Salah Satu Peserta
function deletePeserta(id) {
    if (confirm("Apakah Anda yakin ingin menghapus peserta ini?")) {
        let listPendaftar = getStoredData();
        listPendaftar = listPendaftar.filter(p => p.id !== id);
        saveStoredData(listPendaftar);
        loadAdminData();
    }
}

// Mengosongkan Seluruh Data Tabel
function clearAllData() {
    if (confirm("⚠️ PERINGATAN! Anda akan menghapus seluruh data pendaftaran secara permanen. Lanjutkan?")) {
        localStorage.removeItem("pendaftarLomba");
        loadAdminData();
    }
}

// Mengekspor Semua Data Tabel ke CSV (Bisa Dibuka Langsung di Microsoft Excel)
function exportToCSV() {
    const listPendaftar = getStoredData();
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
}
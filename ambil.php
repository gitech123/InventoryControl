<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
require "authMiddleware.php";

// Mengambil id_barang dari tabel simpan_id_sementara
$sqlkeranjang = "SELECT id_barang FROM simpan_id_sementara";
$result = $conn->query($sqlkeranjang);

if ($result->num_rows > 0) {
    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id_barang'];
    }
    
    $idsString = implode(",", $ids);
    // SQL SELECT query to get data from stock based on ids
    $sqlBarang = "SELECT * FROM stock WHERE id IN ($idsString)";
    $barang = $conn->query($sqlBarang);
    

} else {
    echo "<script> alert('Item Keranjang Kosong');document.location = 'daftarBarang.php'; </script>";
}
// NO SPB
$bulan_spb = date("m");
$tahun_spb = date("Y");
    $romawi_bulan = array(
        '01' => "I",
        '02' => "II",
        '03' => "III",
        '04' => "IV",
        '05' => "V",
        '06' => "VI",
        '07' => "VII",
        '08' => "VIII",
        '09' => "IX",
        '10' => "X",
        '11' => "XI",
        '12' => "XII"
    );
    $bulan_romawi = $romawi_bulan[$bulan_spb];
$sqlBarangcat = "SELECT id, kategori FROM stock WHERE id IN ($idsString) LIMIT 1";
$barangcat = $conn->query($sqlBarangcat);

if ($barangcat->num_rows > 0) {
    $rowBarangcat = $barangcat->fetch_assoc(); // Ambil data pertama
    $kategoricat = $rowBarangcat['kategori'];  // Simpan kategori
    $idBarangcat = $rowBarangcat['id'];        // Simpan id barang

    // Logika pengambilan no_spb berdasarkan kategori
    if ($kategoricat === 'Stock') {
        $sqlNoSPB = "SELECT no_spb FROM ambil_barang WHERE bulan_spb = '$bulan_romawi' AND tahun_spb = $tahun_spb ORDER BY no_spb DESC LIMIT 1";
    } else {
        $sqlNoSPB = "SELECT no_spb FROM ambil_barang WHERE bulan_spb = $bulan_spb AND tahun_spb = $tahun_spb ORDER BY no_spb DESC LIMIT 1";
    }

    $resultNoSPB = $conn->query($sqlNoSPB);

    // Ambil hasil query
    $no_spb = $row['no_spb'] ?? 0;
    if ($resultNoSPB->num_rows > 0) {
        $rowNoSPB = $resultNoSPB->fetch_assoc();
        $no_spb = $rowNoSPB['no_spb'];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/flatpickr.min.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
    body {
        background: url("assets/images/bg-ambil.png");
        background-repeat: no-repeat;
        background-size: 100% 100%;
        background-position: center top;
        color: #fff;
    }

    .btn-request {
        background: #ea2e2e;
        color: #fff;
        border: 1px solid #EA2E2EFF;
    }

    input {
        margin: 0 5px;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <form id="barangForm" action="ambilAction.php" method="POST">
            <input type="hidden" name="barang_id" value="<?php echo $idsString; ?>">
            <div
                style="margin: 10vh 5vw; max-height: 80vh; overflow-y: auto; overflow-x: hidden; width: 90vw; max-width: 100%;">
                <!-- Konten Anda -->
                <table class="table table-bordered table-dark">
                    <tr>
                        <td style="text-align: center; align-content: center;" rowspan="2" colspan="2"
                            class="fs-3 fw-bold">PT. Santos Jaya Abadi 2 Karawang</td>
                        <td colspan="4" class="text-center fs-3 fw-bold">Surat Permintaan Barang</td>
                        <td colspan="4">Form No : QF / FAA / 04</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 10px">
                            <div class="d-flex">
                                <span>No&nbsp;WO:</span>
                                <input type="text" class="form-control form-select-sm" name="no">
                            </div>
                        </td>
                        <td colspan="2" style="width: 10px">
                            <div class="d-flex">
                                <span>Tgl:</span>
                                <input type="text" class="form-control form-select-sm" name="tgl_permintaan">
                            </div>
                        </td>
                        <?php if($kategoricat === "Stock") :?>
                        <td colspan="4">NO SPB :
                            <?php echo $no_spb+1; ?>/BPB/3S1/<?php echo $bulan_romawi; ?>/<?php echo $tahun_spb; ?></td>
                        <?php endif; ?>
                        <?php if($kategoricat === "Non Stock") :?>
                        <td colspan="4">NO SPB :
                            GSP/<?php  if ($no_spb > 0){echo $no_spb+1; } else {echo $no_spb = 1; }  ?>/<?php echo $bulan_spb; ?>/<?php echo $tahun_spb; ?>/NS</td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td colspan="6" rowspan="2">
                            Keperluan :
                            <textarea name="keperluan" rows="2" class="form-control"></textarea>
                        </td>
                        <td colspan="4" style="width: 10px">
                            <div class="d-flex">
                                <span>Nama&nbsp;Mesin: </span>
                                <input type="text" class="form-control form-select-sm" name="nama_mesin">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="width: 10px">
                            <div class="d-flex">
                                <span>Area: </span>
                                <input type="text" class="form-control form-select-sm" name="dikirim_ke">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 10px;text-align: center">No</td>
                        <td colspan="4">Uraian</td>
                        <td>P.BDG</td>
                        <td>Sat</td>
                        <td>JML</td>
                        <td>Ada Barang Bekas? & Jumlahnya </td>
                        <td colspan="2">Kode Lokasi Barang Bekas </td>
                    </tr>
                    <?php $no = 1; ?>
                    <?php while ($row = $barang->fetch_assoc()): ?>
                    <tr> 
                        <td class="text-center"><?php echo $no++ ?></td>
                        <td colspan="4"><?php echo $row["nama_barang"] ?></td>
                        <td></td>
                        <td style="width: 70px; margin: 0px">
                            <select style="margin: 0px" class="form-control" name="satuan[<?php echo $row['id'] ?>]">
                                <option value="pcs">Pcs</option>
                                <option value="pack">Pack</option>
                                <option value="ltr">Ltr</option>
                                <option value="unit">Unit</option>
                                <option value="kg">Kg</option>
                                <option value="PL">PL</option>
                                <option value="Mtr">Mtr</option>
                                <option value="Set">Set</option>
                                <option value="Btg">Btg</option>
                                <option value="Roll">Roll</option>
                            </select>
                        </td>
                        <td style="width: 70px; margin: 0px">
                            <input style="margin: 0px" type="number" class="form-control"
                                name="jumlah[<?php echo $row['id'] ?>]" max="<?php echo $row['jumlah']; ?>">
                        </td>
                        <?php $sqlkodelokasi = "SELECT kode_lokasi FROM mutasi_part_bekas WHERE Nama_Barang = '" . $row["nama_barang"] . "'"; 
                                $sql = $conn->query($sqlkodelokasi); 
                                if ($sql->num_rows > 0) {
                                $kodelokasibekas = $sql->fetch_assoc();
                                $kode_lokasi = $kodelokasibekas['kode_lokasi'];
                                }?>
                        <td>
                            <label for="Ada[]">Sudah Kembali</label>
                            <input style="align-items: center; align-self: center; align-content:center;"
                                class="form-check-input" name="Ada[]" id="Ada_<?php echo $row['id'] ?>" type="checkbox"
                                value="<?php echo $row['id'] ?>" onchange="toggleKodeLokasi(this)">
                            <input style="margin: 0px; width:50%; display: inline;" id="jumlah_bekas_<?php echo $row['id'] ?>" type="number" class="form-control"
                            name="jumlahbarangbekas[<?php echo $row['id'] ?>]" disabled>
                        </td>
                        <td colspan="2" style="width: 70px; margin: 0px">
                            <input style="margin: 0px" type="text" class="form-control"
                                name="kode_lokasi[<?php echo $row['id'] ?>]" value="<?php echo $kode_lokasi; ?>" id="kode_lokasi_<?php echo $row['id']?>"
                                disabled>
                        </td>

                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="10" style="padding-left: 30px;">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex flex-column text-center" style="width: 300px">
                                    <span class="fs-3 fw-bold text-uppercase mb-5">Yang Meminta :</span>
                                    <select id="teknisi" class="form-control" name="teknisi" onchange="updateNama()">
                                        <option value="packing">Packing</option>
                                        <option value="Mathand">Mathand</option>
                                        <option value="Electric">Electric</option>
                                        <option value="Utility">Utility</option>
                                        <option value="PdM">PdM</option>
                                        <option value="Gilmix">Gilmix</option>
                                        <option value="Goreng">Goreng</option>
                                        <option value="UPBM">UPBM</option>
                                        <option value="good_day">Good Day</option>
                                        <option value="SPV">SPV</option>
                                        <option value="other">Lainnya</option>
                                    </select>

                                    <select id="nama" class="form-control" name="nama_teknisi" style="width: auto">
                                        <!-- Options will be populated based on the selected teknisi -->
                                    </select>
                                </div>
                                <div class="d-flex flex-column text-center" style="width: 300px">
                                    <span class="fs-3 fw-bold text-uppercase mb-5">Atasan Langsung :</span>
                                    <span class="fs-3 fw-bold text-uppercase">
                                        <button style="border-radius: 5px" type="submit" class="btn-request">Submit
                                            SPB</button>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>

    <!-- Modal -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.js"></script>
    <script src="assets/js/flatpickr.min.js"></script>
    <script>
      // Fungsi untuk men-toggle disabled pada kode_lokasi berdasarkan status checkbox
      function toggleKodeLokasi(checkbox) {
        var itemId = checkbox.value;  // Mengambil nilai dari checkbox (ID barang)
        var kodeLokasiInput = document.getElementById('kode_lokasi_' + itemId); // Menargetkan input kode lokasi
        var JumlahBekas = document.getElementById('jumlah_bekas_' + itemId);
        if (checkbox.checked) {
            // Jika checkbox dicentang, aktifkan input kode_lokasi
            kodeLokasiInput.disabled = false;
            JumlahBekas.disabled = false;
        } else {
            // Jika checkbox tidak dicentang, nonaktifkan input kode_lokasi
            kodeLokasiInput.disabled = true;
            kodeLokasiInput.value = ''; // Mengosongkan input
            JumlahBekas.disabled = true;
            JumlahBekas.value = '';
        }
    }

    // Fungsi untuk validasi sebelum submit
    function validateForm() {
        var formIsValid = true;
        var checkboxes = document.querySelectorAll("input[name='Ada[]']:checked");
        checkboxes.forEach(function (checkbox) {
            var itemId = checkbox.value;
            var kodeLokasiInput = document.getElementById('kode_lokasi_' + itemId);
            var JumlahBekas = document.getElementById('jumlah_bekas_' + itemId);
            // Jika checkbox dicentang, pastikan kode_lokasi diisi
            if (kodeLokasiInput.disabled === false && JumlahBekas.disabled === false && JumlahBekas.value.trim() ===""  && kodeLokasiInput.value.trim() === "") {
                alert("Kode Lokasi dan jumlah barang bekas harus diisi jika barang bekas sudah kembali!");
                kodeLokasiInput.focus();
                formIsValid = false;
            }
        });
        return formIsValid;
    }
    $(document).ready(function() {
        $("input[name='tgl_permintaan']").flatpickr({
            dateFormat: "Y-m-d",
            enableTime: true,
            time_24hr: true,
            defaultDate: new Date()
        });
    });

    function updateNama() {
        const teknisi = document.getElementById('teknisi').value;
        const nama = document.getElementById('nama');

        const packingNames = [
            "Dana", "Dede R", "Dede I", "Deden", "Firman", "Gumaidi", "Iman",
            "Iyus", "Koes", "Nurudin", "Oban", "Ade", "Saud", "Syaifudin",
            "Tatang Somantri", "Tatang Sunarya", "Tayip", "Wanto", "Wiyardi", "Yunitri"
        ];
        const MathandNames = [
            "Dedi", "Rochman", "Wahyu"
        ];
        const GorengNames = [
            "Ajat", "Anwar", "Setyo"
        ];
        const UPBMNames = [
            "Hartono", "Joko"
        ];
        const GilmixNames = [
            "Rohim", "Acep", "Iwan"
        ];
        const UtilityNames = [
            "Indra", "Daryono", "Cudinto",
            "Dudung", "Fredi"
        ];
        const GoodDayNames = [
            "Yana", "Risdiyantoro", "Hendi", "La Ode"
        ];
        const ElectricNames = [
            "Fajar", "Wirdan", "Sodikin"
        ];
        const PdMNames = [
            "Gilang", "Henry", "Ikhsan", "Irga"
        ];
        const SPV = [
            "Aries", "Febri", "Erdi", "Andi", "Fahrizam", "Yomi"
        ];
        const Other = [
            "Produksi", "Warehouse", "Engineering", "QC", "Sefty", "Nani"
        ];

        // Clear current options
        nama.innerHTML = '';

        // Map teknisi to the corresponding names array
        const namesMap = {
            "packing": packingNames,
            "Mathand": MathandNames,
            "Electric": ElectricNames,
            "Utility": UtilityNames,
            "PdM": PdMNames,
            "Gilmix": GilmixNames,
            "Goreng": GorengNames,
            "UPBM": UPBMNames,
            "good_day": GoodDayNames,
            "other": Other,
            "SPV": SPV
        };

        // Get the appropriate names array
        const names = namesMap[teknisi] || [];

        // Populate the nama options
        names.forEach(name => {
            const option = document.createElement('option');
            option.value = name;
            option.text = name;
            nama.appendChild(option);
        });
    }

    // Validate jumlah before form submission
    document.getElementById('barangForm').addEventListener('submit', function(event) {
        const inputs = document.querySelectorAll('input[name^="jumlah"]');
        let valid = true;

        inputs.forEach(input => {
            const max = parseInt(input.max);
            const value = parseInt(input.value);
            if (value > max || value <= 0) {
                valid = false;
                alert('Jumlah tidak boleh lebih dari ' + max + ' dan tidak boleh kurang dari 1');
                input.focus();
            }
        });

        if (!valid) {
            event.preventDefault();
        }
    });

    // Initialize nama select based on the default teknisi value
    document.addEventListener('DOMContentLoaded', updateNama);
    </script>
</body>

</html>
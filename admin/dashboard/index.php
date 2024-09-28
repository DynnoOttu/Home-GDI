<?php include '../_header.php' ?>

<?php
$get1 = mysqli_query($con, "SELECT * FROM jemaat");
$count1 = mysqli_num_rows($get1);

$get2 = mysqli_query($con, "SELECT * FROM majelis");
$count2 = mysqli_num_rows($get2);

$get3 = mysqli_query($con, "SELECT * FROM kepala_keluarga");
$jumlahKk = mysqli_num_rows($get3);

$dataRayon = mysqli_query($con, "SELECT * FROM rayon");
$jumlahRayon = mysqli_num_rows($dataRayon);

$months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
$jumlahJemaatPerBulan = [];

for ($bulan = 1; $bulan <= 12; $bulan++) {
    $query = mysqli_query($con, "SELECT COUNT(*) as count FROM status_sosial_jemaat WHERE MONTH(meninggal_at) = '$bulan' AND meninggal_at IS NOT NULL");
    $row = $query->fetch_assoc();
    $jumlahJemaatPerBulan[] = $row['count'] ?? 0;
}

// Menghitung jumlah jemaat total
$queryTotalJemaat = mysqli_query($con, "SELECT COUNT(*) as count FROM jemaat");
$rowTotalJemaat = $queryTotalJemaat->fetch_assoc();
$jumlahJemaatTotal = $rowTotalJemaat['count'] ?? 0;

// Menghitung jumlah jemaat berdasarkan jenis kelamin
$queryJemaatGender = mysqli_query($con, "SELECT jenis_kelamin, COUNT(*) as count FROM jemaat GROUP BY jenis_kelamin");
$jumlahJemaatByGender = [];
while ($rowJemaatGender = $queryJemaatGender->fetch_assoc()) {
    $jumlahJemaatByGender[$rowJemaatGender['jenis_kelamin']] = $rowJemaatGender['count'];
}

// Menginisiasi data untuk grafik jemaat
$chartJemaatLabels = ['Total Jemaat', 'Laki-Laki', 'Perempuan'];
$chartJemaatDataValues = [
    $jumlahJemaatTotal,
    $jumlahJemaatByGender['Laki-Laki'] ?? 0,
    $jumlahJemaatByGender['Perempuan'] ?? 0
];

// Query untuk menghitung jumlah status baptis
$queryBaptis = mysqli_query($con, "SELECT status_baptis, COUNT(*) as count FROM status_sosial_jemaat GROUP BY status_baptis");
$jumlahStatusBaptis = [];
while ($rowBaptis = $queryBaptis->fetch_assoc()) {
    $jumlahStatusBaptis[$rowBaptis['status_baptis']] = $rowBaptis['count'];
}

// Query untuk menghitung jumlah status sidi
$querySidi = mysqli_query($con, "SELECT status_sidi, COUNT(*) as count FROM status_sosial_jemaat GROUP BY status_sidi");
$jumlahStatusSidi = [];
while ($rowSidi = $querySidi->fetch_assoc()) {
    $jumlahStatusSidi[$rowSidi['status_sidi']] = $rowSidi['count'];
}

// Query untuk menghitung jumlah status pernikahan
$queryPernikahan = mysqli_query($con, "SELECT status_pernikahan, COUNT(*) as count FROM status_sosial_jemaat GROUP BY status_pernikahan");
$jumlahStatusPernikahan = [];
while ($rowPernikahan = $queryPernikahan->fetch_assoc()) {
    $jumlahStatusPernikahan[$rowPernikahan['status_pernikahan']] = $rowPernikahan['count'];
}

// Inisiasi data untuk grafik
$labelsBaptis = ['Sudah', 'Belum'];
$dataValuesBaptis = [
    $jumlahStatusBaptis['Sudah'] ?? 0,
    $jumlahStatusBaptis['Belum'] ?? 0
];

$labelsSidi = ['Sudah', 'Belum'];
$dataValuesSidi = [
    $jumlahStatusSidi['Sudah'] ?? 0,
    $jumlahStatusSidi['Belum'] ?? 0
];

$labelsPernikahan = ['Kawin', 'Belum Kawin', 'Cerai Hidup', 'Cerai Mati'];
$dataValuesPernikahan = [
    $jumlahStatusPernikahan['Kawin'] ?? 0,
    $jumlahStatusPernikahan['Belum Kawin'] ?? 0,
    $jumlahStatusPernikahan['Cerai Hidup'] ?? 0,
    $jumlahStatusPernikahan['Cerai Mati'] ?? 0
];

?>

<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Sales Cards  -->
    <!-- ============================================================== -->
    <div class="row">
        <!-- Column -->
        <div class="col-md-6 col-lg-4 col-xlg-3">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                    <h6 class="text-white">Jumlah Jemaat: <?= $count1 ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xlg-3">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                    <h6 class="text-white">Jumlah Majelis: <?= $count2 ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xlg-3">
            <div class="card card-hover">
                <div class="box bg-warning text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                    <h6 class="text-white">Jumlah Rayon: <?= $jumlahRayon ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xlg-3">
            <div class="card card-hover">
                <div class="box bg-primary text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                    <h6 class="text-white">Jumlah Kepala Keluarga: <?= $jumlahKk ?></h6>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <div class="row mt-5">
        <div class="col-md-4">
            <h3 class="mb-2 text-center">Jumlah jemaat meninggal per bulan</h3>
            <canvas id="myChart" height="30vh" width="50vw"></canvas>
        </div>

        <div class="col-md-4">
            <h3 class="mb-2 text-center">Jumlah jemaat berdasarkan jenis kelamin</h3>
            <canvas id="jemaatChart" height="30vh" width="50vw"></canvas>
        </div>

        <div class="col-md-4">
            <h3 class="mb-2 text-center">Status Baptis</h3>
            <canvas id="baptisChart" height="30vh" width="80vw"></canvas>
        </div>
    </div>



    <div class="row mt-5">
        <div class="col-md-4">
            <h3 class="mb-2 text-center">Status Sidi</h3>
            <canvas id="sidiChart" height="30vh" width="80vw"></canvas>
        </div>

        <div class="col-md-4">
            <h3 class="mb-2 text-center">Status Pernikahan</h3>
            <canvas id="pernikahanChart" height="30vh" width="80vw"></canvas>
        </div>
    </div>
</div>

<?php include '../_footer.php' ?>

<script>
    // Chart Jumlah Jemaat Meninggal per Bulan
    const bulanLabels = <?php echo json_encode($months); ?>;
    const jemaatMeninggalData = <?php echo json_encode($jumlahJemaatPerBulan); ?>;

    const meninggalData = {
        labels: bulanLabels,
        datasets: [{
            label: 'Jumlah Jemaat Meninggal Berdasarkan Bulan',
            data: jemaatMeninggalData,
            backgroundColor: 'rgb(54, 162, 235)',
            borderColor: 'rgb(54, 162, 235)',
            fill: false,
            tension: 0.1
        }]
    };

    const meninggalConfig = {
        type: 'pie',
        data: meninggalData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    };

    const myMeninggalChart = new Chart(
        document.getElementById('myChart'),
        meninggalConfig
    );

    // Chart Jumlah Jemaat Berdasarkan Jenis Kelamin
    const jemaatLabels = <?php echo json_encode($chartJemaatLabels); ?>;
    const jemaatData = <?php echo json_encode($chartJemaatDataValues); ?>;

    const jemaatChartData = {
        labels: jemaatLabels,
        datasets: [{
            label: 'Jumlah Jemaat Berdasarkan Jenis Kelamin',
            data: jemaatData,
            backgroundColor: ['rgb(54, 162, 235)', 'rgb(75, 192, 192)', 'rgb(255, 99, 132)'],
            borderColor: ['rgb(54, 162, 235)', 'rgb(75, 192, 192)', 'rgb(255, 99, 132)'],
            fill: false,
            tension: 0.1
        }]
    };

    const jemaatChartConfig = {
        type: 'pie',
        data: jemaatChartData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    };

    const myJemaatChart = new Chart(
        document.getElementById('jemaatChart'),
        jemaatChartConfig
    );

    // Baptis Chart
    const baptisLabels = <?php echo json_encode($labelsBaptis); ?>;
    const baptisDataValues = <?php echo json_encode($dataValuesBaptis); ?>;
    const baptisData = {
        labels: baptisLabels,
        datasets: [{
            label: 'Jumlah Jemaat Berdasarkan Status Baptis',
            data: baptisDataValues,
            backgroundColor: ['rgb(54, 162, 235)', 'rgb(255, 99, 132)'],
            borderColor: ['rgb(54, 162, 235)', 'rgb(255, 99, 132)'],
            fill: false,
            tension: 0.1
        }]
    };
    const baptisChartConfig = {
        type: 'pie',
        data: baptisData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    };
    const baptisChart = new Chart(document.getElementById('baptisChart'), baptisChartConfig);

    // Sidi Chart
    const sidiLabels = <?php echo json_encode($labelsSidi); ?>;
    const sidiDataValues = <?php echo json_encode($dataValuesSidi); ?>;
    const sidiData = {
        labels: sidiLabels,
        datasets: [{
            label: 'Jumlah Jemaat Berdasarkan Status Sidi',
            data: sidiDataValues,
            backgroundColor: ['rgb(54, 162, 235)', 'rgb(255, 99, 132)'],
            borderColor: ['rgb(54, 162, 235)', 'rgb(255, 99, 132)'],
            fill: false,
            tension: 0.1
        }]
    };
    const sidiChartConfig = {
        type: 'pie',
        data: sidiData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    };
    const sidiChart = new Chart(document.getElementById('sidiChart'), sidiChartConfig);

    // Pernikahan Chart
    const pernikahanLabels = <?php echo json_encode($labelsPernikahan); ?>;
    const pernikahanDataValues = <?php echo json_encode($dataValuesPernikahan); ?>;
    const pernikahanData = {
        labels: pernikahanLabels,
        datasets: [{
            label: 'Jumlah Jemaat Berdasarkan Status Pernikahan',
            data: pernikahanDataValues,
            backgroundColor: ['rgb(54, 162, 235)', 'rgb(75, 192, 192)', 'rgb(255, 99, 132)', 'rgb(255, 206, 86)'],
            borderColor: ['rgb(54, 162, 235)', 'rgb(75, 192, 192)', 'rgb(255, 99, 132)', 'rgb(255, 206, 86)'],
            fill: false,
            tension: 0.1
        }]
    };
    const pernikahanChartConfig = {
        type: 'pie',
        data: pernikahanData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    };
    const pernikahanChart = new Chart(document.getElementById('pernikahanChart'), pernikahanChartConfig);
</script>
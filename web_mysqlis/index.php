<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Robot Chạy Line</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('/img/robot.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 30px;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        h1 {
            text-align: center;
            color: #333;
            animation: glow 1s infinite alternate;
        }
        @keyframes glow {
            from { text-shadow: 0 0 5px #fff, 0 0 10px #ff9c00; }
            to { text-shadow: 0 0 20px #ff9c00, 0 0 30px #ff9c00; }
        }
        .info-box {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .info-item {
            text-align: center;
            padding: 10px;
            width: 30%;
            border-radius: 10px;
            background: linear-gradient(135deg, #ff9c00, #ff6f00);
            color: #fff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            transform: scale(1);
            transition: transform 0.3s ease;
        }
        .info-item:hover {
            transform: scale(1.05);
        }
        .info-item h2 {
            font-size: 18px;
            color: #fff;
        }
        .info-item p {
            font-size: 24px;
        }
        .robot-img {
            width: 100px;
            margin: 10px auto;
        }
        .button-group {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            margin: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
            transform: translateY(-3px);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #ff9c00;
            color: #fff;
        }
        .chart-container {
            margin-top: 30px;
            width: 100%;
            max-width: 600px;
        }
        .loading-spinner {
            display: none;
            margin: 0 auto;
            margin-bottom: 20px;
        }
        .loading-spinner img {
            width: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Thông Tin Robot Chạy Line</h1>

    <div class="robot-img">
        <img src="/img/robots.jpg" alt="Robot Chạy Line" style="width:100px;">
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <img src="/images/loading.gif" alt="Loading...">
    </div>

    <div class="info-box">
        <div class="info-item">
            <h2>Quãng Đường</h2>
            <p id="distance">Đang tải...</p>
        </div>
        <div class="info-item">
            <h2>Năng Lượng Tiêu Thụ</h2>
            <p id="energy">Đang tải...</p>
        </div>
        <div class="info-item">
            <h2>Hiệu Suất</h2>
            <p id="efficiency">Đang tải...</p>
        </div>
    </div>

    <div class="button-group">
        <button class="btn" onclick="fetchRobotData()">Lấy Dữ Liệu</button>
        <button class="btn" onclick="exportCSV()">Xuất Báo Cáo CSV</button>
        <button class="btn" onclick="resetData()">Đặt Lại Dữ Liệu</button>
    </div>

    <div class="chart-container">
        <canvas id="robotChart"></canvas>
    </div>

    <h2>Lịch Sử Hoạt Động</h2>
    <table border="1" id="historyTable">
        <tr>
            <th>Thời gian</th>
            <th>Quãng đường (m)</th>
            <th>Năng lượng tiêu thụ (J)</th>
        </tr>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let chartData = {
        labels: [],
        datasets: [
            {
                label: 'Quãng đường (m)',
                data: [],
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Năng lượng tiêu thụ (J)',
                data: [],
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                tension: 0.4
            }
        ]
    };

    let ctx = document.getElementById('robotChart').getContext('2d');
    let robotChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Thời gian',
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Giá trị',
                    }
                }
            }
        }
    });

    function fetchRobotData() {
        document.getElementById('loadingSpinner').style.display = 'block';
        fetch('get_robot_data.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingSpinner').style.display = 'none';
                let currentTime = new Date().toLocaleTimeString();

                document.getElementById('distance').innerText = data.distance + ' m';
                document.getElementById('energy').innerText = data.energy + ' J';
                let efficiency = (data.distance / data.energy).toFixed(2);
                document.getElementById('efficiency').innerText = efficiency + ' m/J';

                chartData.labels.push(currentTime);
                chartData.datasets[0].data.push(data.distance);
                chartData.datasets[1].data.push(data.energy);

                robotChart.update();

                let table = document.getElementById('historyTable');
                let newRow = table.insertRow();
                newRow.insertCell(0).innerText = currentTime;
                newRow.insertCell(1).innerText = data.distance + ' m';
                newRow.insertCell(2).innerText = data.energy + ' J';
            })
            .catch(error => {
                console.error('Lỗi khi lấy dữ liệu:', error);
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('distance').innerText = 'Không thể tải';
                document.getElementById('energy').innerText = 'Không thể tải';
                document.getElementById('efficiency').innerText = 'Không thể tính toán';
            });
    }

    function exportCSV() {
        let csvContent = "\uFEFFThời gian,Quãng đường (m),Năng lượng tiêu thụ (J)\n";
        let rows = document.querySelectorAll('#historyTable tr');
        rows.forEach((row, index) => {
            if (index > 0) {
                let cols = row.querySelectorAll('td');
                let csvRow = [];
                cols.forEach(col => csvRow.push(col.innerText));
                csvContent += csvRow.join(",") + "\n";
            }
        });
        let encodedUri = encodeURI("data:text/csv;charset=utf-8," + csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "robot_data.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function resetData() {
        chartData.labels = [];
        chartData.datasets.forEach(dataset => dataset.data = []);
        robotChart.update();

        let table = document.getElementById('historyTable');
        let rowCount = table.rows.length;
        for (let i = rowCount - 1; i > 0; i--) {
            table.deleteRow(i);
        }
        document.getElementById('distance').innerText = 'Đang tải...';
        document.getElementById('energy').innerText = 'Đang tải...';
        document.getElementById('efficiency').innerText = 'Đang tải...';
    }
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>儀表板</title>
    <link rel="stylesheet" href="/resources/css/reset.css">
    <link rel="stylesheet" href="/resources/css/style.css">
    <style>
        .container {
            margin: 45px 15px 0;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .container .card {
            background-color: #fcedd5;
            width: 28%;
            height: 285px;
            border-radius: 20px;
            padding: 15px;

        }

        .container .today-status {
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
        }

        .container .card:nth-of-type(1) {
            background-color: #D9D9D9;
        }

        .container .card:nth-of-type(2) {
            background-color: #cdebed;
        }

        .container .card:nth-of-type(3) {
            background-color: #fde808;

        }

        .container .today-status div {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .container .today-status div :nth-child(1) {
            margin-right: 15px;
            margin-bottom: 15px;
        }

        .today-status h2,
        .today-status h3 {
            margin: 10px 0;
        }

        .today-status button {
            text-decoration: none;
            background-color: #F2EFE7;
            color: #006A71;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            border: 0px solid #006A71;
            cursor: pointer;
        }
        .today-status button:hover{
            background-color: #006A71;
            color: #F2EFE7;
        }
    </style>
</head>

<body>
    <nav class="toolbar">
        <h3>XX公司後台系統</h3>
        <ul>
            <li><a href="/">首頁</a></li>
            <li>個人資料</li>
            <li><a href="/check-record">打卡紀錄</a></li>
            <li>請假申請</li>
        </ul>
    </nav>
    <main class="main-container">
        <header>
            <h1><?php echo $_SESSION['account']; ?></h1>
            <label id="date"></label>
        </header>
        <section class="container">
            <div class="today-status card">
                <h3>今日打卡狀態：<span>未開放</span></h3>
                <img src="/resources/img/circle-xmark-regular.svg" style="width:100%;max-width:80px;height:auto;" alt="">
                <h3>現在時間：<span id="time"></span></h3>
                <div>
                    <button id="check-in">上班打卡</button>
                    <button id="check-out">下班打卡</button>
                </div>
            </div>
            <div class="today-status card">
                <h3>今日打卡狀態：<span>正常</span></h3>
                <img src="/resources/img/face-smile-wink-regular.svg" width="100px" height="100px" alt="">
                <h3>現在時間:10:15:36</h3>
                <div>
                    <button id="check-in">上班打卡</button>
                    <button id="check-out">下班打卡</button>
                </div>
            </div>
            <div class="today-status card">
                <h3>今日打卡狀態：<span>異常</span></h3>
                <img src="/resources/img/triangle-exclamation-solid.svg" width="100px" height="100px" alt="">
                <h3>現在時間:10:15:36</h3>
                <div>
                    <button id="check-in">上班打卡</button>
                    <button id="check-out">下班打卡</button>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
<script>
    // 取得當前時間
    function updateTime() {
        const now = new Date();
        const hour = String(now.getHours()).padStart(2, 0);
        const minute = String(now.getMinutes()).padStart(2, 0);
        const second = String(now.getSeconds()).padStart(2, 0);
        const timeString = `${hour}:${minute}:${second}`;
        document.getElementById('time').textContent = timeString;
    }
    document.addEventListener('DOMContentLoaded', function() {
        //剛進頁面先執行一次，後續每秒更新一次時間。
        updateTime();
        setInterval(updateTime, 1000);
        // 取得上班按鈕
        checkInButton = document.getElementById('check-in');
        // 取得下班按鈕
        checkOutButton = document.getElementById('check-out');
        // 監聽上班按鈕是否被點擊
        checkInButton.addEventListener('click', async function() {
            // 上班API
            const response = await fetch('/api/check-in', {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'check-in'
                    })
                })
                const data = await response.json();
                return alert(data.message);
        })
        // 監聽下班按鈕是否被點擊
        checkOutButton.addEventListener('click', async function() {
            // 下班API
            const response = await fetch('/api/check-out', {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'check-out'
                    })
                })
                data = await response.json();
                return alert(data.message);
        })
    })
</script>
<script src="/resources/js/init.js"></script>
<!--  
今日狀態卡片顯示
狀態|顏色|使用場景
關閉/灰色/未開放打卡時段
正常/天空藍/上班時段且當日已打卡 (上下班打卡正常)
異常/黃色/遲到，早退，未打卡
-->
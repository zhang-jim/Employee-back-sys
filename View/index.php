<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>員工打卡系統</title>
    <style>
        body {
            font-family: "Microsoft JhengHei", sans-serif;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 2em;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 1em;
        }

        .actions a,
        .actions button {
            display: inline-block;
            margin: 0.5em;
            padding: 0.75em 1.5em;
            font-size: 1em;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .actions a:hover,
        .actions button:hover {
            background-color: #0056b3;
        }

        .user-info {
            margin-bottom: 1em;
            font-weight: bold;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>歡迎使用員工打卡系統</h1>

        <?php
        session_start();
        if (isset($_SESSION['account'])) {
            echo "<div class='user-info'>目前登入：{$_SESSION['account']}</div>";
        }
        ?>

        <div class="actions">
            <?php if (isset($_SESSION['account'])): ?>
                <a href="/clock-in">我要打卡</a>
                <button id="logoutBtn">登出</button>
            <?php else: ?>
                <a href="/login">登入</a>
                <a href="/register">註冊</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const logoutBtn = document.getElementById("logoutBtn");
        if (logoutBtn) {
            logoutBtn.addEventListener("click", async () => {
                if (!confirm("確定要登出嗎？")) return;

                try {
                    const res = await fetch("/logout", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.location.href = "/login";
                    } else {
                        alert("登出失敗：" + data.message);
                    }
                } catch (err) {
                    alert("無法連線伺服器，登出失敗");
                    console.error(err);
                }
            });
        }
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>員工打卡系統-入口</title>
    <link rel="stylesheet" href="/resources/css/reset.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        h1 {
            font-size: 3.5rem;
            color: #006A71;
            text-align: center;
        }

        .bar {
            text-align: right;
            position: absolute;
            top: 34px;
            right: 34px;
        }

        .bar a {
            background-color: #9ACBD0;
            color: #006A71;
        }

        .bar :nth-child(1) {
            margin-right: 20px;
        }
    </style>
</head>

<body>
    <h1>XX公司後台系統</h1>
    <div class="bar">
        <a href="/login">登入</a>
        <a href="/register">註冊</a>
    </div>
</body>

</html>
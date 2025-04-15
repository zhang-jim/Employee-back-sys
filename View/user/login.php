<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>登入頁面</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f5f5f5;
            max-width: 400px;
            margin: 5em auto;
            padding: 2em;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 1em;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 1em;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.5em;
            font-size: 1em;
            margin-top: 0.2em;
        }

        input[type="submit"] {
            padding: 0.7em;
            font-size: 1em;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .register-link {
            text-align: center;
            margin-top: 1em;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 1em;
        }
    </style>
</head>

<body>
    <h1>登入</h1>
    <form id="loginForm">
        <label>
            帳號：
            <input type="text" name="account" required />
        </label>
        <label>
            密碼：
            <input type="password" name="password" required />
        </label>
        <input type="submit" value="登入" />
    </form>
    <div class="register-link">
        還沒有帳號？<a href="/register">註冊</a>
    </div>
    <div class="error" id="errorMessage"></div>

    <script>
        const form = document.getElementById("loginForm");
        const errorBox = document.getElementById("errorMessage");

        form.addEventListener("submit", async function(e) {
            e.preventDefault();
            errorBox.textContent = "";

            const account = form.account.value.trim();
            const password = form.password.value.trim();

            if (!account || !password) {
                errorBox.textContent = "請輸入帳號與密碼";
                return;
            }

            try {
                const res = await fetch("/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        account,
                        password
                    })
                });

                const data = await res.json();

                if (data.success) {
                    alert(data.message || "登入成功！");
                    window.location.href = "/"; // ✅ 登入後導向首頁
                } else {
                    errorBox.textContent = data.message || "登入失敗，請再試一次。";
                }
            } catch (err) {
                errorBox.textContent = "伺服器錯誤，請稍後再試。";
                console.error(err);
            }
        });
    </script>
</body>

</html>
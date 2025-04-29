<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>登入頁面</title>
    <link rel="stylesheet" href="/resources/css/reset.css">
    <link rel="stylesheet" href="/resources/css/form.css">
</head>

<body>
    <main>
        <h1>登入</h1>
        <form id="loginForm">
            <label>
                帳號：
                <input type="text" name="account" id="account"
                    placeholder="請輸入帳號" required />
                <div id="accountTip" class="tip"></div>
            </label>
            <label>
                密碼：
                <input type="password" name="password" id="password"
                    required placeholder="請輸入密碼" />
                <div id="passwordTip" class="tip"></div>
            </label>
            <input type="submit" value="登入" />
        </form>
        <div class="link">
            還沒有帳號？<a href="/register">註冊</a>
        </div>
        <div id="messageBox" class="message"></div>
    </main>
</body>

</html>
<script src="/resources/js/validator.js"></script>
<script >
    const form = document.getElementById('loginForm');
    const messageBox = document.getElementById('messageBox');

    const account = document.getElementById('account');
    const accountTip = document.getElementById('accountTip');

    const password = document.getElementById('password');
    const passwordTip = document.getElementById('passwordTip');

    let accountVaild = false;
    let passwordVaild = false;

    // 帳號即時驗證
    account.addEventListener('input', function() {
        const result = Validator.validateAccount(account.value.trim());
        if (result.valid) {
            accountTip.textContent = result.message;
            accountTip.style.color = 'green';
            accountVaild = true;
        } else {
            accountTip.textContent = result.message;
            accountTip.style.color = 'red';
            accountTip.classList.add('show');
            accountVaild = false;
        }
    });
    // 密碼即時驗證
    password.addEventListener('input', function() {
        const result = Validator.validatePassword(password.value.trim());
        if (result.valid) {
            passwordTip.textContent = result.message;
            passwordTip.style.color = 'green';
            passwordVaild = true;
        } else {
            passwordTip.textContent = result.message;
            passwordTip.style.color = 'red';
            passwordTip.classList.add('show');
            passwordVaild = false;
        }
    });
    // 提交表單
    form.addEventListener('submit', async function(event) {
        event.preventDefault(); //終止表單預設GET MEthod.
        const account = form.account.value;
        const password = form.password.value;

        if (accountVaild && passwordVaild) {
            const response = await fetch('/api/login', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    account,
                    password
                })
            })
            const data = await response.json()

            if (data.success) {
                messageBox.textContent = data.message + "即將跳轉";
                messageBox.style.color = 'green';
                setTimeout(() => {
                    window.location.href = "/dashboard"
                }, 1500);
            } else {
                messageBox.textContent = data.message;
            }
        }
    })
</script>
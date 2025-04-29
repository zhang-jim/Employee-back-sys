<!DOCTYPE html>
<html lang="zh-Hant">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/resources/css/reset.css">
  <link rel="stylesheet" href="/resources/css/form.css">
  <title>註冊頁面</title>
  <style>
    input[type="submit"] {
      background-color: #007bff;
    }

    input[type="submit"]:hover {
      background-color: #0056b3;
    }

    .message {
      text-align: center;
      margin-top: 1em;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <main>
    <h1>註冊</h1>
    <form id="registerForm">
      <label>
        暱稱：
        <input type="text" name="nickname" id="nickname"
          required placeholder="請輸入暱稱" />
        <div id="nicknameTip" class="tip"></div>
      </label>
      <label>
        手機號碼：
        <input type="text" name="phonenumber" id="phonenumber"
          required placeholder="請輸入手機號碼" />
        <div id="phonenumberTip" class="tip"></div>
      </label>
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
      <input type="submit" value="註冊" />
    </form>
    <div class="link">
      已有帳號？<a href="/login">登入</a>
    </div>
    <div class="message" id="messageBox"></div>
  </main>
</body>

</html>
<script src="/resources/js/validator.js"></script>
<script>
  const form = document.getElementById("registerForm");
  const messageBox = document.getElementById("messageBox");

  const account = document.getElementById('account');
  const accountTip = document.getElementById('accountTip');

  const password = document.getElementById('password');
  const passwordTip = document.getElementById('passwordTip');

  const nickname = document.getElementById('nickname');
  const nicknameTip = document.getElementById('nicknameTip');

  const phonenumber = document.getElementById('phonenumber');
  const phonenumberTip = document.getElementById('phonenumberTip');

  let accountVaild = false;
  let passwordVaild = false;
  let nicknameVaild = false;
  let phonenumberVaild = false;

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

  // 暱稱即時驗證 => 不得有特殊符號
  nickname.addEventListener('input', function() {
    const result = Validator.validateNickname(nickname.value.trim());
    if (result.valid) {
      nicknameTip.textContent = result.message;
      nicknameTip.style.color = 'green';
      nicknameVaild = true;
    } else {
      nicknameTip.textContent = result.message;
      nicknameTip.style.color = 'red';
      nicknameTip.classList.add('show');
      nicknameVaild = false;
    }
  })
  // 手機號碼即時驗證 
  phonenumber.addEventListener('input', function() {
    const result = Validator.validatePhonenumber(phonenumber.value.trim());
    if (result.valid) {
      phonenumberTip.textContent = result.message;
      phonenumberTip.style.color = 'green';
      phonenumberVaild = true;
    } else {
      phonenumberTip.textContent = result.message;
      phonenumberTip.style.color = 'red';
      phonenumberTip.classList.add('show');
      phonenumberVaild = false;
    }
  })

  // 提交表單
  form.addEventListener('submit', async function(event) {
    event.preventDefault(); //終止表單預設GET MEthod.
    const account = form.account.value;
    const password = form.password.value;
    const phonenumber = form.phonenumber.value;
    const nickname = form.nickname.value;

    if (accountVaild && passwordVaild && nicknameVaild && phonenumberVaild) {
      const response = await fetch('/api/register', {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          account,
          password,
          phonenumber,
          nickname
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
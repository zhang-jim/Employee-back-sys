<!DOCTYPE html>
<html lang="zh-Hant">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>註冊頁面</title>
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
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #0056b3;
    }

    .login-link {
      text-align: center;
      margin-top: 1em;
    }

    .login-link a {
      color: #007bff;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    .message {
      text-align: center;
      margin-top: 1em;
      font-weight: bold;
    }

    .error {
      color: red;
    }

    .success {
      color: green;
    }
  </style>
</head>

<body>
  <h1>註冊</h1>
  <form id="registerForm">
    <label>
      帳號：
      <input type="text" name="account" required />
    </label>
    <label>
      密碼：
      <input type="password" name="password" required />
    </label>
    <input type="submit" value="註冊" />
  </form>
  <div class="login-link">
    已有帳號？<a href="/login">登入</a>
  </div>
  <div class="message" id="messageBox"></div>

  <script>
    const form = document.getElementById("registerForm");
    const messageBox = document.getElementById("messageBox");

    form.addEventListener("submit", async function (e) {
      e.preventDefault();
      messageBox.textContent = "";
      messageBox.className = "message";

      const account = form.account.value.trim();
      const password = form.password.value.trim();

      if (!account || !password) {
        messageBox.textContent = "請輸入帳號與密碼";
        messageBox.classList.add("error");
        return;
      }

      try {
        const res = await fetch("/register", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ account, password })
        });

        const data = await res.json();

        if (data.success) {
          messageBox.textContent = data.message || "註冊成功！";
          messageBox.classList.add("success");
          form.reset();
          setTimeout(() => {
            window.location.href = "/login";
          }, 1500);
        } else {
          messageBox.textContent = data.message || "註冊失敗";
          messageBox.classList.add("error");
        }
      } catch (err) {
        messageBox.textContent = "伺服器錯誤，請稍後再試";
        messageBox.classList.add("error");
        console.error(err);
      }
    });
  </script>
</body>

</html>

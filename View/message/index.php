<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>留言板</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f4f4f4;
            max-width: 700px;
            margin: 3em auto;
            padding: 2em;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 1em;
        }

        .auth {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1em;
            font-size: 0.9em;
        }

        textarea {
            width: 100%;
            height: 80px;
            padding: 0.5em;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        form {
            margin-bottom: 1.5em;
        }

        input[type="submit"] {
            margin-top: 0.5em;
            padding: 0.5em 1.2em;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: #fdfdfd;
            margin-bottom: 1em;
            padding: 1em;
            border-radius: 5px;
            border: 1px solid #ddd;
            position: relative;
        }

        .meta {
            font-size: 0.85em;
            color: #666;
            margin-bottom: 0.5em;
        }

        .content {
            margin-bottom: 0.5em;
        }

        .actions {
            position: absolute;
            top: 1em;
            right: 1em;
        }

        .actions button {
            background-color: transparent;
            border: none;
            color: #007bff;
            cursor: pointer;
            margin-left: 0.5em;
        }

        .actions button:hover {
            text-decoration: underline;
        }

        .message-box {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1em;
        }

        .message-box.success {
            color: green;
        }

        .message-box.error {
            color: red;
        }
    </style>
</head>

<body>
    <h1>留言板</h1>

    <div class="auth">
        <?php
        session_start();
        if (isset($_SESSION['account'])) {
            echo "使用者：" . htmlspecialchars($_SESSION['account']) . " <button id='logoutBtn'>登出</button>";
        } else {
            echo "<a href='/login'>登入</a>";
        }
        ?>
    </div>

    <div class="message-box" id="messageBox"></div>

    <form id="messageForm">
        <label>
            留言內容：
            <textarea name="message" maxlength="100" required></textarea>
        </label>
        <input type="submit" value="送出">
    </form>

    <ul id="messageList"></ul>

    <script>
        const CURRENT_USER_ACCOUNT = <?= isset($_SESSION['account']) ? json_encode($_SESSION['account']) : 'null' ?>;
        const form = document.getElementById("messageForm");
        const messageList = document.getElementById("messageList");
        const messageBox = document.getElementById("messageBox");

        form.addEventListener("submit", async function(e) {
            e.preventDefault();
            const message = form.message.value.trim();
            messageBox.textContent = "";
            messageBox.className = "message-box";

            if (!message) {
                messageBox.textContent = "請輸入留言內容";
                messageBox.classList.add("error");
                return;
            }

            try {
                const res = await fetch("/messages/create", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        message
                    })
                });

                const data = await res.json();
                messageBox.textContent = data.message;

                if (data.success) {
                    messageBox.classList.add("success");
                    form.reset();
                    loadMessages();
                } else {
                    messageBox.classList.add("error");
                }
            } catch (err) {
                messageBox.textContent = "留言失敗，請稍後再試";
                messageBox.classList.add("error");
            }
        });

        async function loadMessages() {
            try {
                const res = await fetch("/messages");
                const data = await res.json();

                if (data.success) {
                    messageList.innerHTML = "";
                    data.message.forEach(msg => {
                        const li = document.createElement("li");

                        li.innerHTML = `
              <div class="meta">[${msg.created_at}] ${escapeHtml(msg.account)}</div>
              <div class="content">${escapeHtml(msg.content)}</div>
            `;

                        // 若為本人，顯示編輯刪除
                        if (msg.account === CURRENT_USER_ACCOUNT) {
                            const actions = document.createElement("div");
                            actions.className = "actions";

                            const editBtn = document.createElement("button");
                            editBtn.textContent = "編輯";
                            editBtn.addEventListener("click", () => editMessage(msg.id, msg.content));

                            const deleteBtn = document.createElement("button");
                            deleteBtn.textContent = "刪除";
                            deleteBtn.addEventListener("click", () => deleteMessage(msg.id));

                            actions.appendChild(editBtn);
                            actions.appendChild(deleteBtn);
                            li.appendChild(actions);
                        }

                        messageList.appendChild(li);
                    });
                } else {
                    messageList.innerHTML = "<li>載入留言失敗。</li>";
                }
            } catch (err) {
                console.error("無法載入留言：", err);
                messageList.innerHTML = "<li>無法連接伺服器。</li>";
            }
        }

        async function deleteMessage(messageId) {
            if (!confirm("確定要刪除這則留言嗎？")) return;

            try {
                const res = await fetch("/messages/" + messageId, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        message_id: messageId
                    })
                });

                const data = await res.json();
                alert(data.message);
                if (data.success) {
                    loadMessages();
                }
            } catch (err) {
                alert("刪除留言失敗！");
            }
        }

        async function editMessage(messageId, oldContent) {
            const newContent = prompt("請輸入新的留言內容：", oldContent);
            if (newContent === null || newContent.trim() === "") return;

            try {
                const res = await fetch("/messages/" + messageId, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        new_message: newContent.trim()
                    })
                });

                const data = await res.json();
                alert(data.message);
                if (data.success) {
                    loadMessages();
                }
            } catch (err) {
                alert("編輯留言失敗！");
            }
        }

        function escapeHtml(text) {
            const map = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                "\"": "&quot;",
                "'": "&#39;"
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        loadMessages();
    </script>
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
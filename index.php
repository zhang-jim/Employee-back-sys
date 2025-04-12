<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>留言板</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 600px;
            margin: 2em auto;
        }

        textarea {
            width: 100%;
            height: 80px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 1em;
            border-bottom: 1px solid #ccc;
            padding-bottom: 0.5em;
        }

        .auth {
            margin-bottom: 1em;
        }
    </style>
</head>

<body>
    <h1>留言板</h1>

    <div class="auth">
        <?php
        session_start();
        if (isset($_SESSION['account'])) {
            echo "使用者：" . htmlspecialchars($_SESSION['account']) . " <a href='Auth/logout.php'>登出</a>";
        } else {
            echo "<a href='Auth/login.php'>登入</a>";
        }
        ?>
    </div>

    <form id="messageForm">
        <label>
            留言內容：
            <textarea name="message" maxlength="100" required></textarea>
        </label>
        <br>
        <input type="submit" value="送出">
    </form>

    <ul id="messageList"></ul>
    <!-- 由AI協助撰寫 -->
    <script>
        const CURRENT_USER_ACCOUNT = <?= isset($_SESSION['account']) ? json_encode($_SESSION['account']) : 'null' ?>;

        const form = document.getElementById("messageForm");
        const messageList = document.getElementById("messageList");

        form.addEventListener("submit", async function(e) {
            e.preventDefault();
            const message = form.message.value.trim();

            if (!message) {
                alert("請輸入留言內容");
                return;
            }

            try {
                const res = await fetch("api/message/create.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        message
                    })
                });

                const data = await res.json();
                alert(data.message);
                if (data.success) {
                    form.reset();
                    loadMessages();
                }
            } catch (err) {
                alert("發送留言失敗，請稍後再試。");
                console.error(err);
            }
        });

        async function loadMessages() {
            try {
                const res = await fetch("api/message/list.php");
                const data = await res.json();

                if (data.success) {
                    messageList.innerHTML = "";
                    data.messages.forEach(msg => {
                        const li = document.createElement("li");
                        li.innerHTML = `
                        [${msg.created_at}] ${escapeHtml(msg.account)}：
                        <span class="content">${escapeHtml(msg.content)}</span>
                    `;

                        // 加上「編輯」與「刪除」按鈕（若為本人）
                        if (msg.account === CURRENT_USER_ACCOUNT) {
                            const editBtn = document.createElement("button");
                            editBtn.textContent = "編輯";
                            editBtn.addEventListener("click", () => editMessage(msg.id, msg.content));

                            const deleteBtn = document.createElement("button");
                            deleteBtn.textContent = "刪除";
                            deleteBtn.addEventListener("click", () => deleteMessage(msg.id));

                            li.appendChild(editBtn);
                            li.appendChild(deleteBtn);
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

        // 刪除留言
        async function deleteMessage(messageId) {
            if (!confirm("確定要刪除這則留言嗎？")) return;

            try {
                const res = await fetch("api/message/delete.php", {
                    method: "POST",
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
                console.error(err);
            }
        }

        // 編輯留言
        async function editMessage(messageId, oldContent) {
            const newContent = prompt("請輸入新的留言內容：", oldContent);
            if (newContent === null || newContent.trim() === "") return;

            try {
                const res = await fetch("api/message/update.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        message_id: messageId,
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
                console.error(err);
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

</body>

</html>
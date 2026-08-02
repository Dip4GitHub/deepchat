<?php
//require ('login.php');
session_start();
include('includes/dbconfigure.php');

$currentPage  = "email-form";
$tableName    = "web_users";
$msg          = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';

$action       = isset($_POST['action']) ? $_POST['action'] : '';
$button       = isset($_POST['button']) ? $_POST['button'] : '';
$submit       = isset($_POST['submit']) ? $_POST['submit'] : '';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Deepchat</title>
    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style2.css">
    <link rel="stylesheet" href="https://jsdelivr.net">



</head>

<body>
    <?php
    if ($msg != '') { ?>
        <div class="card shadow-lg mt-2">
            <main class="card p-1" role="main" aria-labelledby="title">
                <div class="alert alert-danger p-2" role="alert"><strong><?php echo $msg; ?></strong></div>
            </main>
        </div>

    <?php }
    if (!isset($_SESSION['name'])) {
    ?>
        <div class="card shadow-lg mt-2">
            <main class="card p-4 shadow-sm" role="main" aria-labelledby="title">
                <h2 id="title">Welcome To Deepchat</h2>
                <div class="card-body">
                    <p>Enter your email and hit Send.</p>
                    <form method="post" action="login.php">
                        <div class="mb-3 email">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control email" id="email" name="email"
                                placeholder="Enter Email">
                            <span class="error mt-3"></span>
                            <input class="btn btn-primary Send mt-3" value="Send" name="button" type="button"
                                onclick="verifyOTP(this)">
                        </div>
                        <div class="mb-3 otp mt-3" style="display: none;">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" class="form-control otp" id="otp" name="otp" placeholder="Enter OTP">
                            <span class="error mt-3"></span>
                            <input class="btn btn-primary otp mt-3" value="Confirm OTP" name="submit" type="submit">
                        </div>
                    </form>
                </div>
            </main>
        </div>
    <?php } else { ?>
        <div class="app-container">
            <div class="video-container">
                <div class="video-wrapper local-video">
                    <video id="local" autoplay muted playsinline style="width: 100%; height: 100%;"></video>
                    <div class="video-label">Your Video</div>
                </div>
                <div class="video-wrapper remote-video">
                    <video id="remote" autoplay playsinline style="width: 100%; height: 100%;"></video>
                    <div class="video-label">Remote User</div>
                </div>
            </div>

            <div class="chat-container">
                <?php
                $qry = mysqli_query($Dbconnect, "SELECT * FROM `web_users` WHERE id != '" . $_SESSION['user_id'] . "'");
                ?>
                <aside class="users-sidebar">
                    <div class="users-header">
                        <h4>Users</h4>
                    </div>
                    <div class="users-list">
                        <?php while ($user = mysqli_fetch_assoc($qry)) { ?>
                            <div class="contact" data-user-id="<?php echo $user['id']; ?>"
                                onclick="selectUser(<?php echo $user['id']; ?>,'<?php echo $user['email']; ?>'), enableNotifications()">
                                <div class="user-avatar" data-letter="<?php echo strtoupper(substr($user['email'], 0, 1)); ?>">
                                    <i class="bi bi-person-circle"></i>
                                    <?php // echo strtoupper(substr($user['email'], 0, 1)); 
                                    ?>
                                </div>
                                <div class="user-name"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </aside>

                <div class="chat-wrapper">
                    <div class="chat" id="msg_box">
                        <!-- Messages will appear here -->
                    </div>

                    <div class="chat-box">
                        <button class="send-btn call-btn" title="Start Video Call" onclick="start()">
                            <i class="bi bi-camera-video"></i>
                        </button>
                        <button class="send-btn share-btn" title="Share Screen" onclick="share()">
                            <i class="bi bi-display"></i>
                        </button>
                        <input type="text" class="msg-input" placeholder="Type a message..." id="msg" autocomplete="off" />
                        <button class="send-btn send-msg-btn" id="submit" title="Send">
                            <i class="bi bi-send"></i>
                        </button>
                        <a href="logout.php" class="send-btn logout-btn" title="Logout" style="text-decoration: none;">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            :root {
                --chat-bg: rgba(255, 255, 255, 0.08);
                --primary-dark: #000000;
                --secondary-dark: #131313;
                --accent: #ffffff;
                --accent-dark: #c8c8c8;
            }

            body {
                margin: 0;
                font-family: 'Segoe UI', sans-serif;
                height: 100vh;
                background: linear-gradient(135deg, #000000 0%, #212121 100%);
                overflow: hidden;
            }

            /* ===== APP CONTAINER ===== */
            .app-container {
                display: flex;
                flex-direction: row;
                height: 100vh;
                width: 100%;
                gap: 0;
            }

            /* ===== VIDEO SECTION ===== */
            .video-container {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 16px;
                background: linear-gradient(135deg, #000000 0%, #181818 100%);
                width: 35%;
                min-width: 280px;
                border-right: 3px solid rgba(255, 255, 255, 0.15);
                box-shadow: 2px 0 15px rgba(0, 0, 0, 0.4);
                flex-shrink: 0;
                overflow: hidden;
            }

            .video-wrapper {
                position: relative;
                border-radius: 12px;
                overflow: hidden;
                background: #000;
                border: 2px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
                transition: all 0.3s ease;
                flex: 1;
                min-height: 0;
            }

            .video-wrapper:hover {

                border: 1px solid white;
                box-shadow: 0 12px 32px rgba(223, 225, 227, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            }

            .video-wrapper video {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                background: #000;
            }

            .video-label {
                position: absolute;
                bottom: 12px;
                left: 12px;
                background: linear-gradient(135deg, #ffffff, #ffffff);
                color: #000;
                padding: 8px 14px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 0.5px;
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
                pointer-events: none;
                z-index: 10;
            }

            /* ===== CHAT SECTION ===== */
            .chat-container {
                display: flex;
                flex: 1;
                overflow: hidden;
                gap: 0;
                background: var(--primary-dark);
            }

            /* ===== USERS SIDEBAR ===== */
            .users-sidebar {
                flex: 0 0 250px;
                background: linear-gradient(135deg, #000000 0%, #181818 100%);
                border-right: 2px solid rgba(255, 255, 255, 0.1);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                height: 100%;
            }

            .users-header {
                padding: 15px;
                background: linear-gradient(135deg, #181818 0%, #000000 100%);

                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                flex-shrink: 0;
            }

            .users-header h4 {
                color: #fff;
                font-size: 14px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .users-list {
                flex: 1;
                overflow-y: auto;
                padding: 8px 0;
                background: linear-gradient(180deg, #0f1419 0%, #1a2227 100%);
            }

            .contact {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                border-left: 4px solid transparent;
                margin: 4px 0;
            }

            .contact:hover {
                background: rgba(255, 255, 255, 0.08);
                border-left-color: #ffffff;
            }

            .contact.selected {
                background: rgba(255, 255, 255, 0.12);
                border-left-color: #ffffff;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: white;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #000;
                font-weight: bold;
                font-size: 16px;
                flex-shrink: 0;
            }

            .user-name {
                color: #fff;
                font-size: 13px;
                font-weight: 500;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                flex: 1;
            }

            /* ===== CHAT WRAPPER ===== */
            .chat-wrapper {
                flex: 1;
                display: flex;
                flex-direction: column;
                background: var(--primary-dark);
                overflow: hidden;
                width: 100%;
            }

            .chat {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 12px;
                background-image: url("assets/image/black-line.jpg");
                background-size: 100% 100%;
            }

            .msg {
                padding: 12px 16px;
                max-width: 70%;
                border-radius: 12px;
                color: var(--text-color);
                font-size: 14px;
                word-wrap: break-word;
                display: inline-block;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }

            .msg.left {
                background: #f9f9f9;
                color: #111111;
                align-self: flex-start;
                border-bottom-left-radius: 4px;
                border: 1px solid rgba(255, 255, 255, 0.12);
            }

            .msg.right {
                background: #121212;
                color: #f1f1f1;
                align-self: flex-end;
                border-bottom-right-radius: 4px;
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            .msg b {
                font-weight: 600;
            }

            .msg p {
                font-size: 11px;
                color: #999;
                margin-top: 6px;
                opacity: 0.8;
            }

            .msg.right p {
                color: #666;
            }

            /* ===== CHAT INPUT BOX ===== */
            .chat-box {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px;
                background: linear-gradient(180deg, #1f2c33 0%, #162229 100%);
                border-top: 2px solid rgba(255, 255, 255, 0.1);
                flex-wrap: wrap;
                flex-shrink: 0;
            }

            .msg-input {
                flex: 1;
                min-width: 150px;
                padding: 10px 16px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 24px;
                background: var(--secondary-dark);
                color: white;
                font-size: 14px;
                outline: none;
                transition: all 0.3s ease;
            }

            .msg-input:focus {
                border-color: #ffffff;
                box-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
            }

            .msg-input::placeholder {
                color: #999;

            }

            .send-btn {
                padding: 10px 14px;
                border-radius: 50%;
                background-color: #ffffff;
                border: none;
                color: #000000;
                cursor: pointer;
                font-size: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.22);
                flex-shrink: 0;
            }

            .send-btn:hover {
                background-color: #f0f0f0;
                transform: scale(1.05);
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22);
            }

            .send-btn:active {
                transform: scale(0.95);
            }

            .call-btn {
                background: linear-gradient(135deg, #181818 0%, #000000 100%);
                color: #ffffff;
            }



            .share-btn {
                background: linear-gradient(135deg, #181818 0%, #000000 100%);
                color: #ffffff;
            }



            .send-msg-btn {
                background: linear-gradient(135deg, #181818 0%, #000000 100%);
                color: white;
            }



            .logout-btn {
                background: linear-gradient(135deg, #181818 0%, #000000 100%);
                color: #ffffff;
            }

            .logout-btn:hover {

                color: #ffffff;
            }



            .logout-btn a {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* ===== SCROLLBAR STYLING ===== */
            .users-list::-webkit-scrollbar,
            .chat::-webkit-scrollbar {
                width: 8px;
            }

            .users-list::-webkit-scrollbar-track,
            .chat::-webkit-scrollbar-track {
                background-color: linear-gradient(135deg, #181818 0%, #000000 100%);
            }

            .users-list::-webkit-scrollbar-thumb,
            .chat::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #181818 0%, #000000 100%);
                border-radius: 10px;
            }

            .users-list::-webkit-scrollbar-thumb:hover,
            .chat::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #181818 0%, #000000 100%);
            }

            /* ===== RESPONSIVE DESIGN ===== */
            @media (max-width: 1024px) {
                .video-container {
                    width: 32%;
                    min-width: 250px;
                }

                .users-sidebar {
                    flex: 0 0 140px;
                }
            }

            @media (max-width: 768px) {
                .app-container {
                    flex-direction: column;
                }

                .video-container {
                    width: 100%;
                    height: 35vh;
                    min-height: 180px;
                    flex-direction: row;
                    border-right: none;
                    border-bottom: 2px solid rgba(0, 153, 255, 0.3);
                    gap: 10px;
                    padding: 12px;
                }

                .video-wrapper {
                    flex: 1;
                }

                .chat-container {
                    flex-direction: column;
                }

                .users-sidebar {
                    flex: 0 0 60px;
                    border-right: none;
                    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
                }

                .users-list {
                    display: flex;
                    flex-direction: row;
                    gap: 4px;
                    padding: 8px;
                    overflow-x: auto;
                }

                .contact {
                    flex-direction: row;
                    padding: 8px 6px;
                    margin: 0;
                    border-left: none;
                    border-bottom: 3px solid transparent;
                    min-width: 200px;
                }

                /* Ensure avatar and name are vertically centered and name is visible */
                .contact {
                    align-items: center;
                }

                .contact:hover,
                .contact.selected {
                    border-left: none;
                    border-bottom-color: white;
                }

                .user-avatar {
                    width: 36px;
                    height: 36px;
                    font-size: 14px;
                }

                .user-name {
                    font-size: 10px;
                    writing-mode: horizontal-tb;
                    text-align: center;
                    /* allow wrapping and ensure it's not clipped behind avatar */
                    white-space: normal;
                    overflow: visible;
                    text-overflow: clip;
                    display: block;
                    max-width: 80px;
                    margin-top: 4px;
                }

                .users-header {
                    display: none;
                }

                .msg {
                    max-width: 85%;
                }

                .chat-box {
                    flex-wrap: nowrap;
                    gap: 6px;
                    padding: 10px;
                }

                .msg-input {
                    min-width: 80px;
                    font-size: 13px;
                }

                .send-btn {
                    padding: 8px 10px;
                    font-size: 14px;
                }
            }

            @media (max-width: 480px) {
                .video-container {
                    height: 28vh;
                    min-height: 140px;
                    gap: 6px;
                    padding: 8px;
                }

                .video-label {
                    font-size: 10px;
                    padding: 4px 8px;
                    bottom: 6px;
                    left: 6px;
                }

                .users-sidebar {
                    flex: 0 0 50px;
                }

                .contact {
                    padding: 6px 4px;
                    min-width: 45px;
                }

                .user-avatar {
                    width: 32px;
                    height: 32px;
                    font-size: 12px;
                }

                .user-name {
                    font-size: 9px;
                    white-space: normal;
                    overflow: visible;
                    text-overflow: clip;
                    display: block;
                    max-width: 70px;
                    margin-top: 3px;
                }

                .msg {
                    max-width: 90%;
                    padding: 8px 12px;
                    font-size: 13px;
                }

                .msg p {
                    font-size: 10px;
                }

                .chat {
                    padding: 12px;
                    gap: 8px;
                }

                .chat-box {
                    gap: 4px;
                    padding: 8px;
                }

                .msg-input {
                    min-width: 60px;
                    padding: 8px 12px;
                    font-size: 12px;
                }

                .send-btn {
                    padding: 8px;
                    font-size: 13px;
                }
            }
        </style>
        <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/jquery-3.6.0.min.js"></script>
        <script>
            const chatKeyBase64 = "<?php echo base64_encode(hash('sha256', CHAT_ENCRYPTION_KEY, true)); ?>";
            const currentUserName = "<?php echo $_SESSION['name']; ?>";
            const currentUserId = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>;

            function deleteMessage(id) {
                if (confirm('Are you sure you want to delete this message?')) {
                    conn.send(JSON.stringify({
                        type: "delete",
                        message_id: id,
                        sender_id: currentUserId,
                        receiver_id: selectedUserId
                    }));

                }
            }
        </script>
        <script>
            let selectedUserId = 0;
            let selectedUserName = '';

            function selectUser(id, email) {
                selectedUserId = id;
                selectedUserName = email;
                $('.contact').removeClass('selected');
                $('[data-user-id="' + id + '"]').addClass('selected');

                $('#msg_box').html('');
                loadPrivateChat(id);
            }
        </script>
        <script>
            // websocket connection
            var conn = new WebSocket('ws://localhost:8080?user_id=' + currentUserId);
            conn.onopen = function(e) {
                console.log("Connection established!");
            };

            async function importChatKey() {
                const keyData = Uint8Array.from(atob(chatKeyBase64), c => c.charCodeAt(0));
                return window.crypto.subtle.importKey('raw', keyData, {
                    name: 'AES-CBC'
                }, false, ['decrypt']);
            }
 
            //  sending messages
            $('#submit').click(function() {
                var msg = $('#msg').val().trim();
                if (!msg || selectedUserId == 0) {
                    alert('Please select user first');
                    return;
                }
                var content = {
                    type: "message",
                    msg: msg,
                    name: currentUserName,
                    sender_id: currentUserId,
                    receiver_id: selectedUserId,
                    message_type: 'text',
                    file_name: ''
                };

                //right
                conn.send(JSON.stringify(content));
                $('#msg').val('');

            });




            async function decryptChatMessage(encryptedBase64) {
                try {
                    const data = Uint8Array.from(atob(encryptedBase64), c => c.charCodeAt(0));
                    const iv = data.slice(0, 16);
                    const cipherBytes = data.slice(16);
                    const key = await importChatKey();

                    const decrypted = await window.crypto.subtle.decrypt({
                            name: 'AES-CBC',
                            iv
                        },
                        key,
                        cipherBytes
                    );

                    return new TextDecoder().decode(decrypted);
                } catch (error) {
                    console.error('Decrypt failed', error);
                    return '[decrypt error]';
                }
            }

            function appendMessage(id, name, msg, isOwn, messageTime = '') {
                msg = escapeHtml(msg);
                name = escapeHtml(name);
                var html = `<div  id="msg-${id}" class='msg ${isOwn ? "right" : "left"}' ondblclick="deleteMessage(${id})"><b>${name}:</b> ${msg}
        <p style="font-size: 10px; color: #555; margin-top: 5px;">${messageTime}</p>
      </div>`;
                $('#msg_box').append(html);
                $('#msg_box').scrollTop($('#msg_box')[0].scrollHeight);
            }

            conn.onmessage = async function(e) {
                const data = JSON.parse(e.data);

                //console.log('Received data:', data);
                // =========================
                // WEBRTC
                // =========================

                if (data.offer) {
                    // Ensure the caller is selected in UI so chat and remote video sync
                    if (selectedUserId !== data.sender_id) {
                        selectedUserId = data.sender_id;
                        $('.contact').removeClass('selected');
                        $('[data-user-id="' + data.sender_id + '"]').addClass('selected');
                    }

                    pc = new RTCPeerConnection(config);
                    pc.ontrack = e => {
                        remote.srcObject = e.streams[0];
                        remote.play();
                    };
                    pc.onicecandidate = e => {
                        if (e.candidate) {
                            ws.send(JSON.stringify({
                                ice: e.candidate
                            }));
                        }
                    };

                    stream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: true
                    });

                    local.srcObject = stream;
                    await local.play();


                    stream.getTracks().forEach(t =>
                        pc.addTrack(t, stream)
                    );

                    await pc.setRemoteDescription(data.offer);

                    const answer = await pc.createAnswer();

                    await pc.setLocalDescription(answer);

                    ws.send(JSON.stringify({
                        answer
                    }));

                    return;
                }

                if (data.answer) {
                    await pc.setRemoteDescription(data.answer);
                    return;
                }

                if (data.ice) {
                    await pc.addIceCandidate(data.ice);
                    return;
                }

                // =========================
                // CHAT
                // =========================
                if (data.type === "delete_error") {
                    alert(data.message);
                    return;
                }
                if (data.type === "delete") {
                    //$("#msg-" + data.message_id).remove();


                    $("#msg-" + data.message_id).html(' <p style="font-size: 10px; color: #555; margin-top: 5px;">Message deleted</p>');
                    $('#msg_box').scrollTop($('#msg_box')[0].scrollHeight);
                    return;

                }

                if (data.type == "message") {
                    const isCurrentChat = ((data.sender_id == selectedUserId && data.receiver_id ==
                        currentUserId) || (
                        data
                        .sender_id == currentUserId && data.receiver_id == selectedUserId));

                    if (!isCurrentChat) {
                        return;
                    }

                    var decryptedMsg =
                        await decryptChatMessage(data.msg);

                    var isOwn =
                        data.sender_id == currentUserId;

                    var messageTime = data.messageTime;
                    // right 

                    appendMessage(
                        data.message_id,
                        data.sender_name,
                        decryptedMsg,
                        isOwn,
                        messageTime
                    );
                    if (data.sender_id != currentUserId) {
                        showNotification(data.sender_name, decryptedMsg)

                    }
                    return;
                }



            }
            async function loadPrivateChat(userId) {
                try {
                    $('#msg_box').html('');
                    const response =
                        await fetch('includes/private-chat.php?user_id=' + userId);

                    const messages = await response.json();

                    //showing message
                    for (const item of messages) {
                        const decrypted =
                            await decryptChatMessage(
                                item.message
                            );
                        //console.log('Decrypted message:', decrypted);

                        const isOwn =
                            item.sender_id == currentUserId;
                        var messageTime = item.messageTime;

                        // right
                        appendMessage(
                            item.id,
                            item.sender_name,
                            decrypted,
                            isOwn,
                            messageTime
                        );
                    }
                } catch (error) {
                    console.log(error);
                }
            }
            $('#msg').keypress(function(e) {
                if (e.which == 13) {
                    $('#submit').click();
                }
            });

            function escapeHtml(text) {
                return $('<div>').text(text).html();
            }
        </script>
        <script>
            let pc, stream;
            const ws = conn;

            const config = {
                iceServers: [{
                    urls: "stun:stun.l.google.com:19302"
                }]
            };


            /* get button */
            const local = document.getElementById('local');
            const remote = document.getElementById('remote');

            async function start() {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: true
                });
                local.srcObject = stream;
                await local.play();

                pc = new RTCPeerConnection(config);

                stream.getTracks().forEach(t => pc.addTrack(t, stream));

                pc.ontrack = e => remote.srcObject = e.streams[0];
                await remote.play();
                /* appendMessagepc.ontrack = async (e) => {
                      remote.srcObject = e.streams[0];
                      await remote.play();
                  };*/


                pc.onicecandidate = e => {
                    if (e.candidate) ws.send(JSON.stringify({
                        ice: e.candidate
                    }));
                };

                const offer = await pc.createOffer();
                await pc.setLocalDescription(offer);
                ws.send(JSON.stringify({
                    offer
                }));
            }

            async function share() {
                if (!pc) {
                    alert('Please start the video call first, then click screen share.');
                    return;
                }

                try {
                    const screenStream = await navigator.mediaDevices.getDisplayMedia({
                        video: true,
                        audio: true
                    });

                    const screenTrack = screenStream.getVideoTracks()[0];
                    if (!screenTrack) {
                        alert('Unable to capture screen.');
                        return;
                    }

                    const sender = pc.getSenders().find(s =>
                        s.track &&
                        s.track.kind === 'video'
                    );

                    if (sender) {
                        await sender.replaceTrack(screenTrack);
                    } else {
                        pc.addTrack(screenTrack, screenStream);
                    }

                    local.srcObject = screenStream;
                    await local.play();

                    screenTrack.onended = async () => {
                        try {
                            const cameraStream = await navigator.mediaDevices.getUserMedia({
                                video: true,
                                audio: true
                            });
                            const cameraTrack = cameraStream.getVideoTracks()[0];

                            if (sender && cameraTrack) {
                                await sender.replaceTrack(cameraTrack);
                            } else if (cameraTrack) {
                                pc.addTrack(cameraTrack, cameraStream);
                            }

                            stream = cameraStream;
                            local.srcObject = cameraStream;
                        } catch (screenErr) {
                            console.error('Failed to restore camera after screen sharing ended', screenErr);
                        }
                    };
                } catch (err) {
                    console.error(err);
                    if (err.name === 'NotAllowedError' || err.name === 'SecurityError') {
                        alert('Screen share permission was denied. Please allow screen sharing in your browser prompt.');
                    } else {
                        alert('Screen sharing failed: ' + err.message);
                    }
                }
            }

            /* ws.onmessage = async e => {
              const data = JSON.parse(e.data);

              if (data.offer) {
                pc = new RTCPeerConnection(config);

                pc.ontrack = e => remote.srcObject = e.streams[0];
                pc.onicecandidate = e => {
                  if (e.candidate) ws. JSON.stringify({
                    ice: e.candidate
                  }));
                };

                stream = await navigator.mediaDevices.getUserMedia({
                  video: true,
                  audio: true
                });
                local.srcObject = stream;
                stream.getTracks().forEach(t => pc.addTrack(t, stream));

                await pc.setRemoteDescription(data.offer);
                const answer = await pc.createAnswer();
                await pc.setLocalDescription(answer);
                ws.send(JSON.stringify({
                  answer
                }));
              }

              if (data.answer) {
                await pc.setRemoteDescription(data.answer);
              }

              if (data.ice) {
                await pc.addIceCandidate(data.ice);
              }
            };*/
            function enableNotifications() {
                Notification.requestPermission().then(permission => {
                    console.log(permission);
                });

                if (Notification.permission === 'denied') {
                    Notification.requestPermission().then(permission => {
                        console.log(permission);
                    });



                }
            }

            function showNotification(sender, message) {
                if (Notification.permission === 'granted') {
                    new Notification(sender, {
                        body: message,
                        icon: "src/deepchat-logo.png"
                    });

                    // alert();
                }
            }
        </script>

    <?php } ?>
    <script>
        function verifyOTP(submit) {
            var email = $('#email').val();
            $.post('includes/ajax/send-mail.php', {
                    email: email
                },
                function(data, status) {
                    // console.log(status); 
                    // console.log(data); 
                    $('.otp').show();
                    //$('.email').hide();

                });
        }
    </script>
</body>

</html>
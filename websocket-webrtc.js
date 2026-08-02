
/* websocket-webrtc.js
 =========================
   GLOBAL VARIABLES
========================= */




let pc;
let localStream;

/* =========================
   VIDEO ELEMENTS
========================= */

const localVideo =
document.getElementById('local');

const remoteVideo =
document.getElementById('remote');

/* =========================
   WEBSOCKET
========================= */

const ws =
new WebSocket(
'ws://localhost:8080?user_id=' + currentUserId
);

ws.onopen = () => {
    console.log('WebSocket Connected');
};

/* =========================
   RTC CONFIG
========================= */

const config = {
    iceServers: [
        {
            urls: 'stun:stun.l.google.com:19302'
        }
    ]
};

/* =========================
   SELECT USER
========================= */

function selectUser(id, name)
{
    selectedUserId = id;

    $('.contact').removeClass('selected');

    $('[data-user-id="' + id + '"]')
    .addClass('selected');
}

/* =========================
   CREATE PEER
========================= */

function createPeer()
{
    pc = new RTCPeerConnection(config);

    /* SEND ICE */

    pc.onicecandidate = event => {

        if(event.candidate)
        {
            ws.send(JSON.stringify({
                type: 'ice',
                candidate: event.candidate,
                sender_id: currentUserId,
                receiver_id: selectedUserId
            }));
        }
    };

    /* RECEIVE REMOTE STREAM */

    pc.ontrack = event => {

        remoteVideo.srcObject =
        event.streams[0];

        remoteVideo.play();
    };
}

/* =========================
   START CALL
========================= */

async function start()
{
    if(selectedUserId == 0)
    {
        alert('Select user first');
        return;
    }

    createPeer();

    localStream =
    await navigator.mediaDevices
    .getUserMedia({
        video: true,
        audio: true
    });

    localVideo.srcObject =
    localStream;

    await localVideo.play();

    localStream.getTracks()
    .forEach(track => {

        pc.addTrack(track, localStream);

    });

    const offer =
    await pc.createOffer();

    await pc.setLocalDescription(offer);

    ws.send(JSON.stringify({

        type: 'offer',
        offer: offer,

        sender_id: currentUserId,
        receiver_id: selectedUserId

    }));
}

/* =========================
   SCREEN SHARE
========================= */

async function share()
{
    try {

        const screenStream =
        await navigator.mediaDevices
        .getDisplayMedia({
            video: true
        });

        const screenTrack =
        screenStream.getVideoTracks()[0];

        const sender =
        pc.getSenders().find(s =>
            s.track.kind === 'video'
        );

        await sender.replaceTrack(screenTrack);

        localVideo.srcObject =
        screenStream;

        await localVideo.play();

        screenTrack.onended =
        async () => {

            const cameraStream =
            await navigator.mediaDevices
            .getUserMedia({
                video: true,
                audio: true
            });

            const cameraTrack =
            cameraStream
            .getVideoTracks()[0];

            await sender
            .replaceTrack(cameraTrack);

            localVideo.srcObject =
            cameraStream;
        };

    } catch(err) {

        console.log(err);

    }
}

/* =========================
   RECEIVE SOCKET MESSAGE
========================= */

ws.onmessage = async (event) => {

    const data =
    JSON.parse(event.data);

    /* IGNORE OTHER USERS */

    const validUser = (

        (
            data.sender_id ==
            selectedUserId

            &&

            data.receiver_id ==
            currentUserId
        )

        ||

        (
            data.sender_id ==
            currentUserId

            &&

            data.receiver_id ==
            selectedUserId
        )
    );

    if(!validUser)
    {
        return;
    }

    /* =========================
       OFFER
    ========================= */

    if(data.type === 'offer')
    {
        createPeer();

        localStream =
        await navigator.mediaDevices
        .getUserMedia({
            video: true,
            audio: true
        });

        localVideo.srcObject =
        localStream;

        await localVideo.play();

        localStream.getTracks()
        .forEach(track => {

            pc.addTrack(track, localStream);

        });

        await pc.setRemoteDescription(
            new RTCSessionDescription(
                data.offer
            )
        );

        const answer =
        await pc.createAnswer();

        await pc.setLocalDescription(answer);

        ws.send(JSON.stringify({

            type: 'answer',
            answer: answer,

            sender_id: currentUserId,
            receiver_id: data.sender_id

        }));

        return;
    }

    /* =========================
       ANSWER
    ========================= */

    if(data.type === 'answer')
    {
        await pc.setRemoteDescription(
            new RTCSessionDescription(
                data.answer
            )
        );

        return;
    }

    /* =========================
       ICE
    ========================= */

    if(data.type === 'ice')
    {
        try {

            await pc.addIceCandidate(
                new RTCIceCandidate(
                    data.candidate
                )
            );

        } catch(err) {

            console.log(err);

        }

        return;
    }

    /* =========================
       CHAT
    ========================= */

    if (data.type === "text") {
      appendMessage(
        data.sender_id,
        data.name,
        data.msg,
        data.sender_id == currentUserId,
      );
    }
};

/* =========================
   SEND CHAT
========================= */

$('#submit').click(function() {

    const msg =
    $('#msg').val().trim();

    if(msg == '')
    {
        return;
    }

    if(selectedUserId == 0)
    {
        alert('Select user first');
        return;
    }

    ws.send(
      JSON.stringify({
        type: "text",

        msg: msg,
        name: currentUserName,

        sender_id: currentUserId,
        receiver_id: selectedUserId,
      }),
    );

    appendMessage(
        0,
        currentUserName,
        msg,
        true
    );

    $('#msg').val('');
});

/* =========================
   APPEND MESSAGE
========================= */

function appendMessage(
    id,
    name,
    msg,
    isOwn
)
{
    const html = `
    <div class="msg ${
        isOwn ? 'right' : 'left'
    }">

        <b>${name}</b><br>

        ${msg}

    </div>
    `;

    $('#msg_box').append(html);

    $('#msg_box').scrollTop(
        $('#msg_box')[0].scrollHeight
    );
}

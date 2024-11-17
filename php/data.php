<?php

    while($row = mysqli_fetch_assoc($query)) {
        $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']} OR outgoing_msg_id = {$row['unique_id']} AND outgoing_msg_id = {$outgoing_id} OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";

        $query2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($query2);

        if(mysqli_num_rows($query2) > 0) {
            $result = $row2['msg'];
        } else {
            $result = "No new messages";
        }

        if(strlen($result) > 28) {
            $msg = substr($result, 0, 28) . '...';
        } else {
            $msg = $result;
        }

        if(isset($row['outgoing_msg_id'])) {
            if($outgoing_id === $row2['outgoing_msg_id']) {
                $you = "You: ";
            } else {
                $you = "";
            }
        } else {
            $you = "";
        }

        if($row['status'] === "Offline") {
            $offline = "offline";
        } else {
            $offline = "";
        }

        if($outgoing_id === $row['unique_id']) {
            $hide_me = "hide";
        } else {
            $hide_me = "";
        }

        $output .= '<a href="chat.php?user_id='.$row['unique_id'].'">
        <div class="content">
            <img src="php/images/'.$row['img'].'" alt="">
            <div class="details">
                <span>'. $row['fname'] . " " . $row['lname'] . '</span>
                <p>' . $you . $msg . '</p>
            </div>
        </div>
        <div class="status-dot '.$offline.' "><i class="fas fa-circle"></i></div>
        </a>';
    }

?>
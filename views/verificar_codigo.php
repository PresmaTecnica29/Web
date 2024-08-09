<?php
require "enviar_gmails.php";
// create a database connection
$this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// change character set to utf8 and check it
if (!$this->db_connection->set_charset("utf8")) {
    $this->errors[] = $this->db_connection->error;
}

// if no connection errors (= working database connection)
if (!$this->db_connection->connect_errno) {
$user_email = $this->db_connection->real_escape_string(strip_tags($_POST['user_email'], ENT_QUOTES));

$sql = "SELECT user_id FROM users WHERE user_email = '$user_email'";
$query_check_user_id = $this->db_connection->query($sql);
$fetchid = $query_check_user_id->fetch_assoc();
$userid = $fetchid["user_id"];
$codeinput = $_POST['registerinput_verfcode'];
if (verifyCode($userid, $codeinput)) {

    echo 
}



}
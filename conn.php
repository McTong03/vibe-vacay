<?php
  $con = mysqli_connect("localhost", "root", "", "vibe-vacay");
  
  if (!$con) {
      die("Connection failed: " . mysqli_connect_error());
  }
?>
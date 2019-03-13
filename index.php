<?php require "config.php";
$url = "";

if (isset($_GET["c"])) {
  $sql = "SELECT url, clicks FROM links WHERE code = ?";

  if ($stmt = $mysqli->prepare($sql)) {
    //bind string to the prepared statement as a parameter
    $stmt->bind_param("s", $param_code);
    //set parameter
    $param_code = trim($_GET["c"]);

    //attempt to execute statement
    if ($stmt->execute()) {
      $stmt->store_result();
      if ($stmt->num_rows > 0) {
        $stmt->bind_result($url, $clicked);
        $stmt->fetch();
        $stmt->close();

        header("Location: $url" );

        $sql = "UPDATE links SET clicks = ". ++$clicked ." WHERE code = '" . $param_code . "'";
        $mysqli->query($sql);
        $mysqli->close();

        exit();
      }
    }
  }
} else {
  $url = BASE_URL . "generate/";
  header("Location: $url" );
  exit();
}
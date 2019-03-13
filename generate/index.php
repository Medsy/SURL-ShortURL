<?php require "../config.php";
$output = $code = $status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $url = filter_var(trim($_POST["url"]), FILTER_SANITIZE_URL);

//attempted to appened http:// to the url if it was not present.
//this works as intended but makes it harder to validate the url
//and couldn't find a nicer solution
//  $scheme = parse_url($url, PHP_URL_SCHEME);
//  if (empty($scheme)) {
//    $url = 'http://' . ltrim($url, '/');
//  }

  //begin validating the url
  if (empty($url)) {
    $output = "You need to enter an URL.";
    $status = "error";
  } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
    $output = "You need to enter a valid URL.";
    $status = "error";
  } else {
    //prepare SELECT statement
    $sql = "SELECT code, clicks FROM links WHERE url = ?";

    if ($stmt = $mysqli->prepare($sql)) {
      //bind string to the prepared statement as a parameter
      $stmt->bind_param("s", $param_url);
      //set parameter
      $param_url = trim($url);
      
      //attempt to execute statement
      if ($stmt->execute()) {
        $stmt->store_result();
        //check if url is already in the db
        if ($stmt->num_rows > 0) {
          $stmt->bind_result($code, $clicked);
          $stmt->fetch();
          $stmt->close();
          //return code from database
          $output = BASE_URL . "?c=" . $code;
          $outputClicks = $clicked;
          $status = "link";
        } else {
          //prepare insert statement. Only Inserting url into database here.
          $sql = "INSERT INTO links (url) VALUE (?)";

          if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_url);

            $param_url = $url;

            $stmt->execute();
            $lastID = $mysqli->insert_id;
            $stmt->close();
            // base encode to 36 the the row ID plus a large number so that we get better looking codes's even with low ID values
            $code = base_convert($lastID + 172737 , 10, 36);

            // once code is generated, updated the last id we grabbed after we inserted
            $sql = "UPDATE links SET code = '". $code ."' WHERE id = " . $lastID;
            $mysqli->query($sql);
            $mysqli->close();

            // return generated code
            $output = BASE_URL . "?c=" . $code;
            $status = "link";
          }
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Meds | SURL</title>
  <link rel="stylesheet" type="text/css" href="css/normalize.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link href="https://fonts.googleapis.com/css?family=Roboto:900" rel="stylesheet">
</head>

<body>
<div id="page">
  <div id="mainContainer">
    <header><h1 title="SURL">SURL</h1> <br> <h2 title="Shorten U.R.L">Shorten U.R.L</h2></header>
    <form action="" method="post">

      <input id="url" name="url" type="text" class="mainInput <?=$status?>" value="<?=htmlspecialchars(isset($_POST["url"]) ? $_POST["url"] : ""); ?>"><!-- ternary operator used to retain the input value after post if it is set -->
      <button class="submitButton" type="submit">SUBMIT</button>
    </form>
    <span class="output <?=$status?>">
    <?php
    if ($status == "link") {
      echo '<a class="link" href="'. $output .'" target="_blank">' . $output . '</a><br> Clicks: ' . $outputClicks;
//      echo '<a class="copyLink" href="#" onclick="copy()">copy</a>';
//      echo '<input id="hiddenLink" type="text" value="' . $output .'" style="width:0;height:0;position:absolute;left:20000px;">'; //this is pretty lazy... also the copy link doesn't work in Chrome and couldn't find the solution in time
    } else {
      echo $output;
    }
    ?>
    </span>
  </div>
</div>
<script type="text/javascript">
  function copy() {
      var copyText = document.getElementById("hiddenLink");
      copyText.select();
      document.execCommand("copy");
  }
</script>
</body>
</html>


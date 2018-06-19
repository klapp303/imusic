<?php
//sessionからエラーメッセージを確認
session_start();
if (@$_SESSION['error_msg']) {
    $error_msg = $_SESSION['error_msg'];
}
session_destroy();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>楽曲データ</title>
<link rel="stylesheet" type="text/css" href="imusic.css">
</head>

<body>
<div id="container">
<?php if (@$error_msg): ?>
<p><?php echo $error_msg; ?></p>
<?php endif; ?>

<form id="imusic-post" class="" method="post" action="php/upload.php" enctype="multipart/form-data">
  iTunes Library.xmlファイル：<input type="file" name="file"><br>
  <select name="mode">
    <option value="rate">レート</option>
    <option value="play_count">再生数</option>
    <option value="count">曲数</option>
    <option value="rate5">レート5のみ</option>
    <option value="rate4">レート4以上</option>
  </select><br>
  <input type="submit" value="楽曲データを解析する">
</form>
</div><!-- /#container -->
</body>
</html>
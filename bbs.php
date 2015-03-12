<?php
	//DB接続
	$link = mysql_connect('localhost', 'root', '');

	if (!$link) {
		die('DB接続出来ません。' . mb_convert_encoding(mysql_error(), 'UTF-8', 'EUC-JP, SJIS'));
	}

	//DB選択
	mysql_select_db('oneline_bbs', $link);

	$errors = array();

	//POSTなら保存処理
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$name = null;
		if (!isset($_POST['name']) || !strlen($_POST['name'])) {
			$errors['name'] = '名前を入力してください。';
		} else if (strlen($_POST['name']) > 40) {
			$errors['name'] = '名前は40文字以内で入力してください。';
		} else {
			$name = $_POST['name'];
		}
	}

	//コメント入力チェック
	$comment = null;
	if (!isset($_POST['comment']) || !strlen($_POST['comment'])) {
			$errors['comment'] = 'コメントを入力してください。';
		} else if (strlen($_POST['comment']) > 40) {
			$errors['comment'] = 'コメントは200文字以内で入力してください。';
		} else {
			$comment = $_POST['comment'];
		}

		//エラーなしなら保存
		if (count($errors) === 0) {
			$sql = "insert into post(name, comment, created_at) values ('"
				. mysql_real_escape_string($name) . "', '"
				. mysql_real_escape_string($comment) . "', '"
				. date('Y-m-d H:i:s') ."')";

			//保存
			mysql_query($sql, $link);

		}

?>

<!DOCTYPE html>
<html>
<head>
	<title>ひとこと掲示板</title>
	<meta charset="UTF-8">
</head>
<body>
	<h1>ひとこと掲示板</h1>

	<!-- これよりフォーム -->
	<form action="bbs.php" method="POST">
		<?php if (count($errors)) { ?>
		<ul class="error_list">
			<?php
				foreach ($errors as $error) {
			?>
			<li>
				<?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
		名前：<input type="text" name="name"><br>
		ひとこと：<input type="text" name="comment" size="60" ><br>
		<input type="submit" name="submit" value="送信">
	</form>
	<!-- フォーム終了 -->

	<!-- これより投稿内容表示 -->
	<?php
		$sql = "select * from post order by created_at desc";
		$result = mysql_query($sql, $link);
	?>
	<?php if($result !== false && mysql_num_rows($result)) { ?>
		<ul>
		<?php while ($post = mysql_fetch_assoc($result)) { ?>
			<li>
				<?php echo htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8'); ?> : 
				<?php echo htmlspecialchars($post['comment'], ENT_QUOTES, 'UTF-8'); ?> - 
				<?php echo htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?>
			</li>
		<?php } ?>
		</ul>
	<?php } ?>
	<?php
		//DBCLOSE
		mysql_free_result($result);
		mysql_close($link);

		// header('Location: http://' .$_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI']);
	 ?>
	<!-- 投稿内容表示終了 -->

</body>
</html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>favorite_food</title>
</head>

<body>

<span style = "font-size : 50px;">好きな食べ物</span> <br>

<?php
    //データベース接続
    $dsn = 'mysql:dbname=***;host=localhost';
	$user = '';
	$password = 'PASSWORD';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS food"
	    ." ("
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
	    . "name char(32),"
        . "comment TEXT,"
        . "password char(32)"
	    .");";
    $stmt = $pdo->query($sql);

    $name = "";
    $comment = "";
    $deletenum = "";
    $deletepassword = "";
    $editnum = "";
    $error = "";

    //投稿欄
    if(isset($_POST['name'])){
        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $newpassword = $_POST['newpassword'];
        if($name == ""){//名前が空白
            $error = "Error：Name is empty.";
        }elseif($comment == ""){//コメントが空白
            $error = "Error：Comment is empty.";
        }elseif($newpassword == ""){//パスワードが空白
            $error = "Error：Password is empty.";
        }else{
            if($_POST['editnum2'] == ""){//新規投稿
                $sql = $pdo -> prepare("INSERT INTO food (name, comment,password) VALUES (:name, :comment, :password)");//データ入力
                $sql -> bindParam(':name', $name2, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment2, PDO::PARAM_STR);
                $sql -> bindParam(':password', $newpassword2, PDO::PARAM_STR);
                $name2 = $name;
                $comment2 = $comment;
                $newpassword2 = $newpassword; 
                $sql -> execute();
                echo "<hr>";
            }else{//編集
                $id = $_POST['editnum2']; 
                $name2 = $name;
                $comment2 = $comment;
                $newpassword2 = $newpassword;
                $sql = 'UPDATE food SET name=:name,comment=:comment,password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name2, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment2, PDO::PARAM_STR);
                $stmt->bindParam(':password', $newpassword2, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    //削除欄
    if(isset($_POST['deletenum'])){
        $deletenum = $_POST['deletenum'];
        $deletepassword = $_POST["deletepassword"];
        if($deletenum == ""){//削除番号が空白
            $error = "Error：Delete-Number is empty.";
        }elseif($deletepassword == ""){//パスワードが空白
            $error = "Error：Password is empty.";
        }else{
            $id = $deletenum;
            $sql = 'SELECT * FROM food WHERE id=:id';//削除番号に一致するデータを取得
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $result) {
		        $t_password = $result["password"];
	        }
	        if ($t_password != $deletepassword){//パスワードが不一致
	            $error = "Error：Password is invalid.";
	        }
            $sql = 'delete from food where id=:id';//データ削除実施
	        $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    //編集番号の指定
    if(isset($_POST['editnum'])){
        $editnum = $_POST['editnum'];
        $editpassword = $_POST["editpassword"];
        if($editnum == ""){//編集番号が空白
            $error = "Error：Edit-Number is empty.";
        }elseif($editpassword == ""){//パスワードが空白
            $error = "Error：Password is empty.";
        }else{
            $id = $_POST['editnum'];
            $sql = 'SELECT * FROM food WHERE id=:id';//編集番号に一致するデータを取得
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $result) {
                $t_password = $result["password"];
                $t_name = $result["name"];
                $t_comment = $result["comment"];
	        }
	        if ($t_password != $editpassword){
	            $error = "Error：Password is invalid.";
                $editnum = "";
	        }else{
                $name = $t_name;
                $comment = $t_comment;
            }
        }
    }
    ?>

    【 投稿フォーム 】<br>
   <form action="" method="post">
        名前：　　　　<input type="text" name="name" value = "<?php echo $name; ?>"> <br>
        コメント：　　<input type="text" name="comment" value = "<?php echo $comment; ?>"> <br>
        <input hidden="num" name="editnum2" value = "<?php echo $editnum; ?>">
        パスワード：　<input type="password" name="newpassword"> <br>
        <input type="submit" name="submit">
    </form>
    <br>
    
    【 削除フォーム 】<br>
    <form action="" method="post">
       削除番号： 　　<input type="num" name="deletenum"> <br>
       パスワード：　 <input type="password" name="deletepassword"> <br>
        <input type="submit" name="submit" value = "削除">
    </form>
    <br>
    
    【 編集フォーム 】 <br>
    <form action="" method="post">
        編集番号：　　<input type="num" name="editnum"> <br>
        パスワード：　<input type="password" name="editpassword"> <br>
        <input type="submit" name="submit" value = "編集">
    </form>

<?php
    if($error != ""){
    echo $error . "<br>";//エラーの表示
    }
    echo "---------------------------------------" . "<br>";
    echo "【投稿一覧】" . "<br>";
	//データの表示
    $sql = 'SELECT * FROM food';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
        echo $row['comment'].'<br>';
	echo "<hr>";
    }
    ?>
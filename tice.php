
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>体侧成绩处理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
    <form action="ticechengji.php" method="post" enctype="multipart/form-data">
        <label>excel文件: </label>
        <input type="file" name="uploadedFile" />
        <label>年级: </label>
        <select name="grade">
            <option value="a" selected="selected">大一</option>
            <option value="b">大二</option>
            <option value="c">大三</option>
            <option value="d">大四</option>
        </select>
        <input type="submit" />
    </form>
</body>
</html>
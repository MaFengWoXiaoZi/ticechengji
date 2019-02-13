<?php
// 首先对各个体侧项目数字化编号, 便于计算
// 身高: 0, 体重: 1; 
// 肺活量: 2;
// 50米跑: 3;
// 立定跳远: 4;
// 坐位体前屈: 5;
// 耐力跑: 6; (女)
// 耐力跑: 7; (男)
// 一分钟仰卧起坐: 8; (女)
// 引体向上: 9; (男)

    include "PHPExcel-1.8/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";
    include "PHPExcel-1.8/PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php";
    include "test_standard.php";

    date_default_timezone_set("PRC");

    function oneExcelFileUploads($excelFile)
    {
        // 对上传的文件进行类型和大小检查, 判断其是否为excel文件类型, 是否超过1M大小, 判断文件是否已经存在
        if (
            ($_FILES[$excelFile]["type"] == "application/vnd.ms-excel"
        || $_FILES[$excelFile]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
         && $_FILES[$excelFile]["size"] <= 1024000)
        {
            if ($_FILES[$excelFile]["error"] > 0)
            {
                echo "错误: " . $_FILES[$excelFile]["error"] . "<br />";
            }
            else
            {
                if (file_exists(iconv("UTF-8","GBK//IGNORE", "D:\\AppServ\\www\\excel\\" . $_FILES[$excelFile]["name"])))
                {
                    echo $_FILES[$excelFile]["name"] . "已经存在";
                }
                else
                {
                    move_uploaded_file($_FILES[$excelFile]["tmp_name"],
                    iconv("UTF-8","GBK//IGNORE", "D:\\AppServ\\www\\excel\\" . $_FILES[$excelFile]["name"])); // windows下需要对文件名进行转码, 将utf-8编码转化为gbk, Linux默认为utf-8, 因此不需要
                    echo "Stored in: " . "excel\\" . $_FILES[$excelFile]["name"];
                }
            }
        }
        else
        {
            echo "<script>alert('您上传的不是excel文件或者excel文件大小超过了1M');</script>";
        }
    }

    oneExcelFileUploads("uploadedFile");


    // 打开excel文件函数, 返回文件对象
    function openExcelFile($inputFileName)
    {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName); // 甄别文件类型
            $objReader = PHPExcel_IOFactory::createReader($inputFileType); // 跟据文件类型创建读取器
            $objReader->setReadDataOnly(true); // 只读取数据，忽略其它各种格式设置
            $objPHPExcel = $objReader->load($inputFileName); // 用读取器读取文件
        } catch(Exception $e) {
            die ("加载文件发生错误: " . pathinfo ($inputFileName1, PATHINFO_BASENAME) . ":" . $e->getMessage()); // 出错时，触发异常
        }
        return $objPHPExcel;
    }

    // 提取一个学生的体侧成绩信息
    function getOneStudentInfo(&$id, &$name, $sheet, $rowIndex)
    {   
        $id = $sheet->getCell("D".$rowIndex)->getValue();
        $name = $sheet->getCell("E".$rowIndex)->getValue();
        $oneStudentGradesInfo = array();  // 存放单个学生的所有体侧项目得分信息
        $index = 0; // 存放单个学生的所有体侧项目得分信息数组的下标
        $cellValue = NULL; // 判断获取的单元格值是否为空, 初始化为空
        $highestColumn = $sheet->getHighestColumn(); // 获得这张表的最大列数 
        $allColumn = PHPExcel_Cell::columnIndexFromString($highestColumn);  // 列数用英文字母表示, 当其大于Z时，需要转换成数字，否则会出错
        $testNum = 0; // 体侧项目的编号，从1开始
        for ($column = 6; $column < $allColumn; $column++)
        {
            $columnA = PHPExcel_Cell::stringFromColumnIndex($column); // 列的数字下标需要转回字母下标
            $cellValue = $sheet->getCell($columnA . $rowIndex)->getValue();

            // 获取当前分数单元格的值, 不为空或者为0的变量则存储，若为空不存储
            if ($cellValue != NULL || (isset($cellValue) && $cellValue ==0 )) 
            {
                $oneStudentGradesInfo[$index] = array(); // 用二维数组的第二维作为关联数组
                $oneStudentGradesInfo[$index]["testNum"] = $testNum; // 存储体侧项目的数字化编号
                $oneStudentGradesInfo[$index]["info"] = $cellValue; // 存储该项目的学生的参加信息
                $index++;
            }
            $testNum++; // 跳到下一个体侧项目编号
        }
        return $oneStudentGradesInfo;
    }
    
    // 计算一个学生的体侧成绩信息, $gradesInfo中是单个学生的详细信息
    function calucateOneStudentGrades($gradesInfo)
    {
        $finalGrades = 0.0;
        if (isset($_POST["grade"]))
        {   // 大一大二体侧成绩计算
            if ($_POST["grade"] == "a" || $_POST["grade"] == "b")
            {
               $finalGrades = TestStandard::college1_2Test($gradesInfo);
            }
            // 大三大四体测成绩计算
            else if ($_POST["grade"] == "c" || $_POST["grade"] == "d")
            {
               $finalGrades = TestStandard::college3_4Test($gradesInfo);
            }
        }

        return $finalGrades;
    }

    // 将体测成绩信息写入excel文件
    function writeResultToExcel($result)
    {
        global $excelFileName;
        $objPHPExcel = new PHPExcel(); // 新建一个excel对象
        $objPHPExcel->setActiveSheetIndex(0); // 设置该excel对象的当前活动表格

        $column = "A"; // 列从A开始, A为列的基下标
        $row = 1; // 行下标从1开始
        // 循环遍历该二维结果数组, 将每个值写入excel中的指定的每个单元格中
        foreach ($result as $item)
        {
            $j = 0; // 列的变下标
            foreach ($item as $key => $value)
            {
                // 用ord函数返回单个字符的ascii码对应的整数值, 加1后再使用chr函数转换为单个字符
                $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($column)+$j) . $row, $value);
                $j++;
            }
            $row++;
        }

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用该excel对象来创建一个excel2007写入器
        $excelFileName = str_replace('.php', time() . '.xlsx', __FILE__);
        $objWriter->save($excelFileName); // 保存该excel文件
    }

    $excelFileName = "";  // 全局的生成的excel文件名
    // windows下文件名要进行编码转换, Linux不需要
    $objPHPExcel = openExcelFile(iconv("UTF-8","GBK//IGNORE", $_FILES["uploadedFile"]["name"]));
    if (isset($_POST["grade"]))
    {
        $grade =  $_POST["grade"]; // 存储学生所属成绩
    }
    $sheet = $objPHPExcel->getSheet(0); // 获取第一张excel中的第一个表格
    $allRow = $sheet->getHighestRow(); // 获取表格的所有行数
    $id = ""; // 记录学号
    $name = ""; // 记录姓名
    $allStudentsInfo = array(); // 存放所有学生体侧信息的数组
    $index = 0; // 存放所有学生体侧成绩信息数组的下标
    $allStudentsGradesInfo = array(); // 存放所有学生体侧所得最终成绩数组

    
    for ($i = 1; $i <= $allRow; $i++)
    {
        $sex = $sheet->getCell("F".$i)->getValue();
        // 判断性别
        if ($sex == "男")
        {
            $sex = "m";
        }
        else
        {
            $sex = "w";
        }
        $oneStudentInfo = getOneStudentInfo($id, $name, $sheet, $i);
        $allStudentsInfo[$index]["id"] = $id; // 存储学号
        $allStudentsInfo[$index]["name"] = $name; // 存储姓名
        $allStudentsInfo[$index]["sex"] = $sex; // 存储性别
        $allStudentsInfo[$index]["info"] = $oneStudentInfo; // 存储单个学生体侧信息
        $index++;
    }

    for ($i = 0; $i < count($allStudentsInfo); $i++)
    {
        $oneStudentFinalGrade = calucateOneStudentGrades($allStudentsInfo[$i]);

        echo "学号: " . $allStudentsInfo[$i]["id"] . " 姓名: " . $allStudentsInfo[$i]["name"] . " 体侧最终分数: " . $oneStudentFinalGrade;
        $allStudentsGradesInfo[$i]["id"] = $allStudentsInfo[$i]["id"];
        $allStudentsGradesInfo[$i]["name"] = $allStudentsInfo[$i]["name"];
        $allStudentsGradesInfo[$i]["grades"] = $oneStudentFinalGrade; // 将每一个学生的学号、姓名、最终成绩信息存储到该数组中

        echo "<br />"; 
    }

    writeResultToExcel($allStudentsGradesInfo);
    $excelFileName = str_replace("D:\\AppServ\\www\\", "", $excelFileName);
    echo $excelFileName;
    echo '<a href="http://localhost/' . $excelFileName . '">导出到Excel</a>';
?>
<?php
    // 将体侧成绩长跑中的分与秒的时间组合转换成以秒为时间单位的量, eg: 3'12 => 192
    function str_ToTime($strtime)
    {
        $strtimeArray = explode("'", $strtime);
        $min = floatval($strtimeArray[0]) * 60;
        $sec = floatval($strtimeArray[1]);
        $time = $min + $sec;
        
        return $time;
    }

    // 按计算的各项成绩乘以所占权重得到最终的分数
    function getFinalGrades($gradesArray)
    {
        return $gradesArray[0] * 0.15 + $gradesArray[1] * 0.15 + $gradesArray[2] * 0.2 + $gradesArray[3] * 0.1 + $gradesArray[4] * 0.1 + $gradesArray[5] * 0.1 + $gradesArray[6] * 0.2;
    }

    class TestStandard
    {
        // 计算各项成绩
        static public function calculateAllItems($ExamObj, $gradesInfo)
        {
            // 计算体重指数得分
            $BMIScores = $ExamObj->getBMIScores(floatval($gradesInfo["info"][0]["info"]), floatval($gradesInfo["info"][1]["info"]));

            // 计算肺活量得分
            $lungCapacityScores = $ExamObj->getLungCapacityScores(floatval($gradesInfo["info"][2]["info"]));

            // 计算短跑得分
            $sprintRunningScores = $ExamObj->getSprintRunningScores(floatval($gradesInfo["info"][3]["info"]));

            // 计算立定跳远得分
            $standingJumpingScores = $ExamObj->getStandingJumpingScores(floatval($gradesInfo["info"][4]["info"]) * 100);

            // 计算坐位体前屈得分
            $sitAndReachScores = $ExamObj->getSitAndReachScores(floatval($gradesInfo["info"][5]["info"]));

            // 计算长跑得分
            $longDistanceRunningScores = $ExamObj->getLongDistanceRunningScores(str_ToTime($gradesInfo["info"][6]["info"]));

            // 计算女生一分钟仰卧起坐得分或者男生引体向上的得分
            if ($gradesInfo["info"][7]["testNum"] == 8)
                $situp_or_chingScores = $ExamObj->getSitupScores($gradesInfo["info"][7]["info"]);
            else
                $situp_or_chinningScores = $ExamObj->getChinningScores($gradesInfo["info"][7]["info"]);

            return array($BMIScores, $lungCapacityScores, $sprintRunningScores, $sitAndReachScores, $standingJumpingScores, $situp_or_chinningScores, $longDistanceRunningScores);
        }

        // 按照大一大二体侧标准计算
        static public function college1_2Test($gradesInfo)
        {
            $BodyExamObj = new BodyExaminationItems($gradesInfo["sex"], 12);
            $allGradesArray = TestStandard::calculateAllItems($BodyExamObj, $gradesInfo);
            $finalGrades = getFinalGrades($allGradesArray);

            return $finalGrades;
        }

        // 按照大三大四体侧标准计算
        static public function college3_4Test($gradesInfo)
        {
            $BodyExamObj = new BodyExaminationItems($gradesInfo["sex"], 34);
            $allGradesArray = TestStandard::calculateAllItems($BodyExamObj, $gradesInfo);
            $finalGrades = getFinalGrades($allGradesArray);

            return $finalGrades;
        }
    }

    // 集成了所有体侧项目的标准
    class BodyExaminationItems
    {   
        private $sex = ""; // 用于评判相应体侧项目成绩的性别
        private $gradeLevel = 0; // 用于评判相应体侧项目成绩的年级


        // 构造函数初始化
        public function __construct($sex, $gradeLevel)
        {
            $this->sex = $sex;
            $this->gradeLevel = $gradeLevel;
        }

        // 根据体重指数获得体重得分
        public function getBMIScores($height, $weight)
        {
            $BMI = $weight / (pow($height / 100, 2));
            $BMIScores = 0;

            if ($this->sex == "m")
            {
                if ($BMI >= 17.9 && $BMI <= 23.9)
                    $BMIScores = 100;
                else if ($BMI <= 17.8 || ($BMI >= 24.0 && BMI <= 27.9))
                    $BMIScores = 80;
                else 
                    $BMIScores = 60;
            }
            else
            {
                if ($BMI >= 17.2 && $BMI <= 23.9)
                    $BMIScores = 100;
                else if ($BMI <= 17.1 || ($BMI >= 24.0 && BMI <= 27.9))
                    $BMIScores = 80;
                else 
                    $BMIScores = 60;
            }

            return $BMIScores;
        }

        // 根据肺活量指数获得肺活量得分
        public function getLungCapacityScores($lungCapacity)
        {
            $lungCapacityScores = 0;

            if ($this->gradeLevel == 12)
            {
                if ($this->sex == "m")
                {
                    if ($lungCapacity >= 5040)
                        $lungCapacityScores = 100;
                    else if ($lungCapacity >= 4920 && $lungCapacity < 5040)
                        $lungCapacityScores = 95;
                    else if ($lungCapacity >= 4800 && $lungCapacity < 4920)
                        $lungCapacityScores = 90;
                    else if ($lungCapacity >= 4550 && $lungCapacity < 4800)
                        $lungCapacityScores = 85;
                    else if ($lungCapacity >= 4300 && $lungCapacity < 4550)
                        $lungCapacityScores = 80;
                    else if ($lungCapacity >= 4180 && $lungCapacity < 4300)
                        $lungCapacityScores = 78;
                    else if ($lungCapacity >= 4060 && $lungCapacity < 4180)
                        $lungCapacityScores = 76;
                    else if ($lungCapacity >= 3940 && $lungCapacity < 4060)
                        $lungCapacityScores = 74;
                    else if ($lungCapacity >= 3820 && $lungCapacity < 3940)
                        $lungCapacityScores = 72;
                    else if ($lungCapacity >= 3700 && $lungCapacity < 3820)
                        $lungCapacityScores = 70;
                    else if ($lungCapacity >= 3580 && $lungCapacity < 3700)
                        $lungCapacityScores = 68;
                    else if ($lungCapacity >= 3460 && $lungCapacity < 3580)
                        $lungCapacityScores = 66;
                    else if ($lungCapacity >= 3340 && $lungCapacity < 3460)
                        $lungCapacityScores = 64;
                    else if ($lungCapacity >= 3220 && $lungCapacity < 3340)
                        $lungCapacityScores = 62;
                    else if ($lungCapacity >= 3100 && $lungCapacity < 3220)
                        $lungCapacityScores = 60;
                    else if ($lungCapacity >= 2940 && $lungCapacity < 3100)
                        $lungCapacityScores = 50;
                    else if ($lungCapacity >= 2780 && $lungCapacity < 2940)
                        $lungCapacityScores = 40;
                    else if ($lungCapacity >= 2620 && $lungCapacity < 2780)
                        $lungCapacityScores = 30;
                    else if ($lungCapacity >= 2460 && $lungCapacity < 2620)
                        $lungCapacityScores = 20;
                    else if ($lungCapacity >= 2300 && $lungCapacity < 2460)
                        $lungCapacityScores = 10;
                    else 
                        $lungCapacityScores = 0;
                }
                else
                {
                    if ($lungCapacity >= 3400)
                        $lungCapacityScores = 100;
                    else if ($lungCapacity >= 3350 && $lungCapacity < 3400)
                        $lungCapacityScores = 95;
                    else if ($lungCapacity >= 3300 && $lungCapacity < 3350)
                        $lungCapacityScores = 90;
                    else if ($lungCapacity >= 3150 && $lungCapacity < 3300)
                        $lungCapacityScores = 85;
                    else if ($lungCapacity >= 3000 && $lungCapacity < 3150)
                        $lungCapacityScores = 80;
                    else if ($lungCapacity >= 2900 && $lungCapacity < 3000)
                        $lungCapacityScores = 78;
                    else if ($lungCapacity >= 2800 && $lungCapacity < 2900)
                        $lungCapacityScores = 76;
                    else if ($lungCapacity >= 2700 && $lungCapacity < 2800)
                        $lungCapacityScores = 74;
                    else if ($lungCapacity >= 2600 && $lungCapacity < 2700)
                        $lungCapacityScores = 72;
                    else if ($lungCapacity >= 2500 && $lungCapacity < 2600)
                        $lungCapacityScores = 70;
                    else if ($lungCapacity >= 2400 && $lungCapacity < 2500)
                        $lungCapacityScores = 68;
                    else if ($lungCapacity >= 2300 && $lungCapacity < 2400)
                        $lungCapacityScores = 66;
                    else if ($lungCapacity >= 2200 && $lungCapacity < 2300)
                        $lungCapacityScores = 64;
                    else if ($lungCapacity >= 2100 && $lungCapacity < 2200)
                        $lungCapacityScores = 62;
                    else if ($lungCapacity >= 2000 && $lungCapacity < 2100)
                        $lungCapacityScores = 60;
                    else if ($lungCapacity >= 1960 && $lungCapacity < 2000)
                        $lungCapacityScores = 50;
                    else if ($lungCapacity >= 1920 && $lungCapacity < 1960)
                        $lungCapacityScores = 40;
                    else if ($lungCapacity >= 1880 && $lungCapacity < 1920)
                        $lungCapacityScores = 30;
                    else if ($lungCapacity >= 1840 && $lungCapacity < 1880)
                        $lungCapacityScores = 20;
                    else if ($lungCapacity >= 1800 && $lungCapacity < 1840)
                        $lungCapacityScores = 10;
                    else 
                        $lungCapacityScores = 0;
                }
            }
            else
            {
                if ($this->sex == "m")
                {
                    if ($lungCapacity >= 5140)
                        $lungCapacityScores = 100;
                    else if ($lungCapacity >= 5020 && $lungCapacity < 5140)
                        $lungCapacityScores = 95;
                    else if ($lungCapacity >= 4900 && $lungCapacity < 5020)
                        $lungCapacityScores = 90;
                    else if ($lungCapacity >= 4650 && $lungCapacity < 4900)
                        $lungCapacityScores = 85;
                    else if ($lungCapacity >= 4400 && $lungCapacity < 4650)
                        $lungCapacityScores = 80;
                    else if ($lungCapacity >= 4280 && $lungCapacity < 4400)
                        $lungCapacityScores = 78;
                    else if ($lungCapacity >= 4160 && $lungCapacity < 4280)
                        $lungCapacityScores = 76;
                    else if ($lungCapacity >= 4040 && $lungCapacity < 4160)
                        $lungCapacityScores = 74;
                    else if ($lungCapacity >= 3920 && $lungCapacity < 4040)
                        $lungCapacityScores = 72;
                    else if ($lungCapacity >= 3800 && $lungCapacity < 3920)
                        $lungCapacityScores = 70;
                    else if ($lungCapacity >= 3680 && $lungCapacity < 3800)
                        $lungCapacityScores = 68;
                    else if ($lungCapacity >= 3560 && $lungCapacity < 3680)
                        $lungCapacityScores = 66;
                    else if ($lungCapacity >= 3440 && $lungCapacity < 3560)
                        $lungCapacityScores = 64;
                    else if ($lungCapacity >= 3320 && $lungCapacity < 3440)
                        $lungCapacityScores = 62;
                    else if ($lungCapacity >= 3200 && $lungCapacity < 3320)
                        $lungCapacityScores = 60;
                    else if ($lungCapacity >= 3030 && $lungCapacity < 3200)
                        $lungCapacityScores = 50;
                    else if ($lungCapacity >= 2860 && $lungCapacity < 3030)
                        $lungCapacityScores = 40;
                    else if ($lungCapacity >= 2690 && $lungCapacity < 2860)
                        $lungCapacityScores = 30;
                    else if ($lungCapacity >= 2520 && $lungCapacity < 2690)
                        $lungCapacityScores = 20;
                    else if ($lungCapacity >= 2350 && $lungCapacity < 2520)
                        $lungCapacityScores = 10;

                }
                else
                {
                    if ($lungCapacity >= 3450)
                        $lungCapacityScores = 100;
                    else if ($lungCapacity >= 3400 && $lungCapacity < 3450)
                        $lungCapacityScores = 95;
                    else if ($lungCapacity >= 3350 && $lungCapacity < 3400)
                        $lungCapacityScores = 90;
                    else if ($lungCapacity >= 3200 && $lungCapacity < 3350)
                        $lungCapacityScores = 85;
                    else if ($lungCapacity >= 3050 && $lungCapacity < 3150)
                        $lungCapacityScores = 80;
                    else if ($lungCapacity >= 2950 && $lungCapacity < 3050)
                        $lungCapacityScores = 78;
                    else if ($lungCapacity >= 2850 && $lungCapacity < 2950)
                        $lungCapacityScores = 76;
                    else if ($lungCapacity >= 2750 && $lungCapacity < 2850)
                        $lungCapacityScores = 74;
                    else if ($lungCapacity >= 2650 && $lungCapacity < 2750)
                        $lungCapacityScores = 72;
                    else if ($lungCapacity >= 2550 && $lungCapacity < 2650)
                        $lungCapacityScores = 70;
                    else if ($lungCapacity >= 2450 && $lungCapacity < 2550)
                        $lungCapacityScores = 68;
                    else if ($lungCapacity >= 2350 && $lungCapacity < 2450)
                        $lungCapacityScores = 66;
                    else if ($lungCapacity >= 2250 && $lungCapacity < 2350)
                        $lungCapacityScores = 64;
                    else if ($lungCapacity >= 2150 && $lungCapacity < 2250)
                        $lungCapacityScores = 62;
                    else if ($lungCapacity >= 2050 && $lungCapacity < 2150)
                        $lungCapacityScores = 60;
                    else if ($lungCapacity >= 2010 && $lungCapacity < 2050)
                        $lungCapacityScores = 50;
                    else if ($lungCapacity >= 1970 && $lungCapacity < 2010)
                        $lungCapacityScores = 40;
                    else if ($lungCapacity >= 1930 && $lungCapacity < 1970)
                        $lungCapacityScores = 30;
                    else if ($lungCapacity >= 1890 && $lungCapacity < 1930)
                        $lungCapacityScores = 20;
                    else if ($lungCapacity >= 1850 && $lungCapacity < 1890)
                        $lungCapacityScores = 10;
                    else 
                        $lungCapacityScores = 0;
                }
            }

            return $lungCapacityScores;
        }

        // 根据50m跑成绩获得50m跑的得分
        public function getSprintRunningScores($sprintRunning)
        {
            $sprintRunningScores = 0.0;

            if ($this->gradeLevel == 12)
            {
                if ($this->sex == "m")
                {
                    if ($sprintRunning <= 6.7)
                        $sprintRunningScores = 100;
                    else if ($sprintRunning > 6.7 && $sprintRunning <= 6.8)
                        $sprintRunningScores = 95;
                    else if ($sprintRunning > 6.8 && $sprintRunning <= 6.9)
                        $sprintRunningScores = 90;
                    else if ($sprintRunning > 6.9 && $sprintRunning <= 7.0)
                        $sprintRunningScores = 85;
                    else if ($sprintRunning > 7.0 && $sprintRunning <= 7.1)
                        $sprintRunningScores = 80;
                    else if ($sprintRunning > 7.1 && $sprintRunning <= 7.3)
                        $sprintRunningScores = 78;
                    else if ($sprintRunning > 7.3 && $sprintRunning <= 7.5)
                        $sprintRunningScores = 76;
                    else if ($sprintRunning > 7.5 && $sprintRunning <= 7.7)
                        $sprintRunningScores = 74;
                    else if ($sprintRunning > 7.7 && $sprintRunning <= 7.9)
                        $sprintRunningScores = 72;
                    else if ($sprintRunning > 7.9 && $sprintRunning <= 8.1)
                        $sprintRunningScores = 70;
                    else if ($sprintRunning > 8.1 && $sprintRunning <= 8.3)
                        $sprintRunningScores = 68;
                    else if ($sprintRunning > 8.3 && $sprintRunning <= 8.5)
                        $sprintRunningScores = 66;
                    else if ($sprintRunning > 8.5 && $sprintRunning <= 8.7)
                        $sprintRunningScores = 64;
                    else if ($sprintRunning > 8.7 && $sprintRunning <= 8.9)
                        $sprintRunningScores = 62;
                    else if ($sprintRunning > 8.9 && $sprintRunning <= 9.1)
                        $sprintRunningScores = 60;
                    else if ($sprintRunning > 9.1 && $sprintRunning <= 9.3)
                        $sprintRunningScores = 50;
                    else if ($sprintRunning > 9.3 && $sprintRunning <= 9.5)
                        $sprintRunningScores = 40;
                    else if ($sprintRunning > 9.5 && $sprintRunning <= 9.7)
                        $sprintRunningScores = 30;
                    else if ($sprintRunning > 9.7 && $sprintRunning <= 9.9)
                        $sprintRunningScores = 20;
                    else if ($sprintRunning > 9.9 && $sprintRunning <= 10.1)
                        $sprintRunningScores = 10;
                    else
                        $sprintRunningScores = 0;
                }
                else
                {
                    if ($sprintRunning <= 7.5)
                        $sprintRunningScores = 100;
                    else if ($sprintRunning > 7.5 && $sprintRunning <= 7.6)
                        $sprintRunningScores = 95;
                    else if ($sprintRunning > 7.6 && $sprintRunning <= 7.7)
                        $sprintRunningScores = 90;
                    else if ($sprintRunning > 7.7 && $sprintRunning <= 8.0)
                        $sprintRunningScores = 85;
                    else if ($sprintRunning > 8.0 && $sprintRunning <= 8.3)
                        $sprintRunningScores = 80;
                    else if ($sprintRunning > 8.3 && $sprintRunning <= 8.5)
                        $sprintRunningScores = 78;
                    else if ($sprintRunning > 8.5 && $sprintRunning <= 8.7)
                        $sprintRunningScores = 76;
                    else if ($sprintRunning > 8.7 && $sprintRunning <= 8.9)
                        $sprintRunningScores = 74;
                    else if ($sprintRunning > 8.9 && $sprintRunning <= 9.1)
                        $sprintRunningScores = 72;
                    else if ($sprintRunning > 9.1 && $sprintRunning <= 9.3)
                        $sprintRunningScores = 70;
                    else if ($sprintRunning > 9.3 && $sprintRunning <= 9.5)
                        $sprintRunningScores = 68;
                    else if ($sprintRunning > 9.5 && $sprintRunning <= 9.7)
                        $sprintRunningScores = 66;
                    else if ($sprintRunning > 9.7 && $sprintRunning <= 9.9)
                        $sprintRunningScores = 64;
                    else if ($sprintRunning > 9.9 && $sprintRunning <= 10.1)
                        $sprintRunningScores = 62;
                    else if ($sprintRunning > 10.1 && $sprintRunning <= 10.3)
                        $sprintRunningScores = 60;
                    else if ($sprintRunning > 10.3 && $sprintRunning <= 10.5)
                        $sprintRunningScores = 50;
                    else if ($sprintRunning > 10.5 && $sprintRunning <= 10.7)
                        $sprintRunningScores = 40;
                    else if ($sprintRunning > 10.7 && $sprintRunning <= 10.9)
                        $sprintRunningScores = 30;
                    else if ($sprintRunning > 10.9 && $sprintRunning <= 11.1)
                        $sprintRunningScores = 20;
                    else if ($sprintRunning > 11.1 && $sprintRunning <= 11.3)
                        $sprintRunningScores = 10;
                    else 
                        $sprintRunningScores = 0;

                }
            }
            else
            {
                if ($this->sex == "m")
                {
                    if ($sprintRunning <= 6.6)
                        $sprintRunningScores = 100;
                    else if ($sprintRunning > 6.6 && $sprintRunning <= 6.7)
                        $sprintRunningScores = 95;
                    else if ($sprintRunning > 6.7 && $sprintRunning <= 6.8)
                        $sprintRunningScores = 90;
                    else if ($sprintRunning > 6.8 && $sprintRunning <= 6.9)
                        $sprintRunningScores = 85;
                    else if ($sprintRunning > 6.9 && $sprintRunning <= 7.0)
                        $sprintRunningScores = 80;
                    else if ($sprintRunning > 7.0 && $sprintRunning <= 7.2)
                        $sprintRunningScores = 78;
                    else if ($sprintRunning > 7.2 && $sprintRunning <= 7.4)
                        $sprintRunningScores = 76;
                    else if ($sprintRunning > 7.4 && $sprintRunning <= 7.6)
                        $sprintRunningScores = 74;
                    else if ($sprintRunning > 7.6 && $sprintRunning <= 7.8)
                        $sprintRunningScores = 72;
                    else if ($sprintRunning > 7.8 && $sprintRunning <= 8.0)
                        $sprintRunningScores = 70;
                    else if ($sprintRunning > 8.0 && $sprintRunning <= 8.2)
                        $sprintRunningScores = 68;
                    else if ($sprintRunning > 8.2 && $sprintRunning <= 8.4)
                        $sprintRunningScores = 66;
                    else if ($sprintRunning > 8.4 && $sprintRunning <= 8.6)
                        $sprintRunningScores = 64;
                    else if ($sprintRunning > 8.6 && $sprintRunning <= 8.8)
                        $sprintRunningScores = 62;
                    else if ($sprintRunning > 8.8 && $sprintRunning <= 9.0)
                        $sprintRunningScores = 60;
                    else if ($sprintRunning > 9.0 && $sprintRunning <= 9.2)
                        $sprintRunningScores = 50;
                    else if ($sprintRunning > 9.2 && $sprintRunning <= 9.4)
                        $sprintRunningScores = 40;
                    else if ($sprintRunning > 9.4 && $sprintRunning <= 9.6)
                        $sprintRunningScores = 30;
                    else if ($sprintRunning > 9.6 && $sprintRunning <= 9.8)
                        $sprintRunningScores = 20;
                    else if ($sprintRunning > 9.8 && $sprintRunning <= 10.0)
                        $sprintRunningScores = 10;
                    else
                        $sprintRunningScores = 0;

                }
                else
                {
                    if ($sprintRunning <= 7.4)
                        $sprintRunningScores = 100;
                    else if ($sprintRunning > 7.4 && $sprintRunning <= 7.5)
                        $sprintRunningScores = 95;
                    else if ($sprintRunning > 7.5 && $sprintRunning <= 7.6)
                        $sprintRunningScores = 90;
                    else if ($sprintRunning > 7.6 && $sprintRunning <= 7.9)
                        $sprintRunningScores = 85;
                    else if ($sprintRunning > 7.9 && $sprintRunning <= 8.2)
                        $sprintRunningScores = 80;
                    else if ($sprintRunning > 8.2 && $sprintRunning <= 8.4)
                        $sprintRunningScores = 78;
                    else if ($sprintRunning > 8.4 && $sprintRunning <= 8.6)
                        $sprintRunningScores = 76;
                    else if ($sprintRunning > 8.6 && $sprintRunning <= 8.8)
                        $sprintRunningScores = 74;
                    else if ($sprintRunning > 8.8 && $sprintRunning <= 9.0)
                        $sprintRunningScores = 72;
                    else if ($sprintRunning > 9.0 && $sprintRunning <= 9.2)
                        $sprintRunningScores = 70;
                    else if ($sprintRunning > 9.2 && $sprintRunning <= 9.4)
                        $sprintRunningScores = 68;
                    else if ($sprintRunning > 9.4 && $sprintRunning <= 9.6)
                        $sprintRunningScores = 66;
                    else if ($sprintRunning > 9.6 && $sprintRunning <= 9.8)
                        $sprintRunningScores = 64;
                    else if ($sprintRunning > 9.8 && $sprintRunning <= 10.0)
                        $sprintRunningScores = 62;
                    else if ($sprintRunning > 10.0 && $sprintRunning <= 10.2)
                        $sprintRunningScores = 60;
                    else if ($sprintRunning > 10.2 && $sprintRunning <= 10.4)
                        $sprintRunningScores = 50;
                    else if ($sprintRunning > 10.4 && $sprintRunning <= 10.6)
                        $sprintRunningScores = 40;
                    else if ($sprintRunning > 10.6 && $sprintRunning <= 10.8)
                        $sprintRunningScores = 30;
                    else if ($sprintRunning > 10.8 && $sprintRunning <= 11.0)
                        $sprintRunningScores = 20;
                    else if ($sprintRunning > 11.0 && $sprintRunning <= 11.2)
                        $sprintRunningScores = 10;
                    else
                        $sprintRunningScores = 0;

                }
            }

            return $sprintRunningScores;
        }

        // 根据坐位体前屈成绩信息获得坐位体前屈的得分
        public function getSitAndReachScores($sitAndReach)
        {
            $sitAndReachScores = 0.0;

            if ($this->gradeLevel == 12)
            {
                if ($this->sex == "m")
                {
                    if ($sitAndReach >= 24.9)
                        $sitAndReachScores = 100;
                    else if ($sitAndReach >= 23.1 && $sitAndReach < 24.9)
                        $sitAndReachScores = 95;
                    else if ($sitAndReach >= 21.3 && $sitAndReach < 23.1)
                        $sitAndReachScores = 90;
                    else if ($sitAndReach >= 19.5 && $sitAndReach < 21.3)
                        $sitAndReachScores = 85;
                    else if ($sitAndReach >= 17.7 && $sitAndReach < 19.5)
                        $sitAndReachScores = 80;
                    else if ($sitAndReach >= 16.3 && $sitAndReach < 17.7)
                        $sitAndReachScores = 78;
                    else if ($sitAndReach >= 14.9 && $sitAndReach < 16.3)
                        $sitAndReachScores = 76;
                    else if ($sitAndReach >= 13.5 && $sitAndReach < 14.9)
                        $sitAndReachScores = 74;
                    else if ($sitAndReach >= 12.1 && $sitAndReach < 13.5)
                        $sitAndReachScores = 72;
                    else if ($sitAndReach >= 10.7 && $sitAndReach < 12.1)
                        $sitAndReachScores = 70;
                    else if ($sitAndReach >= 9.3 && $sitAndReach < 10.7)
                        $sitAndReachScores = 68;
                    else if ($sitAndReach >= 7.9 && $sitAndReach < 9.3)
                        $sitAndReachScores = 66;
                    else if ($sitAndReach >= 6.5 && $sitAndReach < 7.9)
                        $sitAndReachScores = 64;
                    else if ($sitAndReach >= 5.1 && $sitAndReach < 6.5)
                        $sitAndReachScores = 62;
                    else if ($sitAndReach >= 3.7 && $sitAndReach < 5.1)
                        $sitAndReachScores = 60;
                    else if ($sitAndReach >= 2.7 && $sitAndReach < 3.7)
                        $sitAndReachScores = 50;
                    else if ($sitAndReach >= 1.7 && $sitAndReach < 2.7)
                        $sitAndReachScores = 40;
                    else if ($sitAndReach >= 0.7 && $sitAndReach < 1.7)
                        $sitAndReachScores = 30;
                    else if ($sitAndReach >= -0.3 && $sitAndReach < 0.7)
                        $sitAndReachScores = 20;
                    else if ($sitAndReach >= -1.3 && $sitAndReach < -0.3)
                        $sitAndReachScores = 10;
                    else 
                        $sitAndReachScores = 0;

                 }
                 else
                 {
                    if ($sitAndReach >= 25.8)
                        $sitAndReachScores = 100;
                    else if ($sitAndReach >= 24.0 && $sitAndReach < 25.8)
                        $sitAndReachScores = 95;
                    else if ($sitAndReach >= 22.2 && $sitAndReach < 24.0)
                        $sitAndReachScores = 90;
                    else if ($sitAndReach >= 20.6 && $sitAndReach < 22.2)
                        $sitAndReachScores = 85;
                    else if ($sitAndReach >= 19.0 && $sitAndReach < 20.6)
                        $sitAndReachScores = 80;
                    else if ($sitAndReach >= 17.7 && $sitAndReach < 19.0)
                        $sitAndReachScores = 78;
                    else if ($sitAndReach >= 16.4 && $sitAndReach < 17.7)
                        $sitAndReachScores = 76;
                    else if ($sitAndReach >= 15.1 && $sitAndReach < 16.4)
                        $sitAndReachScores = 74;
                    else if ($sitAndReach >= 13.8 && $sitAndReach < 15.1)
                        $sitAndReachScores = 72;
                    else if ($sitAndReach >= 12.5 && $sitAndReach < 13.8)
                        $sitAndReachScores = 70;
                    else if ($sitAndReach >= 11.2 && $sitAndReach < 12.5)
                        $sitAndReachScores = 68;
                    else if ($sitAndReach >= 9.9 && $sitAndReach < 11.2)
                        $sitAndReachScores = 66;
                    else if ($sitAndReach >= 8.6 && $sitAndReach < 9.9)
                        $sitAndReachScores = 64;
                    else if ($sitAndReach >= 7.3 && $sitAndReach < 8.6)
                        $sitAndReachScores = 62;
                    else if ($sitAndReach >= 6.0 && $sitAndReach < 7.3)
                        $sitAndReachScores = 60;
                    else if ($sitAndReach >= 5.2 && $sitAndReach < 6.0)
                        $sitAndReachScores = 50;
                    else if ($sitAndReach >= 4.4 && $sitAndReach < 5.2)
                        $sitAndReachScores = 40;
                    else if ($sitAndReach >= 3.6 && $sitAndReach < 4.4)
                        $sitAndReachScores = 30;
                    else if ($sitAndReach >= 2.8 && $sitAndReach < 3.6)
                        $sitAndReachScores = 20;
                    else if ($sitAndReach >= 2.0 && $sitAndReach < 2.8)
                        $sitAndReachScores = 10;
                    else
                        $sitAndReachScores = 0;

                 }
             }
             else 
             {
                 if ($this->sex == "m")
                 {
                    if ($sitAndReach >= 25.1)
                        $sitAndReachScores = 100;
                    else if ($sitAndReach >= 23.3 && $sitAndReach < 25.1)
                        $sitAndReachScores = 95;
                    else if ($sitAndReach >= 21.5 && $sitAndReach < 23.3)
                        $sitAndReachScores = 90;
                    else if ($sitAndReach >= 19.9 && $sitAndReach < 21.5)
                        $sitAndReachScores = 85;
                    else if ($sitAndReach >= 18.2 && $sitAndReach < 19.9)
                        $sitAndReachScores = 80;
                    else if ($sitAndReach >= 16.8 && $sitAndReach < 18.2)
                        $sitAndReachScores = 78;
                    else if ($sitAndReach >= 15.4 && $sitAndReach < 16.8)
                        $sitAndReachScores = 76;
                    else if ($sitAndReach >= 14.0 && $sitAndReach < 15.4)
                        $sitAndReachScores = 74;
                    else if ($sitAndReach >= 12.6 && $sitAndReach < 14.0)
                        $sitAndReachScores = 72;
                    else if ($sitAndReach >= 11.2 && $sitAndReach < 12.6)
                        $sitAndReachScores = 70;
                    else if ($sitAndReach >= 9.8 && $sitAndReach < 11.2)
                        $sitAndReachScores = 68;
                    else if ($sitAndReach >= 8.4 && $sitAndReach < 9.8)
                        $sitAndReachScores = 66;
                    else if ($sitAndReach >= 7.0 && $sitAndReach < 8.4)
                        $sitAndReachScores = 64;
                    else if ($sitAndReach >= 5.6 && $sitAndReach < 7.0)
                        $sitAndReachScores = 62;
                    else if ($sitAndReach >= 4.2 && $sitAndReach < 5.6)
                        $sitAndReachScores = 60;
                    else if ($sitAndReach >= 3.2 && $sitAndReach < 4.2)
                        $sitAndReachScores = 50;
                    else if ($sitAndReach >= 2.2 && $sitAndReach < 3.2)
                        $sitAndReachScores = 40;
                    else if ($sitAndReach >= 1.2 && $sitAndReach < 2.2)
                        $sitAndReachScores = 30;
                    else if ($sitAndReach >= 0.2 && $sitAndReach < 1.2)
                        $sitAndReachScores = 20;
                    else if ($sitAndReach >= -0.8 && $sitAndReach < 0.2)
                        $sitAndReachScores = 10;
                    else
                        $sitAndReachScores = 0;
                 }
                 else
                 {
                    if ($sitAndReach >= 26.3)
                        $sitAndReachScores = 100;
                    else if ($sitAndReach >= 24.4 && $sitAndReach < 26.3)
                        $sitAndReachScores = 95;
                    else if ($sitAndReach >= 22.4 && $sitAndReach < 24.0)
                        $sitAndReachScores = 90;
                    else if ($sitAndReach >= 21.0 && $sitAndReach < 22.4)
                        $sitAndReachScores = 85;
                    else if ($sitAndReach >= 19.5 && $sitAndReach < 21.0)
                        $sitAndReachScores = 80;
                    else if ($sitAndReach >= 18.2 && $sitAndReach < 19.5)
                        $sitAndReachScores = 78;
                    else if ($sitAndReach >= 16.9 && $sitAndReach < 18.2)
                        $sitAndReachScores = 76;
                    else if ($sitAndReach >= 15.6 && $sitAndReach < 16.9)
                        $sitAndReachScores = 74;
                    else if ($sitAndReach >= 14.3 && $sitAndReach < 15.6)
                        $sitAndReachScores = 72;
                    else if ($sitAndReach >= 13.0 && $sitAndReach < 14.3)
                        $sitAndReachScores = 70;
                    else if ($sitAndReach >= 11.7 && $sitAndReach < 13.0)
                        $sitAndReachScores = 68;
                    else if ($sitAndReach >= 10.4 && $sitAndReach < 11.7)
                        $sitAndReachScores = 66;
                    else if ($sitAndReach >= 9.1 && $sitAndReach < 10.4)
                        $sitAndReachScores = 64;
                    else if ($sitAndReach >= 7.8 && $sitAndReach < 9.1)
                        $sitAndReachScores = 62;
                    else if ($sitAndReach >= 6.5 && $sitAndReach < 7.8)
                        $sitAndReachScores = 60;
                    else if ($sitAndReach >= 5.7 && $sitAndReach < 6.5)
                        $sitAndReachScores = 50;
                    else if ($sitAndReach >= 4.9 && $sitAndReach < 5.7)
                        $sitAndReachScores = 40;
                    else if ($sitAndReach >= 4.1 && $sitAndReach < 4.9)
                        $sitAndReachScores = 30;
                    else if ($sitAndReach >= 3.3 && $sitAndReach < 4.1)
                        $sitAndReachScores = 20;
                    else if ($sitAndReach >= 2.5 && $sitAndReach < 3.3)
                        $sitAndReachScores = 10;
                    else
                        $sitAndReachScores = 0;
                 }
             }
           return $sitAndReachScores; 
        }

        // 根据立定跳远成绩信息获得立定跳远得分
        public function getStandingJumpingScores($standingJumping)
        {
            $standingJumpingScores = 0.0;

            if ($this->gradeLevel == 12)
            {
                if ($this->sex == "m")
                {
                    if ($standingJumping >= 273)
                        $standingJumpingScores = 100;
                    else if ($standingJumping >= 268 && $standingJumping < 273)
                        $standingJumpingScores = 95;
                    else if ($standingJumping >= 263 && $standingJumping < 268)
                        $standingJumpingScores = 90;
                    else if ($standingJumping >= 256 && $standingJumping < 263)
                        $standingJumpingScores = 85;
                    else if ($standingJumping >= 248 && $standingJumping < 256)
                        $standingJumpingScores = 80;
                    else if ($standingJumping >= 244 && $standingJumping < 248)
                        $standingJumpingScores = 78;
                    else if ($standingJumping >= 240 && $standingJumping < 244)
                        $standingJumpingScores = 76;
                    else if ($standingJumping >= 236 && $standingJumping < 240)
                        $standingJumpingScores = 74;
                    else if ($standingJumping >= 232 && $standingJumping < 236)
                        $standingJumpingScores = 72;
                    else if ($standingJumping >= 228 && $standingJumping < 232)
                        $standingJumpingScores = 70;
                    else if ($standingJumping >= 224 && $standingJumping < 228)
                        $standingJumpingScores = 68;
                    else if ($standingJumping >= 220 && $standingJumping < 224)
                        $standingJumpingScores = 66;
                    else if ($standingJumping >= 216 && $standingJumping < 220)
                        $standingJumpingScores = 64;
                    else if ($standingJumping >= 212 && $standingJumping < 216)
                        $standingJumpingScores = 62;
                    else if ($standingJumping >= 208 && $standingJumping < 212)
                        $standingJumpingScores = 60;
                    else if ($standingJumping >= 203 && $standingJumping < 208)
                        $standingJumpingScores = 50;
                    else if ($standingJumping >= 198 && $standingJumping < 203)
                        $standingJumpingScores = 40;
                    else if ($standingJumping >= 193 && $standingJumping < 198)
                        $standingJumpingScores = 30;
                    else if ($standingJumping >= 188 && $standingJumping < 193)
                        $standingJumpingScores = 20;
                    else if ($standingJumping >= 183 && $standingJumping < 188)
                        $standingJumpingScores = 10;
                    else
                        $standingJumpingScores = 0;
                }
                else
                {
                    if ($standingJumping >= 207)
                        $standingJumpingScores = 100;
                    else if ($standingJumping >= 201 && $standingJumping < 207)
                        $standingJumpingScores = 95;
                    else if ($standingJumping >= 195 && $standingJumping < 201)
                        $standingJumpingScores = 90;
                    else if ($standingJumping >= 188 && $standingJumping < 195)
                        $standingJumpingScores = 85;
                    else if ($standingJumping >= 181 && $standingJumping < 188)
                        $standingJumpingScores = 80;
                    else if ($standingJumping >= 178 && $standingJumping < 181)
                        $standingJumpingScores = 78;
                    else if ($standingJumping >= 175 && $standingJumping < 178)
                        $standingJumpingScores = 76;
                    else if ($standingJumping >= 172 && $standingJumping < 175)
                        $standingJumpingScores = 74;
                    else if ($standingJumping >= 169 && $standingJumping < 172)
                        $standingJumpingScores = 72;
                    else if ($standingJumping >= 166 && $standingJumping < 169)
                        $standingJumpingScores = 70;
                    else if ($standingJumping >= 163 && $standingJumping < 166)
                        $standingJumpingScores = 68;
                    else if ($standingJumping >= 160 && $standingJumping < 163)
                        $standingJumpingScores = 66;
                    else if ($standingJumping >= 157 && $standingJumping < 160)
                        $standingJumpingScores = 64;
                    else if ($standingJumping >= 154 && $standingJumping < 157)
                        $standingJumpingScores = 62;
                    else if ($standingJumping >= 151 && $standingJumping < 154)
                        $standingJumpingScores = 60;
                    else if ($standingJumping >= 146 && $standingJumping < 151)
                        $standingJumpingScores = 50;
                    else if ($standingJumping >= 141 && $standingJumping < 146)
                        $standingJumpingScores = 40;
                    else if ($standingJumping >= 136 && $standingJumping < 141)
                        $standingJumpingScores = 30;
                    else if ($standingJumping >= 131 && $standingJumping < 136)
                        $standingJumpingScores = 20;
                    else if ($standingJumping >= 126 && $standingJumping < 131)
                        $standingJumpingScores = 10;
                    else
                        $standingJumpingScores = 0;
                }
            }
            else
            {
                if ($this->sex == "m")
                {
                    if ($standingJumping >= 275)
                        $standingJumpingScores = 100;
                    else if ($standingJumping >= 270 && $standingJumping < 275)
                        $standingJumpingScores = 95;
                    else if ($standingJumping >= 265 && $standingJumping < 270)
                        $standingJumpingScores = 90;
                    else if ($standingJumping >= 258 && $standingJumping < 265)
                        $standingJumpingScores = 85;
                    else if ($standingJumping >= 250 && $standingJumping < 258)
                        $standingJumpingScores = 80;
                    else if ($standingJumping >= 246 && $standingJumping < 250)
                        $standingJumpingScores = 78;
                    else if ($standingJumping >= 242 && $standingJumping < 246)
                        $standingJumpingScores = 76;
                    else if ($standingJumping >= 238 && $standingJumping < 242)
                        $standingJumpingScores = 74;
                    else if ($standingJumping >= 234 && $standingJumping < 238)
                        $standingJumpingScores = 72;
                    else if ($standingJumping >= 230 && $standingJumping < 234)
                        $standingJumpingScores = 70;
                    else if ($standingJumping >= 226 && $standingJumping < 230)
                        $standingJumpingScores = 68;
                    else if ($standingJumping >= 222 && $standingJumping < 226)
                        $standingJumpingScores = 66;
                    else if ($standingJumping >= 218 && $standingJumping < 222)
                        $standingJumpingScores = 64;
                    else if ($standingJumping >= 214 && $standingJumping < 218)
                        $standingJumpingScores = 62;
                    else if ($standingJumping >= 210 && $standingJumping < 214)
                        $standingJumpingScores = 60;
                    else if ($standingJumping >= 205 && $standingJumping < 210)
                        $standingJumpingScores = 50;
                    else if ($standingJumping >= 200 && $standingJumping < 205)
                        $standingJumpingScores = 40;
                    else if ($standingJumping >= 195 && $standingJumping < 200)
                        $standingJumpingScores = 30;
                    else if ($standingJumping >= 190 && $standingJumping < 195)
                        $standingJumpingScores = 20;
                    else if ($standingJumping >= 185 && $standingJumping < 190)
                        $standingJumpingScores = 10;
                    else
                        $standingJumpingScores = 0;
                }
                else
                {
                    if ($standingJumping >= 208)
                        $standingJumpingScores = 100;
                    else if ($standingJumping >= 202 && $standingJumping < 208)
                        $standingJumpingScores = 95;
                    else if ($standingJumping >= 196 && $standingJumping < 202)
                        $standingJumpingScores = 90;
                    else if ($standingJumping >= 189 && $standingJumping < 196)
                        $standingJumpingScores = 85;
                    else if ($standingJumping >= 182 && $standingJumping < 189)
                        $standingJumpingScores = 80;
                    else if ($standingJumping >= 179 && $standingJumping < 182)
                        $standingJumpingScores = 78;
                    else if ($standingJumping >= 176 && $standingJumping < 179)
                        $standingJumpingScores = 76;
                    else if ($standingJumping >= 173 && $standingJumping < 176)
                        $standingJumpingScores = 74;
                    else if ($standingJumping >= 170 && $standingJumping < 173)
                        $standingJumpingScores = 72;
                    else if ($standingJumping >= 167 && $standingJumping < 170)
                        $standingJumpingScores = 70;
                    else if ($standingJumping >= 164 && $standingJumping < 167)
                        $standingJumpingScores = 68;
                    else if ($standingJumping >= 161 && $standingJumping < 164)
                        $standingJumpingScores = 66;
                    else if ($standingJumping >= 158 && $standingJumping < 161)
                        $standingJumpingScores = 64;
                    else if ($standingJumping >= 155 && $standingJumping < 158)
                        $standingJumpingScores = 62;
                    else if ($standingJumping >= 152 && $standingJumping < 155)
                        $standingJumpingScores = 60;
                    else if ($standingJumping >= 147 && $standingJumping < 152)
                        $standingJumpingScores = 50;
                    else if ($standingJumping >= 142 && $standingJumping < 147)
                        $standingJumpingScores = 40;
                    else if ($standingJumping >= 137 && $standingJumping < 142)
                        $standingJumpingScores = 30;
                    else if ($standingJumping >= 132 && $standingJumping < 137)
                        $standingJumpingScores = 20;
                    else if ($standingJumping >= 127 && $standingJumping < 132)
                        $standingJumpingScores = 10;
                    else
                        $standingJumpingScores = 0;
                }
            }

            return $standingJumpingScores;
        }

        // 根据男生引体向上的成绩信息获得引体向上的得分
        public function getChinningScores($chinning)
        {
            $chinningScores = 0.0;

            if ($this->gradeLevel == 12)
            {
                if ($chinning >= 19)
                        $chinningScores = 100;
                    else if ($chinning >= 18 && $chinning < 19)
                        $chinningScores = 95;
                    else if ($chinning >= 17 && $chinning < 18)
                        $chinningScores = 90;
                    else if ($chinning >= 16 && $chinning < 17)
                        $chinningScores = 85;
                    else if ($chinning >= 15 && $chinning < 16)
                        $chinningScores = 80;
                    else if ($chinning >= 14 && $chinning < 15)
                        $chinningScores = 76;
                    else if ($chinning >= 13 && $chinning < 14)
                        $chinningScores = 72;
                    else if ($chinning >= 12 && $chinning < 13)
                        $chinningScores = 68;
                    else if ($chinning >= 11 && $chinning < 12)
                        $chinningScores = 64;
                    else if ($chinning >= 10 && $chinning < 11)
                        $chinningScores = 60;
                    else if ($chinning >= 9 && $chinning < 10)
                        $chinningScores = 50;
                    else if ($chinning >= 8 && $chinning < 9)
                        $chinningScores = 40;
                    else if ($chinning >= 7 && $chinning < 8)
                        $chinningScores = 30;
                    else if ($chinning >= 6 && $chinning < 7)
                        $chinningScores = 20;
                    else if ($chinning >= 5 && $chinning < 6)
                        $chinningScores = 10;
                    else
                        $chinningScores = 0;
            }
            else
            {
                if ($chinning >= 20)
                        $chinningScores = 100;
                    else if ($chinning >= 19 && $chinning < 20)
                        $chinningScores = 95;
                    else if ($chinning >= 18 && $chinning < 19)
                        $chinningScores = 90;
                    else if ($chinning >= 17 && $chinning < 18)
                        $chinningScores = 85;
                    else if ($chinning >= 16 && $chinning < 17)
                        $chinningScores = 80;
                    else if ($chinning >= 15 && $chinning < 16)
                        $chinningScores = 76;
                    else if ($chinning >= 14 && $chinning < 15)
                        $chinningScores = 72;
                    else if ($chinning >= 13 && $chinning < 14)
                        $chinningScores = 68;
                    else if ($chinning >= 12 && $chinning < 13)
                        $chinningScores = 64;
                    else if ($chinning >= 11 && $chinning < 12)
                        $chinningScores = 60;
                    else if ($chinning >= 10 && $chinning < 11)
                        $chinningScores = 50;
                    else if ($chinning >= 9 && $chinning < 10)
                        $chinningScores = 40;
                    else if ($chinning >= 8 && $chinning < 9)
                        $chinningScores = 30;
                    else if ($chinning >= 7 && $chinning < 8)
                        $chinningScores = 20;
                    else if ($chinning >= 6 && $chinning < 7)
                        $chinningScores = 10;
                    else
                        $chinningScores = 0;
            }

            return $chinningScores;
        }

        // 根据女生一分钟仰卧起坐成绩信息获得一分钟仰卧起坐的得分
        public function getSitupScores($situp)
        {
            $situpScores = 0.0;

            if ($this->gradeLevel == 12)
            {
                if ($situp >= 56)
                        $situpScores = 100;
                    else if ($situp >= 54 && $situp < 56)
                        $situpScores = 95;
                    else if ($situp >= 52 && $situp < 54)
                        $situpScores = 90;
                    else if ($situp >= 49 && $situp < 52)
                        $situpScores = 85;
                    else if ($situp >= 46 && $situp < 49)
                        $situpScores = 80;
                    else if ($situp >= 44 && $situp < 46)
                        $situpScores = 78;
                    else if ($situp >= 42 && $situp < 44)
                        $situpScores = 76;
                    else if ($situp >= 40 && $situp < 42)
                        $situpScores = 74;
                    else if ($situp >= 38 && $situp < 40)
                        $situpScores = 72;
                    else if ($situp >= 36 && $situp < 38)
                        $situpScores = 70;
                    else if ($situp >= 34 && $situp < 36)
                        $situpScores = 68;
                    else if ($situp >= 32 && $situp < 34)
                        $situpScores = 66;
                    else if ($situp >= 30 && $situp < 32)
                        $situpScores = 64;
                    else if ($situp >= 28 && $situp < 30)
                        $situpScores = 62;
                    else if ($situp >= 26 && $situp < 28)
                        $situpScores = 60;
                    else if ($situp >= 24 && $situp < 26)
                        $situpScores = 50;
                    else if ($situp >= 22 && $situp < 24)
                        $situpScores = 40;
                    else if ($situp >= 20 && $situp < 22)
                        $situpScores = 30;
                    else if ($situp >= 18 && $situp < 20)
                        $situpScores = 20;
                    else if ($situp >= 16 && $situp < 18)
                        $situpScores = 10;
                    else
                        $situpScores = 0;
            }
            else
            {
                if ($situp >= 57)
                        $situpScores = 100;
                    else if ($situp >= 55 && $situp < 57)
                        $situpScores = 95;
                    else if ($situp >= 53 && $situp < 55)
                        $situpScores = 90;
                    else if ($situp >= 50 && $situp < 53)
                        $situpScores = 85;
                    else if ($situp >= 47 && $situp < 50)
                        $situpScores = 80;
                    else if ($situp >= 45 && $situp < 47)
                        $situpScores = 78;
                    else if ($situp >= 43 && $situp < 45)
                        $situpScores = 76;
                    else if ($situp >= 41 && $situp < 43)
                        $situpScores = 74;
                    else if ($situp >= 39 && $situp < 41)
                        $situpScores = 72;
                    else if ($situp >= 37 && $situp < 39)
                        $situpScores = 70;
                    else if ($situp >= 35 && $situp < 37)
                        $situpScores = 68;
                    else if ($situp >= 33 && $situp < 35)
                        $situpScores = 66;
                    else if ($situp >= 31 && $situp < 33)
                        $situpScores = 64;
                    else if ($situp >= 29 && $situp < 31)
                        $situpScores = 62;
                    else if ($situp >= 27 && $situp < 29)
                        $situpScores = 60;
                    else if ($situp >= 25 && $situp < 27)
                        $situpScores = 50;
                    else if ($situp >= 23 && $situp < 25)
                        $situpScores = 40;
                    else if ($situp >= 21 && $situp < 23)
                        $situpScores = 30;
                    else if ($situp >= 19 && $situp < 21)
                        $situpScores = 20;
                    else if ($situp >= 17 && $situp < 19)
                        $situpScores = 10;
                    else
                        $situpScores = 0;
            }

            return $situpScores;
        }

        // 根据耐力跑成绩信息获得耐力跑的得分
        public function getLongDistanceRunningScores($longDistanceRunning)
        {
             $longDistanceRunningScores = 0.0;

             if ($this->gradeLevel == 12)
             {
                if ($this->sex == "m")
                {
                    if ($longDistanceRunning <= 197)
                        $longDistanceRunningScores = 100;
                    else if ($longDistanceRunning > 197 && $longDistanceRunning <= 202)
                        $longDistanceRunningScores = 95;
                    else if ($longDistanceRunning > 202 && $longDistanceRunning <= 207)
                        $longDistanceRunningScores = 90;
                    else if ($longDistanceRunning > 207 && $longDistanceRunning <= 214)
                        $longDistanceRunningScores = 85;
                    else if ($longDistanceRunning > 214 && $longDistanceRunning <= 222)
                        $longDistanceRunningScores = 80;
                    else if ($longDistanceRunning > 222 && $longDistanceRunning <= 227)
                        $longDistanceRunningScores = 78;
                    else if ($longDistanceRunning > 227 && $longDistanceRunning <= 232)
                        $longDistanceRunningScores = 76;
                    else if ($longDistanceRunning > 232 && $longDistanceRunning <= 237)
                        $longDistanceRunningScores = 74;
                    else if ($longDistanceRunning > 237 && $longDistanceRunning <= 242)
                        $longDistanceRunningScores = 72;
                    else if ($longDistanceRunning > 242 && $longDistanceRunning <= 247)
                        $longDistanceRunningScores = 70;
                    else if ($longDistanceRunning > 247 && $longDistanceRunning <= 252)
                        $longDistanceRunningScores = 68;
                    else if ($longDistanceRunning > 252 && $longDistanceRunning <= 257)
                        $longDistanceRunningScores = 66;
                    else if ($longDistanceRunning > 257 && $longDistanceRunning <= 262)
                        $longDistanceRunningScores = 64;
                    else if ($longDistanceRunning > 262 && $longDistanceRunning <= 267)
                        $longDistanceRunningScores = 62;
                    else if ($longDistanceRunning > 267 && $longDistanceRunning <= 272)
                        $longDistanceRunningScores = 60;
                    else if ($longDistanceRunning > 272 && $longDistanceRunning <= 292)
                        $longDistanceRunningScores = 50;
                    else if ($longDistanceRunning > 292 && $longDistanceRunning <= 312)
                        $longDistanceRunningScores = 40;
                    else if ($longDistanceRunning > 312 && $longDistanceRunning <= 332)
                        $longDistanceRunningScores = 30;
                    else if ($longDistanceRunning > 332 && $longDistanceRunning <= 352)
                        $longDistanceRunningScores = 20;
                    else if ($longDistanceRunning > 352 && $longDistanceRunning <= 372)
                        $longDistanceRunningScores = 10;
                    else 
                        $longDistanceRunningScores = 0;
                }
                else
                {
                    if ($longDistanceRunning <= 198)
                        $longDistanceRunningScores = 100;
                    else if ($longDistanceRunning > 198 && $longDistanceRunning <= 204)
                        $longDistanceRunningScores = 95;
                    else if ($longDistanceRunning > 204 && $longDistanceRunning <= 210)
                        $longDistanceRunningScores = 90;
                    else if ($longDistanceRunning > 210 && $longDistanceRunning <= 217)
                        $longDistanceRunningScores = 85;
                    else if ($longDistanceRunning > 217 && $longDistanceRunning <= 224)
                        $longDistanceRunningScores = 80;
                    else if ($longDistanceRunning > 224 && $longDistanceRunning <= 229)
                        $longDistanceRunningScores = 78;
                    else if ($longDistanceRunning > 229 && $longDistanceRunning <= 234)
                        $longDistanceRunningScores = 76;
                    else if ($longDistanceRunning > 234 && $longDistanceRunning <= 239)
                        $longDistanceRunningScores = 74;
                    else if ($longDistanceRunning > 239 && $longDistanceRunning <= 244)
                        $longDistanceRunningScores = 72;
                    else if ($longDistanceRunning > 244 && $longDistanceRunning <= 249)
                        $longDistanceRunningScores = 70;
                    else if ($longDistanceRunning > 249 && $longDistanceRunning <= 254)
                        $longDistanceRunningScores = 68;
                    else if ($longDistanceRunning > 254 && $longDistanceRunning <= 259)
                        $longDistanceRunningScores = 66;
                    else if ($longDistanceRunning > 259 && $longDistanceRunning <= 264)
                        $longDistanceRunningScores = 64;
                    else if ($longDistanceRunning > 264 && $longDistanceRunning <= 269)
                        $longDistanceRunningScores = 62;
                    else if ($longDistanceRunning > 269 && $longDistanceRunning <= 274)
                        $longDistanceRunningScores = 60;
                    else if ($longDistanceRunning > 274 && $longDistanceRunning <= 284)
                        $longDistanceRunningScores = 50;
                    else if ($longDistanceRunning > 284 && $longDistanceRunning <= 294)
                        $longDistanceRunningScores = 40;
                    else if ($longDistanceRunning > 294 && $longDistanceRunning <= 304)
                        $longDistanceRunningScores = 30;
                    else if ($longDistanceRunning > 304 && $longDistanceRunning <= 314)
                        $longDistanceRunningScores = 20;
                    else if ($longDistanceRunning > 314 && $longDistanceRunning <= 324)
                        $longDistanceRunningScores = 10;
                    else
                        $longDistanceRunningScores = 0;
                }
             }
             else
             {
                if ($this->sex == "m")
                {
                    if ($longDistanceRunning <= 195)
                        $longDistanceRunningScores = 100;
                    else if ($longDistanceRunning > 195 && $longDistanceRunning <= 200)
                        $longDistanceRunningScores = 95;
                    else if ($longDistanceRunning > 200 && $longDistanceRunning <= 205)
                        $longDistanceRunningScores = 90;
                    else if ($longDistanceRunning > 205 && $longDistanceRunning <= 212)
                        $longDistanceRunningScores = 85;
                    else if ($longDistanceRunning > 212 && $longDistanceRunning <= 220)
                        $longDistanceRunningScores = 80;
                    else if ($longDistanceRunning > 220 && $longDistanceRunning <= 225)
                        $longDistanceRunningScores = 78;
                    else if ($longDistanceRunning > 225 && $longDistanceRunning <= 230)
                        $longDistanceRunningScores = 76;
                    else if ($longDistanceRunning > 230 && $longDistanceRunning <= 235)
                        $longDistanceRunningScores = 74;
                    else if ($longDistanceRunning > 235 && $longDistanceRunning <= 240)
                        $longDistanceRunningScores = 72;
                    else if ($longDistanceRunning > 240 && $longDistanceRunning <= 245)
                        $longDistanceRunningScores = 70;
                    else if ($longDistanceRunning > 245 && $longDistanceRunning <= 250)
                        $longDistanceRunningScores = 68;
                    else if ($longDistanceRunning > 250 && $longDistanceRunning <= 255)
                        $longDistanceRunningScores = 66;
                    else if ($longDistanceRunning > 255 && $longDistanceRunning <= 260)
                        $longDistanceRunningScores = 64;
                    else if ($longDistanceRunning > 260 && $longDistanceRunning <= 265)
                        $longDistanceRunningScores = 62;
                    else if ($longDistanceRunning > 265 && $longDistanceRunning <= 270)
                        $longDistanceRunningScores = 60;
                    else if ($longDistanceRunning > 270 && $longDistanceRunning <= 290)
                        $longDistanceRunningScores = 50;
                    else if ($longDistanceRunning > 290 && $longDistanceRunning <= 310)
                        $longDistanceRunningScores = 40;
                    else if ($longDistanceRunning > 310 && $longDistanceRunning <= 330)
                        $longDistanceRunningScores = 30;
                    else if ($longDistanceRunning > 330 && $longDistanceRunning <= 350)
                        $longDistanceRunningScores = 20;
                    else if ($longDistanceRunning > 350 && $longDistanceRunning <= 370)
                        $longDistanceRunningScores = 10;
                    else
                        $longDistanceRunningScores = 0;

                }
                else
                {
                    if ($longDistanceRunning <= 196)
                        $longDistanceRunningScores = 100;
                    else if ($longDistanceRunning > 196 && $longDistanceRunning <= 202)
                        $longDistanceRunningScores = 95;
                    else if ($longDistanceRunning > 202 && $longDistanceRunning <= 208)
                        $longDistanceRunningScores = 90;
                    else if ($longDistanceRunning > 208 && $longDistanceRunning <= 215)
                        $longDistanceRunningScores = 85;
                    else if ($longDistanceRunning > 215 && $longDistanceRunning <= 222)
                        $longDistanceRunningScores = 80;
                    else if ($longDistanceRunning > 222 && $longDistanceRunning <= 227)
                        $longDistanceRunningScores = 78;
                    else if ($longDistanceRunning > 227 && $longDistanceRunning <= 232)
                        $longDistanceRunningScores = 76;
                    else if ($longDistanceRunning > 232 && $longDistanceRunning <= 237)
                        $longDistanceRunningScores = 74;
                    else if ($longDistanceRunning > 237 && $longDistanceRunning <= 242)
                        $longDistanceRunningScores = 72;
                    else if ($longDistanceRunning > 242 && $longDistanceRunning <= 247)
                        $longDistanceRunningScores = 70;
                    else if ($longDistanceRunning > 247 && $longDistanceRunning <= 252)
                        $longDistanceRunningScores = 68;
                    else if ($longDistanceRunning > 252 && $longDistanceRunning <= 257)
                        $longDistanceRunningScores = 66;
                    else if ($longDistanceRunning > 257 && $longDistanceRunning <= 262)
                        $longDistanceRunningScores = 64;
                    else if ($longDistanceRunning > 262 && $longDistanceRunning <= 267)
                        $longDistanceRunningScores = 62;
                    else if ($longDistanceRunning > 267 && $longDistanceRunning <= 272)
                        $longDistanceRunningScores = 60;
                    else if ($longDistanceRunning > 272 && $longDistanceRunning <= 282)
                        $longDistanceRunningScores = 50;
                    else if ($longDistanceRunning > 282 && $longDistanceRunning <= 292)
                        $longDistanceRunningScores = 40;
                    else if ($longDistanceRunning > 292 && $longDistanceRunning <= 302)
                        $longDistanceRunningScores = 30;
                    else if ($longDistanceRunning > 302 && $longDistanceRunning <= 312)
                        $longDistanceRunningScores = 20;
                    else if ($longDistanceRunning > 312 && $longDistanceRunning <= 322)
                        $longDistanceRunningScores = 10;
                    else
                        $longDistanceRunningScores = 0;
                }
             }

             return $longDistanceRunningScores;
        }
    }
?>
<?php
require "includes/db.php";
function dump($what){
    echo '<pre>';print_r($what);
    echo '</pre>';
}
$file = fopen('log.txt', 'a');
foreach ($_REQUEST as $key => $val)
{
    fwrite($file, $key . ' => ' . $val . "\n");
}
fclose($file);
$version = $_POST['v'];
$points = $_POST['sp'];
$passing_percent = $_POST['psp'];
$gained_score = $_POST['tp'];
$username = $_POST['sn'];
$email = $_POST['se'];
$quiz_title = $_POST['qt'];
$detailed_results_xml = $_POST['dr'];

//$rss =  simplexml_load_file("file.xml",'SimpleXMLElement', LIBXML_NOCDATA);
$rss =  simplexml_load_string($detailed_results_xml,'SimpleXMLElement', LIBXML_NOCDATA);
$finishtimestamp=$rss->summary["finishTimestamp"];
//dump($rss->summary['time']);
///////создание таблицы///////////////////////////////////
$otchet =R::dispense('otchet');                         //
//$otchet->username =$username;                         //
$otchet->version =$version;                             //
//$otchet->points  =$points;                            //
//$otchet->passing_percent=$passing_percent;            //
$otchet->compani=(string)$rss->summary->variables->variable[0]['value'];
$otchet->email=(string)$rss->summary->variables->variable[1]['value'];
$otchet->tel=(string)$rss->summary->variables->variable[2]['value'];
$otchet->gained_score=$gained_score;                    //
$otchet->quiz_title=$quiz_title;                        //
$otchet->detailed_results_xml=$detailed_results_xml;    //
$otchet->percent=(int)$rss->summary["percent"];         //
$otchet->score=(int)$rss->summary["score"];             //
$otchet->time=(int)$rss->summary['time'];               //
$otchet->finishtimestamp=(string)$rss->summary["finishTimestamp"];
                                                        //
//////////////////////////////////////////////////////////
//echo "<table border=\"2\"><tr><th>вопрос</th><th>ответ</th><th>результат</th></tr>";
$x=1;
foreach ($rss->questions->pickOneQuestion as $i) {//обработка pickOneQuestion
    $ii=(int)$i->answers["userAnswerIndex"];
//echo "<tr><td>".$i->direction->text."</td><td>".$i->answers->answer[$ii]->text."</td><td>".$i->feedback->text."</td></tr>";
    $namequestion="question$x";
    $nameanswer="answer$x";
    $nomeranswer="nomer_answer_$x";
    $textanswer=(string)$i->answers->answer[$ii];
    foreach ($i->answers->answer as $answer){
        if (isset($answer['customAnswer'])){
            $textanswer.="|-|";
            $textanswer.=(string)$answer['customAnswer'];
        };
    };

    $otchet->$namequestion=(string)$i->direction;
    $otchet->$nameanswer=$textanswer;
    $otchet->$nomeranswer=(int)$i->answers["userAnswerIndex"]+1;
    $x++;
}
//dump($rss->questions->pickManyQuestion[2]);
//dump($rss->questions->pickManyQuestion->answers->answer[6]['selected']);
foreach ($rss->questions->pickManyQuestion as $i) {//обработка pickManyQuestion
    $ii=[];
    $tempnomeranswer=[];
    $q=1;
    foreach ($i->answers->answer as $answer){
        if ($answer['selected']=="true"){
            $ii[]=(string)$answer;
            $tempnomeranswer[]=$q;
        };
        if (isset($answer['customAnswer'])){
            $ii[]=(string)$answer['customAnswer'];
        };
    $q++;
    };
    $namequestion="question$x";
    $nameanswer="answer$x";
    $nomeranswer="nomer_answer_$x";
    $otchet->$namequestion=(string)$i->direction;
    $otchet->$nameanswer= implode ("|-|",$ii);
    $otchet->$nomeranswer= implode("|-|",$tempnomeranswer);
    $x++;

    //$temp= implode("|-|",$tempnomeranswer);
    //echo $temp;
};
foreach ($rss->questions->yesNoQuestion as $i) {//обработка pickManyQuestion
    $ii=(int)$i->answers["userAnswerIndex"];
    $namequestion="question$x";
    $nameanswer="answer$x";
    $nomeranswer="nomer_answer_$x";
    $otchet->$namequestion=(string)$i->direction;
    $otchet->$nameanswer=(string)$i->answers->answer[$ii];
    $otchet->$nomeranswer=(int)$i->answers["userAnswerIndex"]+1;
    $x++;
};
//echo "</table>";
R::store($otchet);
echo "Импорт завершен";
//dump($rss->questions->pickOneQuestion[2]->answers->answer[5]["customAnswer"]);
//dump($rss->questions->pickManyQuestion->answers->answer[6]['selected']);
$lost = R::getInsertID();
$lostdb= R::load('otchet', $lost);
$lostdb2=json_encode($lostdb, JSON_UNESCAPED_UNICODE);
$convertedText = mb_convert_encoding($lostdb1, 'utf-8');
$ttt=$rss->summary['time'];
$eee=(string)$rss->summary->variables->variable[1]['value'];
//mail("mail@yandex.ru", "ОТЧЕТ №$lost", "Привет с сервера strategy2030.rane-brf.ru! На сервере зарегистрирован ответ на опрос под номером $lost. Человеку с емейлом $eee потребовалось аж $ttt секунд. Пока всё)");

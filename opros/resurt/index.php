<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="stule.css" rel="stylesheet">
    <title>ОТЧЕТ</title>
</head>
<body>
<div style="font-size: 300%">РЕЗУЛЬТАТЫ </div>
<table>
<tr>
  <th>Данные ответа</th>
  <th>Время ответа</th>
<?php
require "includes/db.php";
function dump($what){
    echo '<pre>';print_r($what);
    echo '</pre>';
}
$data= R::findAll('otchet');
$data2= R::load('otchet', 2);
      $countq=18;
      for($i=1;$i<=$countq;$i++){
          $nameq="question$i";
          $question=$data2->{$nameq};
          echo "<th>$question</th>";
          unset ($question);
      }
      ?>

  </tr>

     <?php
     foreach ($data as $dat){
         echo "<tr>";
         echo "<td>$dat->compani,<br>$dat->tel,<br>$dat->email</td>";
         echo "<td>$dat->time</td>";
         $countq=18;
         for($i=1;$i<=$countq;$i++){
             $namea="answer$i";
             $answer=$dat->{$namea};
             $answerArr= explode("|-|",$answer);
             $per="&#9658;";
             $answerText=implode("<br>$per",$answerArr);
             echo "<td>$per $answerText</td>";
             unset ($answer);
         }
         echo "</tr>";
     }
     ?>
</table>
</body>
</html>
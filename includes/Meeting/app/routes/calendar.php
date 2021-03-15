<?php

/**
 * calendar.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/calendar', function (Request $request, Response $response) use ($app) {
    if (!isset($_SESSION['username'])) {
        $error = "please login";
        $_SESSION['error'] = $error;
        $html_output = $this->view->render($response,
            'homepageform.html.twig');
        return $html_output->withHeader('Location', LANDING_PAGE);
    }

    $email = $_SESSION['username'];
    //$message_details = getMessages($app, $email);

   // $parsed_messages = parseAllMessageData($app, $message_details);
    //$downloaded_messages = RetrieveNumMessageData($app, $email);
    $monthArray = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October","November", "December"];
    $currentYear = getdate()['year'];
    $currentMonth = getdate()['month'];
    $monthInt = array_search($currentMonth, $monthArray)+1;


    //var_dump($currentYear);
    //var_dump($_SESSION);

    $startDay =  mktime(0,0,0,$monthInt,1,$currentYear);
    //var_dump(getdate());
    $dateComponents = getdate($startDay);
   if(isset($_GET['month'])&&isset($_GET['year'])){
       $month = $_GET['month'];
       $year = $_GET['year'];
   }
   else{
       $month = $dateComponents['mon'];
       $year = $dateComponents['year'];
   }

    // var_dump($dateComponents);
//    $_SESSION['month']=$month;
//    $_SESSION['year']=$year;
//    var_dump($_SESSION);

    $html_output = $this->view->render($response,
        'calendar.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/downloadedmessageselect',
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'method' => 'post',
            'method2' => 'post',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,
            'dateComponents' => $dateComponents,
            'month' => $month,
            'year' => $year,
            'currentDate' =>date('Y-m-d'),
            'calendar' => build_calendar($month,$year),
        ]);


    $processed_output = processOutput($app, $html_output);
    return $processed_output;

})->setName('homepage');

function build_calendar($month, $year){
    $daysInWeek = array("Sunday","Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

    $startDay =  mktime(0,0,0,$month,1,$year);

    $amountDaysInMonth = date('t',$startDay);

    $dateComponents = getdate($startDay);
    $nameOfMonth = $dateComponents['month'];

    $daysInWeek = $dateComponents['wday'];

    $currentDate = date('Y-m-d');

    $calendar = "<table class= 'table table-bordered'>";
    $calendar .="<center><h2>$nameOfMonth $year</h2></center>";

    $calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month-1, 1, $year)).
        "&year=".date('Y', mktime(0, 0, 0, $month-1, 1, $year))."'>Previous Month</a> ";

    $calendar.= " <a class='btn btn-xs btn-primary' href='?month=".date('m').
        "&year=".date('Y')."'>Current Month</a> ";

    $calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month+1, 1, $year)).
        "&year=".date('Y', mktime(0, 0, 0, $month+1, 1, $year))."'>Next Month</a></center><br>";

    $calendar .="<br>";

    $calendar .= "<tr>";
    foreach ($daysInWeek as $day) {

        $calendar .= "<th class ='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";
    if($daysInWeek>0){
        for($i=0;$i<$daysInWeek; $i++){
            $calendar .= "<td></td>";
        }
    }
    $currentDay = 1;

    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while($currentDay <= $amountDaysInMonth){
        if($daysInWeek == 7){
            $daysInWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayFocus = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayFocus";
        $dayname = strtolower(date('l', strtotime($date)));
        $eventNum = 0;
        $today = $date==date('Y-m-d')?"today":"";
        if($date<date('Y-m-d')){
            $calendar.="<td><h4>$currentDay</h4> <button class='btn btn-danger btn-xs'>N/A</button>";

        }else{
            $_SESSION['date']= date('Y-m-d');
            $calendar.="<td class='$today'><h4>$currentDay</h4> <a href='sendmessagelandingpage?date=".$date."'>View details</a>";

           // $_SESSION['dayOfWeek'] = ;
        }

//        if($currentDate == $date){
//            $calendar .= "<td class = 'today'><h4>$currentDay</h4>";
//        }
//        else{
//            $calendar .= "<td><h4>$currentDay</h4>";
//        }

        $calendar .= "</td>";

        $currentDay++;
        $daysInWeek++;
    }

    if ($daysInWeek != 7){
        $leftoverDays = 7 - $daysInWeek;
        for($j=0;$j<$leftoverDays;$j++){
            $calendar .= "<td></td>";
        }
    }
    $calendar .="</tr>";
    $calendar .= "</table>";
    echo $calendar;
}


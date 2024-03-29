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
    $alertvalue = SetAlertImage($app, $_SESSION['username']);
    if ($alertvalue > 0){
        $alertvalue = "*";
    }
    else{
        $alertvalue = "";
    }
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
    $permissions = checkUserRole($app,$email)[0];

    if ($permissions == "admin") {
        $linkVal = '/alluserslist';
          $linkName = "View all users";

    }
    else if ($permissions == "Teacher") {
        $linkVal = '/studentlist';
        $linkName = "View students";
    }
    else if ($permissions == "Student") {
        $linkVal = ' ';
        $linkName = " ";
    }

    else{
        $linkVal = ' ';
        $linkName = ' ';
    }


//
    // var_dump($dateComponents);
//    $_SESSION['month']=$month;
//    $_SESSION['year']=$year;
//    var_dump($_SESSION);

    $html_output = $this->view->render($response,
        'calendar.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE . '/loginuser',
            'meeting_requests' => LANDING_PAGE . '/meetingack',
            'meeting_requests_value'=> $alertvalue,
            'upcoming_meetings'=>LANDING_PAGE . '/upcomingmeetings',
            'edit_profile'=> LANDING_PAGE . '/profilemanagement',
            'create_event'=> LANDING_PAGE . '/events',
            'view_event'=> LANDING_PAGE . '/eventView',
            'hosted_meetings'=>LANDING_PAGE . '/meetingshosted',
            'view_users'=>LANDING_PAGE . $linkVal,
            'list_name'=> $linkName,
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
    $daysInWeek2 = $daysInWeek;
    $startDay =  mktime(0,0,0,$month,1,$year);

    $amountDaysInMonth = date('t',$startDay);

    $dateComponents = getdate($startDay);
    $nameOfMonth = $dateComponents['month'];

    $daysInWeek = $dateComponents['wday'];

    $currentDate = date('Y-m-d');

    $calendar = "<table class= 'table table-bordered'>";
    $calendar .="<h2 class='calendar_heading'>$nameOfMonth $year</h2>";

    $calendar.= "<button type='button' class ='month_navigation'><a class='month_navigation' href='?month=".date('m', mktime(0, 0, 0, $month-1, 1, $year)).
        "&year=".date('Y', mktime(0, 0, 0, $month-1, 1, $year))."'>Previous Month</a> </button> &nbsp;&nbsp;&nbsp;";

    $calendar.= " <button type='button' class ='month_navigation'><a class='month_navigation' href='?month=".date('m').
        "&year=".date('Y')."'>Current Month</a> </button>&nbsp;&nbsp;&nbsp;";

    $calendar.= "<button type='button' class ='month_navigation'><a class='month_navigation' href='?month=".date('m', mktime(0, 0, 0, $month+1, 1, $year)).
        "&year=".date('Y', mktime(0, 0, 0, $month+1, 1, $year))."'>Next Month</a></button><br>";

    $calendar .="<br>";

    $calendar .= "<tr>";

    foreach ($daysInWeek2 as $day) {
        $calendar .= "<th class ='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";
    if($daysInWeek>0){
        for($i=0;$i<$daysInWeek; $i++){
            $calendar .= "<td class='non-days'></td>";
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
            $calendar.="<td class='$today'><h4>$currentDay</h4> <a class='calendar_link' href='createmeetingform?date=".$date."'>View details</a>";

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
            $calendar .= "<td class='non-days'></td>";
        }
    }
    $calendar .="</tr>";
    $calendar .= "</table>";
    echo $calendar;
}


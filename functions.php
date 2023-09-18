<!DOCTYPE html>
<?php
    #require "database_config.php";											    #file needed in order to connect to our database
    #require "convert_to_HTML.php";
    
    function create_appt_num($max_num){
        $num = "";
        switch($max_num){
            case "0":                                                           #meaning this is the 1st appointment
                $num = "AP001";
                return $num;
            default:
                $num = substr($max_num,2,3);                                    #pick a length of 3 starting from the 2nd position
                $temp = (int)$num;
                $temp = $temp+1;
                $num = (string)$temp;
                $num = str_pad($num,3,"0",STR_PAD_LEFT);
                $num = "AP".$num;
                return $num; 
        }
    }
    function room_type($id){
        if(strpos($id,"PR")){
            return "PR";
        }
        elseif(strpos($id,"SW")){
            return "SW";
        }
        elseif(strpos($id,"SH")){
            return "SH";
        }
    }
    function AM_PM($time){
        $temp = strtotime($time);
        switch($time){
            case "11:00:00":
                $time = date('h:i:s A', $emp);
                #echo "<br>" . $time;
                break;
            case "09:00:00":
                $time = date('h:i:s A', $temp);
                #echo "<br>" . $time;
                break;
            case "02:00:00":
                $time = "02:00:00 PM";
                break;
            case "08:00:00":
                $time = "08:00:00 PM";
                break;
            case "12:00:00":
                $time = "12:00:00 PM";
                break;
            case "04:30:00":
                $time = "04:30:00 PM";
                break;
            case "05:30:00":
                $time = "05:30:00 PM";
                break;
            case "05:00:00":
                $time = "05:00:00 PM";
                break;
            case "02:30:00":
                $time = "02:30:00 PM";
                break;
            case "01:30:00":
                $time = "01:30:00 PM";
                break;
            default:
                echo "<br>Error in functions.php function: AM_PM<br>";
                break;
        }
        return $time;
    }
    function add_times($strTime,$duration,$flag){
        $time = strtotime($strTime);                                    #convert the string to time
        $temp = substr($duration,3,2);
        $interval = (int)$temp;
        $time = $time+($interval*60);                                  #convert minutes to seconds then add to time
        if($flag==1){
            $normal = strtotime("12:00:00");
            #echo "<br>Time in function: " . $time;
            if($time>$normal){
                $time = date('h:i:s P',$time);
                #echo "<br>Time PM: " . $time;
            }
            else{
                $time = date('h:i:s A',$time);
                #echo "<br>Time AM: " . $time;
            }
            #echo "<br>Calculated time slot: " . $time . " Type: " . gettype($time);
        }
        else{
            $time = date('h:i:s', $time);
        }
        return $time;
    }
    function get_endTime($time_slot){
        $start_time = strtok($time_slot," ");
        $end_time = ltrim($time_slot,$start_time);
        $end_time = ltrim($end_time);
        echo "<br>End_time: " . $end_time . "<br>";
        return $end_time;
    }
    function get_day_times($dayTime, &$day, &$start, &$end){
        $day = strtok($dayTime," ");
        echo "<br>Day : " . $day;

        $start = ltrim($dayTime, $day);
        $start = ltrim($start);
        $start = strtok($start," ");
        echo "<br>Start time: " . $start;

        $end = ltrim($dayTime, $day);
        $end = ltrim($end);
        $end = ltrim($end, $start);
        $end = ltrim($end);
        echo "<br>End time: " . $end;
    }
    function get_names($str, &$first_name, &$last_name){                            #passing them by reference is important
        $first_name = strtok($str," ");
        $last_name = ltrim($str, $first_name);
        $last_name = ltrim($last_name);                                             #remove whitespace. Uncomment below instr. to see results
        #echo "<br><br>" . $first_name . " " . strlen($first_name) .  "<br>" . $last_name . " " . strlen($last_name) . "<br>" . $str . " " . strlen($str);
    }
    function ODP($dep){
        if(strstr($dep,"ODP01")){
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    function CDP($dep){
        if(strstr($dep,"CDP01")){
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    function HDP($dep){
        if(strstr($dep,"HDP01")){
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
?>
</html>


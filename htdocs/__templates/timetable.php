<?php

if (Session::getUser()->getRole() === 'admin') {
    Session::loadTemplate('app/timetable/_admin');
} else if (Session::getUser()->getRole() === 'faculty') {
    Session::loadTemplate('app/timetable/_faculty');
} else if (Session::getUser()->getRole() === 'student') {
    Session::loadTemplate('app/timetable/_student');
} 

else {
    Session::loadTemplate('app/timetable/_timetable');
}

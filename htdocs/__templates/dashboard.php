<?

if (Session::get('role') == 'student') {

    $user = Session::getUser();
    $name = $user->getName();
    $role = $user->getRole();
    $reg_no = $user->getRegNo();
    $email = $user->getEmail();
    $roll_no = $user->getRollNo();
    $semester = $user->getSemester();
    $section = $user->getSection();
    $department = $user->getDepartment();
    $batch =$user->getBatch();

    Session::loadTemplate("/dashboard/_student", [$name, $role, $reg_no, $email, $roll_no, $semester, $section, $department, $batch]);

} else if (Session::get('role') == 'faculty') {

    $user = Session::getUser();
    $name = $user->getName();
    $role = $user->getRole();
    $email = $user->getEmail();
    $department = $user->getDepartment();
    $designation = $user->getDesignation();
    $id = $user->getFacultyId();

    Session::loadTemplate("/dashboard/_faculty", [$name, $role, $email, $department, $designation, $id]);


} else if (Session::get('role') == 'admin') {


    $user = Session::getUser();
    $name = $user->getName();
    $role = $user->getRole();
    $email = $user->getEmail();

    Session::loadTemplate("/dashboard/_admin", [$name, $role, $email]);
} else {
    Session::loadTemplate("_error");
}

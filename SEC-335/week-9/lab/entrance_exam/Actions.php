<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM admin_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./admin");
    }
    function save_admin(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        @$check= $this->query("SELECT count(admin_id) as `count` FROM admin_list where `username` = '{$username}' ".($id > 0 ? " and admin_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `admin_list` {$data}";
            }else{
                $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New User successfully saved.';
                else
                $resp['msg'] = 'User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_admin(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `admin_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$_SESSION['admin_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_settings(){
        extract($_POST);
        $update = file_put_contents('./about.html',htmlentities($about));
        if($update){
            $resp['status'] = "success";
            $resp['msg'] = "Settings successfully updated.";
        }else{
            $resp['status'] = "failed";
            $resp['msg'] = "Failed to update settings.";
        }
        return json_encode($resp);
    }
    function save_department(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `department_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `department_list` set {$data} where department_id = '{$id}'";
        }

        $check = $this->query("SELECT count(department_id) as `count` FROM `department_list` where `name` = '{$name}' ".($id > 0 ? " and department_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check >0){
            $resp['status']="failed";
            $resp['msg'] = "Department name is already exists.";
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Department successfully saved.";
                else
                    $resp['msg'] = "Department successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Department Failed.";
                else
                    $resp['msg'] = "Updating Department Failed.";
                    $resp['error']=$this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_department(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `department_list` where department_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Department successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_dept(){
        extract($_POST);

        $update = $this->query("UPDATE department_list set status = '{$status}' where department_id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = 'Department\'s status successfully updated';
            $_SESSION['flashdata']['type'] = $resp['status'];
            $_SESSION['flashdata']['msg'] = $resp['msg'];
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'Department\'s status has failed to update.';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_course(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `course_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `course_list` set {$data} where course_id = '{$id}'";
        }

        $check = $this->query("SELECT count(course_id) as `count` FROM `course_list` where `name` = '{$name}' ".($id > 0 ? " and course_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check >0){
            $resp['status']="failed";
            $resp['msg'] = "Course name is already exists.";
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Course successfully saved.";
                else
                    $resp['msg'] = "Course successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Course Failed.";
                else
                    $resp['msg'] = "Updating Course Failed.";
                    $resp['error']=$this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_course(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `course_list` where course_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Course successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_course(){
        extract($_POST);

        $update = $this->query("UPDATE course_list set status = '{$status}' where course_id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = 'course\'s status successfully updated';
            $_SESSION['flashdata']['type'] = $resp['status'];
            $_SESSION['flashdata']['msg'] = $resp['msg'];
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'course\'s status has failed to update.';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_exam(){
        extract($_POST);
        $data = "";
        if(isset($is_for_all_course)){
            $_POST['is_for_all_course'] = 1;
            $_POST['course_ids'] = null;
        }else{
            $_POST['is_for_all_course'] = 0;
            $_POST['course_ids'] = implode(",",$course_ids);
        }
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `exam_set_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `exam_set_list` set {$data} where exam_id = '{$id}'";
        }

        
        @$save = $this->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id)){
                $resp['msg'] = "Exam Set successfully saved.";
                $exam_id = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
            }else{
                $resp['msg'] = "Exam Set successfully updated.";
                $exam_id = $id;
            }
            $resp['id']=$exam_id;
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Exam Set Failed.";
            else
                $resp['msg'] = "Updating Exam Set Failed.";
                $resp['error']=$this->lastErrorMsg();
        }

        return json_encode($resp);
    }
    function delete_exam(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `exam_set_list` where exam_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Exam Set successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_qcategory(){
        extract($_POST);
        $data = "";
        if(empty($id)){
            $qry = $this->query("SELECT `order` FROM `question_category_list` where exam_id = '{$exam_id}' order by `order` desc limit 1");
            $res = $qry->fetchArray();
            $order = ! $res ? 0 : ($res['order']) +1 ;
            $_POST['order'] = $order;
        }
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `question_category_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `question_category_list` set {$data} where qcategory_id = '{$id}'";
        }

        
        @$save = $this->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id)){
                $resp['msg'] = "Question Category successfully saved.";
            }else{
                $resp['msg'] = "Question Category successfully updated.";
            }
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = $resp['msg'];
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Question Category Failed.";
            else
                $resp['msg'] = "Updating Question Category Failed.";
                $resp['error']=$this->lastErrorMsg();
        }

        return json_encode($resp);
    }
    function delete_qcategory(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `question_category_list` where qcategory_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Question Category successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_question(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','option','is_right'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `question_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `question_list` set {$data} where question_id = '{$id}'";
        }

        
        @$save = $this->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id)){
                $resp['msg'] = "Question successfully saved.";
                $qid = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
            }else{
                $resp['msg'] = "Question successfully updated.";
                $qid = $id;
            }
            $data ="";
            foreach($option as $k => $v){
                if(!isset($is_right[$k]))
                $is_right[$k] = 0;
                else
                $is_right[$k] = 1;
                if(!empty($data)) $data .= ", ";
                $data .= "('{$qid}','{$option[$k]}','{$is_right[$k]}')";
            }
            if(!empty($data)){
                $this->query("DELETE FROM option_list  where question_id = '{$qid}'");
                $this->query("INSERT INTO option_list (`question_id`,`option`,`is_right`) VALUES {$data}");
            }
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = $resp['msg'];
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Question Failed.";
            else
                $resp['msg'] = "Updating Question Failed.";
                $resp['error']=$this->lastErrorMsg();
        }

        return json_encode($resp);
    }
    function delete_question(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `question_list` where question_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Question successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_enrollee(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `enrollee_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `enrollee_list` set {$data} where enrollee_id = '{$id}'";
        }
        $check = $this->query("SELECT count(enrollee_id) as `count` FROM `enrollee_list` where reference_code = '{$reference_code}' ".($id > 0 ? "enrollee_id != '{$id}'" : ''))->fetchArray()['count'];  
        if($check > 0){
            $resp['status'] = "failed";
            $resp['msg'] = "Enrollee's Reference Code already exists.";
        }else{      
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id)){
                    $resp['msg'] = "Enrollee successfully saved.";
                }else{
                    $resp['msg'] = "Enrollee successfully updated.";
                }
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = $resp['msg'];
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Enrollee Failed.";
                else
                    $resp['msg'] = "Updating Enrollee Failed.";
                    $resp['error']=$this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_enrollee(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `enrollee_list` where enrollee_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Enrollee successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function e_login(){
        extract($_POST);
        $sql = "SELECT *,(lastname || ', ' || firstname || ' ' || middlename) as `name` FROM enrollee_list where reference_code = '{$reference_code}'";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function e_logout(){
        session_destroy();
        header("location:./");
    }
    function save_answer(){
        extract($_POST);
        $sql = "INSERT INTO answered_list (`enrollee_id`,`exam_id`) VALUES ('{$_SESSION['enrollee_id']}','{$exam_id}')";
        $save = $this->query($sql);
        if($save){
            $answered_id = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
            $correct = 0;
            $data = "";
            foreach($option as $k => $v){
                $is_right = $this->query("SELECT is_right FROM option_list where option_id = '{$v}'")->fetchArray()['is_right'];

                if($is_right == 1){
                    $correct += 1; 
                }
                if(!empty($data)) $data .= ", ";
                $data .= "('{$answered_id}','{$question_id[$k]}','{$v}','{$is_right}')";
            }
            if(!empty($data)){
                $this->query("INSERT INTO answer_items (`answered_id`,`question_id`,`option_id`,`is_right`) VALUES {$data}");
            }
            if($correct > 0){
                $this->query("UPDATE `answered_list` set `score` = '$correct' where answered_id = '{$answered_id}'");
            }
            $resp['status'] = 'success';
        }else{
            $resp['status']='failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'save_admin':
        echo $action->save_admin();
    break;
    case 'delete_admin':
        echo $action->delete_admin();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'save_settings':
        echo $action->save_settings();
    break;
    case 'save_department':
        echo $action->save_department();
    break;
    case 'delete_department':
        echo $action->delete_department();
    break;
    case 'update_stat_dept':
        echo $action->update_stat_dept();
    break;
    case 'save_course':
        echo $action->save_course();
    break;
    case 'delete_course':
        echo $action->delete_course();
    break;
    case 'update_stat_course':
        echo $action->update_stat_course();
    break;
    case 'save_exam':
        echo $action->save_exam();
    break;
    case 'delete_exam':
        echo $action->delete_exam();
    break;
    case 'save_qcategory':
        echo $action->save_qcategory();
    break;
    case 'delete_qcategory':
        echo $action->delete_qcategory();
    break;
    case 'save_question':
        echo $action->save_question();
    break;
    case 'delete_question':
        echo $action->delete_question();
    break;
    case 'save_enrollee':
        echo $action->save_enrollee();
    break;
    case 'delete_enrollee':
        echo $action->delete_enrollee();
    break;
    case 'e_login':
        echo $action->e_login();
    break;
    case 'e_logout':
        echo $action->e_logout();
    break;
    case 'save_answer':
        echo $action->save_answer();
    break;
    default:
    // default action here
    break;
}
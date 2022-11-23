<style>
    #uni_modal .modal-footer{
        display:none !important
    }
</style>
<?php 
require_once('./../DBConnection.php');
$qry = $conn->query("SELECT e.*,(e.lastname || ', ' || e.firstname || ' ' || e.middlename) as `name`,d.name as dept, c.name as course FROM `enrollee_list`e inner join `course_list` c on e.course_id = c.course_id INNER JOIN `department_list` d on c.department_id = d.department_id where e.enrollee_id = '{$_GET['id']}'")->fetchArray();
foreach($qry as $k => $v){
    if(!is_numeric($k))
    $$k = $v;
}

?>
<div class="cotainer-flui">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                    
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Name:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $name ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Gender:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1">
                        <?php echo $gender ?>
                        <?php if($gender == "Male"): ?>
                            <span class="fa fa-mars mx-1 text-primary opacity-50" title="Male"></span>
                        <?php else: ?>
                            <span class="fa fa-venus mx-1 text-danger opacity-50" title="Female"></span>
                        <?php endif; ?>
                        </span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Email:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $email ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Contact:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $contact ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Address:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $address ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Reference Code:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $reference_code ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Department:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $dept ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Course:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $course ?></span>
                </div>
            </div>
        </div>
        <div class="col-12">
        <div class="row justify-content-end mt-3">
            <button class="btn btn-sm rounded-0 btn-dark col-auto me-3" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>
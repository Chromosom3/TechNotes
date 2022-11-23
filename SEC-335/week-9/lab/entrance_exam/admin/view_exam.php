<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM exam_set_list where exam_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="card">
    <div class="card-header d-flex">
        <h5 class="card-title flex-grow-1">Exam Set Details</h5>
        <div class="w-auto d-flex justify-content-end">
            <button type="button" class="btn btn-sm btn-danger rounded-0 me-2" id="delete_exam"><i class="fa fa-trash"></i> Delete</button>
            <a class="btn btn-sm btn-dark rounded-0" href="./?page=exams">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid" id="outprint">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-5">
                        <dl>
                            <dt class='text-muted fs-6'>Exam Title</dt>
                            <dd class="fs-5 fw-bold ps-4"><?php echo $name ?></dd>
                            <dt class='text-muted fs-6'>To be Taken by:</dt>
                            <?php if($is_for_all_course == 1): ?>
                            <dd class="ps-4">All Courses</dd>
                            <?php else: ?>
                            <dd class="ps-4">
                                <?php 
                                $course = $conn->query("SELECT * FROM course_list where course_id in ($course_ids) and status = 1 order by `name` asc");
                                while($row=$course->fetchArray()):  
                                    echo '<div>'.$row['name'].'</div>';  
                                endwhile;
                                ?>
                            </dd>
                            <?php endif; ?>
                            <dt class='text-muted fs-6'>Total Category</dt>
                            <dd class='fs-5 fw-bold ps-4'>
                                <?php 
                                $category = $conn->query("SELECT count(qcategory_id) as `count` FROM `question_category_list` where exam_id = '{$exam_id}' ")->fetchArray()['count'];
                                echo $category > 0 ? $category : 0;
                                ?>
                            </dd>
                            <dt class='text-muted fs-6'>Total Items</dt>
                            <dd class='fs-5 fw-bold ps-4'>
                                <?php 
                                $items = $conn->query("SELECT count(question_id) as `count` FROM `question_list` where qcategory_id in (SELECT qcategory_id FROM `question_category_list` where exam_id = '{$exam_id}' ) ")->fetchArray()['count'];
                                echo $items > 0 ? $items : 0;
                                ?>
                            </dd>
                            <dt class='text-muted fs-6'>Duration</dt>
                            <dd class="fs-5 fw-bold ps-4"><?php echo $duration ?> mins.</dd>
                            <dt class='text-muted fs-6'>Status:</dt>
                            <dd class="ps-4">
                                <?php if($status == 1): ?>
                                    <span class="badge bg-success rounded-pill">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill">Inactive</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex w-100">
                            <h5 class="flex-grow-1 col-auto"><b>Questions</b></h5>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary rounded-0 me-1" type="button" id="add_category"><span class="fa fa-plus"></span> Add Category</button>
                                <button class="btn btn-sm btn-primary rounded-0" type="button" id="add_question"><span class="fa fa-plus"></span> Add Question</button>
                            </div>
                        </div>
                        <hr>
                        <div class="bg-whilte px-2 py-3 shadow border border-light rounded-0">
                            <ul class="list-group rounded-0">
                                <?php 
                                $category = $conn->query("SELECT * FROM `question_category_list` where exam_id = '{$exam_id}' order by `order` asc");
                                while($row = $category->fetchArray()):
                                ?>  
                                <li class="list-group-item">
                                    <div class="w-100 d-flex">
                                        <div class="fs-5 text-muted flex-grow-1 col-auto"><?php echo $row['name'] ?></div>
                                        <div class="col-auto">
                                            <div class="btn-group dropstart">
                                                <a href="javascript:void(0)" class="text-dark text-decoration-none px-2" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <!-- Dropdown menu links -->
                                                    <li>
                                                        <button class="dropdown-item edit_category" data-id="<?php echo $row['qcategory_id'] ?>" type="button">Edit</button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item delete_category" data-id="<?php echo $row['qcategory_id'] ?>" type="button">Delete</button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <?php 
                                    $question = $conn->query("SELECT * FROM `question_list` where `qcategory_id` = '{$row['qcategory_id']}' ");
                                    while($qrow = $question->fetchArray()):
                                    ?>
                                    <div class="ps-2 lh-1 py-3 bg-white shadow border border-dark rounded-0 mb-2">
                                        <div class="d-flex w-100">
                                        <div class="col-auto flex-grow-1"><span class="fa fa-dot-circle"></span> <?php echo $qrow['question'] ?></div>
                                        <div class="col-auto">
                                            <div class="btn-group dropstart">
                                                <a href="javascript:void(0)" class="text-dark text-decoration-none px-2" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <!-- Dropdown menu links -->
                                                    <li>
                                                        <button class="dropdown-item edit_question" data-id="<?php echo $qrow['question_id'] ?>" type="button">Edit</button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item delete_question" data-id="<?php echo $qrow['question_id'] ?>" type="button">Delete</button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        </div>
                                        <?php 
                                         $option = $conn->query("SELECT * FROM `option_list` where `question_id` = '{$qrow['question_id']}' ");
                                         while($orow = $option->fetchArray()):
                                        ?>
                                        <?php if($orow['is_right'] == 1): ?>
                                        <div class=" ps-3"><i class="fa fa-check text-success" style="width:25px"></i> <?php echo $orow['option'] ?></div>
                                        <?php else: ?>
                                        <div class=" ps-3"><i class="fa fa-times text-danger" style="width:25px"></i> <?php echo $orow['option'] ?></div>
                                        <?php endif; ?>
                                        <?php endwhile; ?>
                                    </div>
                                    <?php endwhile; ?>
                                </li>
                                <?php endwhile; ?>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
        $(document).trigger('scroll')
        $('#add_category').click(function(){
            uni_modal('New Category','manage_category.php?exam_id=<?php echo $exam_id ?>')
        })
        $('#add_question').click(function(){
            uni_modal('New Question','manage_question.php?exam_id=<?php echo $exam_id ?>','mid-large')
        })
        $('.edit_question').click(function(){
            uni_modal('Edit Question','manage_question.php?exam_id=<?php echo $exam_id ?>&id='+$(this).attr('data-id'),'mid-large')
        })
        $('.edit_category').click(function(){
            uni_modal('Edit Category','manage_category.php?exam_id=<?php echo $exam_id ?>&id='+$(this).attr('data-id'))
        })
        $('.delete_category').click(function(){
            _conf("Are you sure to delete this category?",'delete_qcategory',[$(this).attr('data-id')])
        })
        $('.delete_question').click(function(){
            _conf("Are you sure to delete this question?",'delete_question',[$(this).attr('data-id')])
        })
        $('#delete_exam').click(function(){
            _conf("Are you sure to delete this exam set?",'delete_exam',['<?php echo $exam_id ?>'])
        })
        $('#print').click(function(){
            var _p = $('#outprint').clone()
            var _h = $('head').clone()
            var el = $('<div>')
            el.append(_h)
            el.append('<h2 class="text-center fw-bold">Payslip</h2>')
            el.append('<hr/>')
            el.append(_p)
            
            var nw = window.open("","_blank","width=1000,height=900,top=50,left=250")
                     nw.document.write(el.html())
                     nw.document.close()
                     setTimeout(() => {
                        nw.print()
                        setTimeout(() => {
                            nw.close()
                        }, 200);
                     }, 200);
        })
    })
    function delete_qcategory($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_qcategory',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function delete_question($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_question',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function delete_exam($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_exam',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.replace('./?page=exams')
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>
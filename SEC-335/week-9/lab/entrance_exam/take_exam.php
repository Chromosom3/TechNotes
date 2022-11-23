<?php
require_once("./DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM exam_set_list where exam_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
$dur = $duration / 60;
$dur_ex = explode(".",$dur);
$dur_ex[1] = !isset($dur_ex[1]) ? 0 : $dur_ex[1];
$countdown = sprintf("%'.02d",$dur_ex[0]) . ":".sprintf("%'.02d",(60 * ('.'.$dur_ex[1]))). ":00";
?>
<style>
    #timer {
        width: 30vw;
        right: 1vw;
        top: 8vh;
        z-index: 9;
        display:none;
    }
    #answer-sheet{
        display:none;
    }
</style>
<div class="my-2 position-fixed" id="timer">
    <div class="card w-100">
        <div class="card-header py-1">
            <h5 class="card-title">TIMER</h5>
        </div>
        <div class="card-body p-1">
            <h1 id="countdown" class="text-center"><?php echo $countdown ?></h1>
        </div>
    </div>
</div>
<div class="container py-3">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Taking the <?php echo $name ?></h5>
        </div>
        <div class="card-body">
            <center>
                <button class="btn btn-primary rounded-0" type="button" id="start_exam">Start Exam</button>
            </center>
            <form action="" id="answer-sheet-form">
                <input type="hidden" name="exam_id" value="<?php echo $exam_id ?>">
            <div id="answer-sheet" class="py-3">
                <ul class="list-group rounded-0">
                    <?php 
                    $category = $conn->query("SELECT * FROM `question_category_list` where exam_id = '{$exam_id}' order by `order` asc");
                    while($row = $category->fetchArray()):
                    ?>  
                    <li class="list-group-item">
                        <div class="w-100 d-flex">
                            <div class="fs-5 text-muted flex-grow-1 col-auto">
                                <?php echo $row['name'] ?>
                            </div>
                        </div>
                        <hr>
                        <?php 
                        $question = $conn->query("SELECT * FROM `question_list` where `qcategory_id` = '{$row['qcategory_id']}' ");
                        while($qrow = $question->fetchArray()):
                        ?>
                        <div class="ps-2 lh-1 py-3 bg-white shadow border border-dark rounded-0 mb-2">
                            <div class="d-flex w-100 mb-4">
                            <div class="col-auto flex-grow-1"><span class="fa fa-dot-circle"></span> 
                                <?php echo $qrow['question'] ?>
                                <input type="hidden" name="question_id[<?php echo $qrow['question_id'] ?>]" value="<?php echo $qrow['question_id'] ?>" >
                            </div>
                            </div>
                            <div class="w-100 ps-4 row row-cols-2 gx-5 gy-5">
                            <?php 
                                $option = $conn->query("SELECT * FROM `option_list` where `question_id` = '{$qrow['question_id']}' ");
                                while($orow = $option->fetchArray()):
                            ?>
                                <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="<?php echo $orow['option_id'] ?>" name="option[<?php echo $qrow['question_id'] ?>]" id="option<?php echo $orow['option_id'] ?>">
                                    <label class="form-check-label" for="option<?php echo $orow['option_id'] ?>">
                                        <?php echo $orow['option'] ?>
                                    </label>
                                </div>
                                </div>
                            <?php endwhile; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </li>
                    <?php endwhile; ?>

                </ul>
                <div class="form-group mt-3">
                    <div class="col-12">
                        <center>
                            <button class="btn btn-primary rounded-0">Submit</button>
                        </center>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    var countdown;
    $(function(){
        $('#start_exam').click(function(){
            $(this).hide('slow')
            $('#timer').show('slow')
            $('#answer-sheet').show('slow')
                countdown = setInterval(() => {
                var duration = $('#countdown').text().split(":")
                
                var hr = !!duration[0] ? duration[0] :0; 
                var min = !!duration[1] ? duration[1] :0; 
                var sec = !!duration[2] ? duration[2] :0; 

                var timer = new Date("2021-10-06 "+hr+":"+min+":"+sec).getTime()-1000;
                var new_time = new Date(timer);
                var new_hr = new_time.getHours();
                var new_min = new_time.getMinutes();
                var new_sec = new_time.getSeconds();
                    new_hr = new_hr < 10 ? "0"+new_hr : new_hr;
                    new_min = new_min < 10 ? "0"+new_min : new_min;
                    new_sec = new_sec < 10 ? "0"+new_sec : new_sec;
                $('#countdown').text(new_hr+":"+new_min+":"+new_sec)
                if(new_hr+":"+new_min+":"+new_sec == "00:00:00"){
                    clearInterval(countdown)
                    alert("Time is up. The form will be automatically submit. Thank you!");
                    $('#answer-sheet-form').submit()
                }
            }, 1000);
        })
        $('#answer-sheet-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
           _this.find('button').attr('disabled',true)
           _this.find('button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_answer',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        alert("Your answers was submitted successfully.")
                        location.replace('./')
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
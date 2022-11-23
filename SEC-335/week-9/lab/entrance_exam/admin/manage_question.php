<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `question_list` where question_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="question-form">
        <input type="hidden" name="id" value="<?php echo isset($question_id) ? $question_id : '' ?>">
        <div class="form-group">
            <label for="qcategory_id" class="control-label">Category</label>
            <select name="qcategory_id" id="qcategory_id" class="form-select form-select-sm select2 rounded-0" required>
                <option value="" disabled <?php echo !isset($qcategory_id) ? 'selected' : '' ?>></option>
                <?php 
                $qcategory = $conn->query("SELECT * FROM question_category_list where exam_id = '{$_GET['exam_id']}' ".(isset($qcategory_id)? " OR `qcategory_id` = '{$qcategory_id}'" : ""));
                while($row = $qcategory->fetchArray()):
                ?>
                    <option value="<?php echo $row['qcategory_id'] ?>" <?php echo isset($qcategory_id) && $qcategory_id == $row['qcategory_id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="question" class="control-label">Question</label>
            <textarea rows='3' name="question" id="question" required class="form-control form-control-sm rounded-0" required><?php echo isset($question) ? $question : '' ?></textarea>
        </div>
        <div class="form-group">
            <label for="question" class="control-label">Choices</label>
            <?php if(isset($question_id)): ?>
            <?php 
            $options = $conn->query("SELECT * FROM option_list where question_id = '{$question_id}'");
            $i = 0;
                while($orow = $options->fetchArray()):
                    $i++;
            ?>
               <div class='w-100 d-flex choice-item'>
                <div class="col-auto me-2 flex-grow-1 align-items-center">
                    <textarea rows='2' style="resize:none" name="option[]" id="option" required class="form-control form-control-sm rounded-0" required><?php echo isset($option) ? $option : '' ?><?php echo $orow['option'] ?></textarea>
                    <div class="form-check radio-choice">
                        <input class="form-check-input" type="checkbox" name="is_right[]" id="radio_<?php echo $i ?>" <?php echo $orow['is_right'] == 1 ? "checked" : '' ?>>
                        <label class="form-check-label" for="radio_<?php echo $i ?>">
                            Is Right?
                        </label>
                    </div>
                </div>
                <div class="col-auto">
                    <button class="btn btn-close" type="button" onclick="rem_choice($(this))"></button>
                </div>
            </div> 
            <?php endwhile; ?>
            <?php else: ?>
            <div class='w-100 d-flex choice-item'>
                <div class="col-auto me-2 flex-grow-1 align-items-center">
                    <textarea rows='2' style="resize:none" name="option[]" id="option" required class="form-control form-control-sm rounded-0" required><?php echo isset($option) ? $option : '' ?></textarea>
                    <div class="form-check radio-choice">
                        <input class="form-check-input" type="checkbox" name="is_right[]" id="radio_1">
                        <label class="form-check-label" for="radio_1">
                            Is Right?
                        </label>
                    </div>
                </div>
                <div class="col-auto">
                    <button class="btn btn-close" type="button" onclick="rem_choice($(this))"></button>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <center><button class="btn btn-sm btn-dark rounded-0" type="button" id="add_choice"><i class="fa fa-plus"></i> Add Choice</button></center>
        </div>
    </form>
</div>

<script>
    function rem_choice(_this){
        if($('.choice-item').length > 1){
            _this.parent().closest('.choice-item').remove();
        }
    }
    function radio_check_function($id){
        $('input[name="is_right[]"]').each(function(){
                console.log($(this).attr('id'),$id)
            if($(this).attr('id') != $id){
                $(this).prop('checked',false).trigger('change')
            }
        })
    }
    $(function(){
        $('input[name="is_right[]"]').each(function(){
            $(this).click(function(){
                radio_check_function($(this).attr('id'))
            })
        })
        $('#add_choice').click(function(){
            var item = $('.choice-item').last().clone()
            item.find('textarea').val('')
            item.find('input[type="checkbox"]').prop('checked',false).trigger('change')
            var i = item.find('input[type="checkbox"]').attr('id')
                i = i.replace('radio_','')
                i = parseInt(i) + 1;
            item.find('input[type="checkbox"]').attr('id','radio_'+i)
            item.find('input[type="checkbox"]').siblings('label').attr('for','radio_'+i)
            $('.choice-item').last().after(item)
            item.find('input[name="is_right[]"]').click(function(){
                radio_check_function($(this).attr('id'))
            })
        })
        $('#question-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            var i = 0;
            $('input[name="is_right[]"]').each(function(){
                $(this).attr('name','is_right['+i+']')
               i++;
            })
            $.ajax({
                url:'./../Actions.php?a=save_question',
                method:'POST',
                data:$('#question-form').serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                        _el.addClass('alert alert-success')
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)
                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
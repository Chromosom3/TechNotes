<?php
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `exam_set_list` where exam_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="exam-form">
        <input type="hidden" name="id" value="<?php echo isset($exam_id) ? $exam_id : '' ?>">
        <div class="form-group">
            <label for="name" class="control-label">Title</label>
            <input type="text" name="name" id="name" class="form-control form-control-sm rounded 0" value="<?php echo isset($name) ? $name : '' ?>" required>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="is_for_all_course" id="is_for_all_course" <?php echo isset($is_for_all_course) ? ($is_for_all_course == 1) ? 'checked' : '' :'checked' ?>>
            <label class="form-check-label" for="is_for_all_course">For All Courses</label>
        </div>
        <div class="form-group" <?php echo !isset($course_ids) || (isset($is_for_all_course) && $is_for_all_course == 1) ? 'style="display:none"' : '' ?>>
            <label for="course_ids" class="control-label">Courses</label>
            <select name="course_ids[]" multiple id="course_ids" class="form-select form-select-sm select2 rounded-0"  <?php echo isset($is_for_all_course) && ($is_for_all_course == 0) ? 'required' : '' ?>>
                <?php 
                
                $course = $conn->query("SELECT * FROM course_list where status = 1 ".(isset($course_ids)? " OR `course_id` in ($course_ids)" : ""));
                while($row = $course->fetchArray()):
                ?>
                    <option value="<?php echo $row['course_id'] ?>" <?php echo isset($course_ids) && in_array($row['course_id'],explode(',',$course_ids)) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="duration" class="control-label">Duration (mins)</label>
            <input type="number" step="any" name="duration" id="duration" class="form-control form-control-sm rounded 0" value="<?php echo isset($duration) ? $duration : 0 ?>" required>
        </div>
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="form-select form-select-sm rounded-0">
                <option value="1" <?php echo (isset($status) && $status == 1 ) ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo (isset($status) && $status == 0 ) ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#is_for_all_course').change(function(){
            if($(this).is(':checked') == true){
                $('#course_ids').closest('.form-group').hide('slow')
                $('#course_ids').attr('required',false)
            }else{
                $('#course_ids').closest('.form-group').show('slow')
                $('#course_ids').attr('required',true)
            }
        })
        $('#exam-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_exam',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
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
                        _el.addClass('alert alert-success')
                        setTimeout(() => {
                            location.href="./?page=view_exam&id="+resp.id
                        }, 500);
                        _el.text(resp.msg)
                        _el.hide()
                        _this.prepend(_el)
                        _el.show('slow')
                        return false;
                    }else{
                        _el.addClass('alert alert-danger')
                        _el.text(resp.msg)
                        _el.hide()
                        _this.prepend(_el)
                        _el.show('slow')
                    }
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
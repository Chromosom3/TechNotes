<?php
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `enrollee_list` where enrollee_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<style>
    #logo-img{
        width:75px;
        height:75px;
        object-fit:scale-down;
        background : var(--bs-light);
        object-position:center center;
        border:1px solid var(--bs-dark);
        border-radius:50% 50%;
    }
</style>
<div class="container-fluid">
    <form action="" id="enrollee-form">
        <input type="hidden" name="id" value="<?php echo isset($enrollee_id) ? $enrollee_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reference_code" class="control-label">Reference Code</label>
                        <input type="text" name="reference_code" autofocus id="reference_code" required class="form-control form-control-sm rounded-0" value="<?php echo isset($reference_code) ? $reference_code : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="control-label">First Name</label>
                        <input type="text" name="firstname" id="firstname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($firstname) ? $firstname : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="middlename" class="control-label">Middle Name</label>
                        <input type="text" name="middlename" id="middlename" required class="form-control form-control-sm rounded-0" placeholder="(optional)" value="<?php echo isset($middlename) ? $middlename : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="control-label">Last Name</label>
                        <input type="text" name="lastname" id="lastname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($lastname) ? $lastname : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="gender" class="control-label">Gender</label>
                        <select name="gender" id="gender" class="form-select form-select-sm rounded-0">
                            <option value="Male" <?php echo (isset($gender) && $gender == "Male" ) ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?php echo (isset($gender) && $gender == "Female" ) ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" name="email" id="email" required class="form-control form-control-sm rounded-0" value="<?php echo isset($email) ? $email : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact" class="control-label">Contact</label>
                        <input type="text" name="contact" id="contact" required class="form-control form-control-sm rounded-0" value="<?php echo isset($contact) ? $contact : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address" class="control-label">Address</label>
                        <textarea name="address" id="address" cols="30" rows="3" style="resize:none" class="form-control form-control-sm rounded-0" required><?php echo isset($address) ? $address : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="course_id" class="control-label">Course</label>
                        <select name="course_id" id="course_id" class="form-select form-select-sm rounded-0">
                            <option <?php echo (!isset($course_id)) ? 'selected' : '' ?> disabled>Please Select course</option>
                            <?php
                            $course = $conn->query("SELECT * FROM course_list where `status` = 1 ".(isset($course_id) ? " or course_id ='{$course_id}'" : "")." order by `name` asc");
                            while($row= $course->fetchArray()):
                            ?>
                                <option value="<?php echo $row['course_id'] ?>" <?php echo (isset($course_id) && $course_id == $row['course_id'] ) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-0">
                            <option value="1" <?php echo (isset($status) && $status == 1 ) ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?php echo (isset($status) && $status == 0 ) ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function display_img(input){
        if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#logo-img').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
    }
    $(function(){
        $('#enrollee-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_enrollee',
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
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($enrollee_id) ?>" != 1)
                        _this.get(0).reset();
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
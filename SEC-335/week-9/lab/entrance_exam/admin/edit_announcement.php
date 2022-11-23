<?php 
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `announcement_list` where announcement_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="container-fluid">
    <form action="" id="form-announcement-modal">
        <input type="hidden" name="id" value="<?php echo isset($announcement_id) ? $announcement_id :"" ?>">
        <div class="form-group">
            <label for="" class="control-label">Write your Announcement here</label>
            <textarea name="content" id="content" cols="30" rows="10" class="from-control form-control-sm summernote"><?php echo isset($content) ? html_entity_decode($content) :"" ?></textarea>
        </div>
        <div class="form-group">
            <div class="w-100 py-2 justify-content-end d-flex">
                <button class="btn btn-sm btn-primary rounded-0 me-1">Save</button>
                <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#form-announcement-modal').submit(function(e){
            e.preventDefault()
            var _this = $(this)
                _this.find('button').attr('disabled',true)
                _this.find('button:submit').text('Posting Announcement')
            $('.pop_msg').remove()
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $.ajax({
                url:'./../Actions.php?a=save_announcement',
                method:'POST',
                data:$(this).serialize(),
                dataType:'json',
                error:err=>{
                    console.log(err)
                    _this.find('button').attr('disabled',false)
                    _this.find('button:submit').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                    }else{
                        _el.addClass('alert alert-danger')
                        _el.text(resp.msg)
                        _el.hide()
                        _this.prepend(_el)
                        _el.show('slow')
                    }
                    _this.find('button').attr('disabled',false)
                    _this.find('button:submit').text('Save')
                }
            })
        })
    })
</script>
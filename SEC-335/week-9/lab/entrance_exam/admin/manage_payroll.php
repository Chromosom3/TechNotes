<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `payroll_list` where payroll_id = '{$_GET['id']}'");
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
    .earning-item .btn-close,.deduction-item .btn-close{
        font-size: 11px !important;
    }
</style>
<div class="card">
    <div class="card-header">
        <h5 class="card-title"><?php echo isset($_GET['id'])? "Update" :"Create New" ?> enrollee's Payroll</h5>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <form action="" id="payroll-form">
                <input type="hidden" name="id" value="<?php echo isset($payroll_id) ? $payroll_id : '' ?>">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="payroll_type" class="control-label">Payroll Type</label>
                            <select name="payroll_type" id="payroll_type" class="form-select form-select-sm rounded-0">
                                <option value="1" <?php echo (isset($payroll_type) && $payroll_type == 1 ) ? 'selected' : '' ?>>Monthly</option>
                                <option value="2" <?php echo (isset($payroll_type) && $payroll_type == 2 ) ? 'selected' : '' ?>>Semi-Monthly</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payroll_month" class="control-label">Payroll Month</label>
                            <input type="month" name="payroll_month" id="payroll_month" class="form-control form-control-sm rounded 0 text-end" value="<?php echo isset($payroll_month) ? $payroll_month : 0 ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="enrollee_id" class="control-label">enrollee</label>
                            <select name="enrollee_id" id="enrollee_id" class="form-select form-select-sm rounded-0">
                                <option value="" disabled <?php echo !isset($payroll_id) ? "selected" : "" ?>>Select enrollee Here</option>
                                <?php 
                                $qry =  $conn->query("SELECT *,(lastname || ', ' || firstname || ' ' || middlename) as name FROM `enrollee_list` where `status` = 1 order by name asc");
                                while($row = $qry->fetchArray()):
                                ?>
                                <option value="<?php echo $row['enrollee_id'] ?>" <?php echo isset($enrollee_id) && $enrollee_id == $row['enrollee_id'] ? "selected" : "" ?>><?php echo $row['code']. " - ". $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="daily_rate" class="control-label">Daily Rate</label>
                            <input type="number" step="any" name="daily_rate" id="daily_rate" class="form-control form-control-sm rounded 0 text-end" value="<?php echo isset($daily_rate) ? $daily_rate : 0 ?>" readonly>
                            <input type="hidden" step="any" name="monthly_rate" id="monthly_rate" class="form-control form-control-sm rounded 0 text-end" value="<?php echo isset($monthly_rate) ? $monthly_rate : 0 ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="hourly" class="control-label">Rate Per Hour</label>
                            <input type="number" step="any" name="hourly" id="hourly" class="form-control form-control-sm rounded 0 text-end" value="<?php echo isset($hourly) ? $hourly : 0 ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="per_minute" class="control-label">Rate Per Minute</label>
                            <input type="number" step="any" name="per_minute" id="per_minute" class="form-control form-control-sm rounded 0 text-end" value="<?php echo isset($per_minute) ? $per_minute : 0 ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="no_present" class="control-label">Days Present</label>
                            <input type="number" step="any" name="no_present" id="no_present" class="form-control form-control-sm rounded 0 text-end" value="<?php echo isset($no_present) ? $no_present : 0 ?>" required>
                            <input type="hidden" name="no_absences" id="no_absences" value="<?php echo isset($no_absences) ? $no_absences : 0 ?>">
                        </div>
                        <div class="form-group">
                            <label for="late_undertime" class="control-label">Late + Undertime(minute/s)</label>
                            <input type="number" step="any" name="late_undertime" id="late_undertime" class="form-control form-control-sm rounded 0 text-end" value="<?php echo isset($late_undertime) ? $late_undertime : 0 ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ot_min" class="control-label">Overtime(minute/s)</label>
                            <input type="number" step="any" name="ot_min" id="ot_min" class="form-control form-control-sm rounded 0 text-end" value="<?php echo isset($ot_min) ? $ot_min : 0 ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label for="" class="text-muted"><b>Other Eearnings</b></label></div>
                        <div class="form-group">
                            <label for="earning_name" class="control-label">Label</label>
                            <input type="text" class="form-control form-control-sm rounded-0" id="earning_name">
                        </div>
                        <div class="form-group">
                            <label for="earning_amount" class="control-label">Amount</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="earning_amount">
                        </div>
                        <div class="form-group">
                            <div class="w-100 d-flex justify-content-between my-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="taxable">
                                <label class="form-check-label" for="taxable">
                                   Taxable
                                </label>
                            </div>
                            <button type="button" id="add_earning" class="btn btn-sm btn-primary rounded-0 py-1"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <hr>
                        <div id="earnings-holder">
                            <?php if(isset($payroll_id)): ?>
                                <?php 
                                $earnings = $conn->query("SELECT * FROM `earning_list` where payroll_id = '{$payroll_id}'");
                                while($row=$earnings->fetchArray()):
                                ?>
                                <div class="earning-item w-100 d-flex my-1 row-cols-2 align-items-center">
                                    <div class="col-6"><?php echo $row['name'] ?> <?php echo $row['taxable'] == 1 ? "<small class='text-muted'> (taxable)</small>" : "" ?></div>
                                    <div class="col-auto flex-grow-1 text-end"><?php echo $row['amount'] ?></div>
                                    <button type="button" class="btn-close col-auto px-1" aria-label="Close">
                                    <input type="hidden" name="earning_name[]" value="<?php echo $row['name'] ?>">
                                    <input type="hidden" name="earning_amount[]" value="<?php echo $row['amount'] ?>">
                                    <input type="hidden" name="earning_tax[]" value="<?php echo $row['taxable'] ?>">
                                </div>
                            <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                        <div class="form-group"><label for="" class="text-muted"><b>Other Deduction</b></label></div>
                        <div class="form-group">
                            <label for="deduction_name" class="control-label">Label</label>
                            <input type="text" class="form-control form-control-sm rounded-0" id="deduction_name">
                        </div>
                        <div class="form-group">
                            <label for="deduction_amount" class="control-label">Amount</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="deduction_amount">
                        </div>
                        <div class="form-group">
                            <div class="w-100 d-flex justify-content-end my-1">
                            <button type="button" id="add_deduction" class="btn btn-sm btn-primary rounded-0 py-1"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <hr>
                        <div id="deductions-holder">
                        <?php if(isset($payroll_id)): ?>
                                <?php 
                                $deductions = $conn->query("SELECT * FROM `deduction_list` where payroll_id = '{$payroll_id}'");
                                while($row=$deductions->fetchArray()):
                                ?>
                                <div class="deduction-item w-100 d-flex my-1 row-cols-2 align-items-center">
                                    <div class="col-6"><?php echo $row['name'] ?></div>
                                    <div class="col-auto flex-grow-1 text-end"><?php echo $row['amount'] ?></div>
                                    <button type="button" class="btn-close col-auto px-1" aria-label="Close">
                                        <input type="hidden" name="deduction_name[]" value="<?php echo $row['name'] ?>">
                                        <input type="hidden" name="deduction_amount[]" value="<?php echo $row['amount'] ?>">
                                </div>
                            <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_earnings" class="control-label">Total Additional Earnings</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="total_earnings" name="total_earnings" value="<?php echo isset($total_earnings) ? $total_earnings : 0 ?>" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="total_deduction" class="control-label">Total Deductions</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="total_deduction" name="total_deduction" value="<?php echo isset($total_deduction) ? $total_deduction : 0 ?>" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="taxable_income" class="control-label">Total Taxable Income</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="taxable_income" name="taxable_income" value="<?php echo isset($taxable_income) ? $taxable_income : 0 ?>" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="gross_income" class="control-label">Gross Income</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="gross_income" name="gross_income" value="<?php echo isset($gross_income) ? $gross_income : 0 ?>" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="withholding_tax" class="control-label">Withholding Tax</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="withholding_tax" name="withholding_tax" value="<?php echo isset($withholding_tax) ? $withholding_tax : 0 ?>" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="nontaxable_income" class="control-label">Total Non-Taxable Income</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="nontaxable_income" name="nontaxable_income" value="<?php echo isset($nontaxable_income) ? $nontaxable_income : 0 ?>" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="net_pay" class="control-label">Net Pay</label>
                            <input type="number" step="any" class="form-control form-control-sm rounded-0 text-end" id="net_pay" name="net_pay" value="<?php echo isset($net_pay) ? $net_pay : 0 ?>" required readonly>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-12 d-flex justify-content-end">
            <div class="col-auto">
                <button class="btn btn-primary rounded-0 me-2" form="payroll-form">Save</button>
                <a class="btn btn-dark rounded-0" href="./?page=payroll_list">Back</a>
            </div>
        </div>
    </div>
</div>

<script>
    function calculate(){
        var ptype = $('#payroll_type').val()
        var daily = $('#daily_rate').val()
        var hourly = $('#hourly').val()
        var per_minute = $('#per_minute').val()
        var eid = $('#enrollee_id').val()

        if(eid == '' || daily == '')
        return false;
        $('button[form="payroll-form"]').attr('readonly',true).html("Calculating ...")
        var gross = 0;
        var taxable = 0;
        var nontaxable = 0;
        var earnings = 0;
        var deductions = 0;
        var net_pay = 0;

        // attendance
            // present
            var present = $('#no_present').val()
                present = present > 0 ? parseFloat(present) : 0;

                gross += present * parseFloat(daily);
                taxable += present * parseFloat(daily);
            
            // tardy & undertime
            var late_undertime = $('#late_undertime').val()
                late_undertime = late_undertime > 0 ? parseFloat(late_undertime) : 0;

                gross -= late_undertime * parseFloat(per_minute)
                taxable -= late_undertime * parseFloat(per_minute)

            // overtime
            var ot_min = $('#ot_min').val()
                ot_min = ot_min > 0 ? parseFloat(ot_min) : 0;

                gross += ot_min * parseFloat(per_minute)
                taxable += ot_min * parseFloat(per_minute)
            
            // additional earnings
            $('[name="earning_amount[]"]').each(function(){
                amount = $(this).val()
                label = $(this).siblings('[name="earning_name[]"]').val();
                istax = $(this).siblings('[name="earning_tax[]"]').val();
                console.log(amount,label,istax)
                if(amount > 0){
                    earnings += parseFloat(amount)
                    gross += parseFloat(amount)
                    if(istax == 1){
                        taxable += parseFloat(amount)
                    }else{
                        nontaxable += parseFloat(amount)
                    }
                }
            })
            // deductions
            $('[name="deduction_amount[]"]').each(function(){
                amount = $(this).val()
                label = $(this).siblings('[name="deduction_name[]"]').val();
                if(amount > 0){
                    deductions += parseFloat(amount)
                }
            })

        $('#no_absences').val((ptype == 1? 22 : 11) - present)
        $('#gross_income').val(gross.toFixed(3))
        $('#taxable_income').val(taxable.toFixed(3))
        $('#nontaxable_income').val(nontaxable.toFixed(3))
        $('#total_deduction').val(deductions.toFixed(3))
        $('#total_earnings').val(earnings.toFixed(3))
        net_pay += (gross - deductions)
        if(taxable > 0){
            $.ajax({
                url:'../Actions.php?a=get_tax',
                method:'POST',
                data:{amount:taxable,ptype:ptype},
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert("There's an error occured while fetching the withholding tax amount.")
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        $('#withholding_tax').val(resp.tax.toFixed(3))
                        net_pay -= parseFloat(resp.tax.toFixed(3))
                    }else{
                        console.log(resp)
                        alert("There's an error occured while fetching the withholding tax amount.")
                    }
                    $('button[form="payroll-form"]').attr('disabled',false).html("Save")
                }
            })
        }else{
            $('button[form="payroll-form"]').attr('disabled',false).html("Save")
        }
        $('#net_pay').val(net_pay.toFixed(3))
    }
    $(function(){
        $('#no_present, #late_undertime,#ot_min,#payroll_type').on('input',function(){
            calculate()
        })
        $('#earnings-holder .btn-close').click(function(){
            $(this).closest('.earning-item').remove()
            calculate()
        })
        $('#deductions-holder .btn-close').click(function(){
            $(this).closest('.deduction-item').remove()
            calculate()
        })
        $('#add_earning').click(function(){
            var label = $('#earning_name').val()
            var amount = $('#earning_amount').val()
            var tax = $('#taxable').is(":checked")
            if(label == '' || amount == '')
            return false;
            var el = $('<div>')
                el.addClass('earning-item w-100 d-flex my-1 row-cols-2 align-items-center')
                el.append("<div class='col-6'>"+label+(tax? "<small class='text-muted'> (taxable)<small>" : "" )+"</div>")
                el.append("<div class='col-auto flex-grow-1 text-end'>"+amount+"</div>")
                close_btn = $('<button type="button" class="btn-close col-auto px-1" aria-label="Close">')
                el.append(close_btn)
                el.append('<input name="earning_name[]" type="hidden" value="'+label+'"/>')
                el.append('<input name="earning_amount[]" type="hidden" value="'+amount+'"/>')
                el.append('<input name="earning_tax[]" type="hidden" value="'+(tax? 1 : 0)+'"/>')
            $('#earnings-holder').append(el) 
            close_btn.click(function(){
                $(this).closest('.earning-item').remove()
                calculate()
            })
            $('#earning_name').val('')
            $('#earning_amount').val('')
            $('#taxable').removeAttr("checked")
            calculate()
        })
        $('#add_deduction').click(function(){
            var label = $('#deduction_name').val()
            var amount = $('#deduction_amount').val()
            var tax = $('#taxable').is(":checked")
            if(label == '' || amount == '')
            return false;
            var el = $('<div>')
                el.addClass('deduction-item w-100 d-flex my-1 row-cols-2 align-items-center')
                el.append("<div class='col-6'>"+label+"</div>")
                el.append("<div class='col-auto flex-grow-1 text-end'>"+amount+"</div>")
                close_btn = $('<button type="button" class="btn-close col-auto px-1" aria-label="Close">')
                el.append(close_btn)
                el.append('<input name="deduction_name[]" type="hidden" value="'+label+'"/>')
                el.append('<input name="deduction_amount[]" type="hidden" value="'+amount+'"/>')
            $('#deductions-holder').append(el) 
            close_btn.click(function(){
                $(this).closest('.deduction-item').remove()
                calculate()
            })
            $('#deduction_name').val('')
            $('#deduction_amount').val('')
            calculate()
        })
        $('#enrollee_id').change(function(){
            var eid= $(this).val()
            $.ajax({
                url:"../Actions.php?a=get_rate",
                method:'POST',
                data:{enrollee_id : eid},
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert("There's an error occured while fetching the enrollee's salary rate.")
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        $('#monthly_rate').val(resp.monthly)
                        $('#daily_rate').val(resp.daily)
                        $('#hourly').val(resp.hourly)
                        $('#per_minute').val(resp.per_minute)
                        calculate()
                    }else{
                        alert("There's an error occured while fetching the enrollee's salary rate.")
                        console.log(resp)

                    }
                }
            })
        })
        $('#payroll-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('.card-footer button').attr('disabled',true)
            $('.card-footer button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_payroll',
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
                     $('.card-footer button').attr('disabled',false)
                     $('.card-footer button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                       location.replace("./?page=view_payroll&id="+resp.id)
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    $('#page-container').animate({scrollTop:0},'fast')
                     $('.card-footer button').attr('disabled',false)
                     $('.card-footer button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
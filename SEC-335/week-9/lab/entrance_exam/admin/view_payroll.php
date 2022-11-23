<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT p.*,(e.lastname || ', ' || e.firstname || ' ' || e.middlename) as name,e.code, d.name as dept, dd.name as desg FROM `payroll_list` p inner join enrollee_list e on p.enrollee_id = e.enrollee_id inner join department_list d on e.department_id = d.department_id inner join course_list dd on e.course_id = dd.course_id where p.payroll_id = '{$_GET['id']}'");
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
    .truncate-5 {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
    }
    .logo-img{
        width:50px;
        height:50px;
        object-fit:scale-down;
        background : var(--bs-light);
        object-position:center center;
        border:1px solid var(--bs-dark);
        border-radius:50% 50%;
    }
</style>
<div class="card">
    <div class="card-header d-flex">
        <h5 class="card-title flex-grow-1">Payslip</h5>
        <div class="w-auto d-flex justify-content-end">
            <button type="button" class="btn btn-sm btn-success rounded-0 me-2" id="print"><i class="fa fa-print"></i> Print</button>
            <a class="btn btn-sm btn-dark rounded-0" href="./?page=payroll_list">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid" id="outprint">
            <div class="col-12">
                <div class="row">
                    <div class="col-6">
                        <dl>
                            <dt><b>enrollee</b></dt>
                            <dd><?php echo $code." - ".$name ?></dd>
                            <dt><b>Department</b></dt>
                            <dd><?php echo $dept ?></dd>
                            <dt><b>Position</b></dt>
                            <dd><?php echo $desg ?></dd>
                        </dl>
                    </div>
                    <div class="col-6">
                        <dl>
                            <dt><b>Payroll Type</b></dt>
                            <dd><?php echo $payroll_type == 1 ? "Monthly" : "Semi-Monthly" ?></dd>
                            <dt><b>Payroll Month</b></dt>
                            <dd><?php echo date("F, Y",strtotime($payroll_month)) ?></dd>
                            <dt><b>Basic</b></dt>
                            <dd><?php echo $payroll_type == 1 ? number_format($monthly_rate,2) : number_format($monthly_rate /2,2) ?></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <colgroup>
                                <col width="33.33%">
                                <col width="33.33%">
                                <col width="33.33%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="p-1"></th>
                                    <th class="p-1">Other Earnings</th>
                                    <th class="p-1">Deductions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-1">
                                        <dl>
                                            <dt><b>Daily Rate</b></dt>
                                            <dd class="ps-4"><?php echo number_format($daily_rate,3) ?></dd>
                                            <dt><b>Per Minute</b></dt>
                                            <dd class="ps-4"><?php echo number_format($daily_rate/8/60,3) ?></dd>
                                            <dt><b>No. of day/s Present</b></dt>
                                            <dd class="ps-4"><?php echo $no_present ." = ".(number_format($daily_rate * $no_present,3)) ?></dd>
                                            <dt><b>No. of day/s Absent</b></dt>
                                            <dd class="ps-4"><?php echo $no_absences ." = ".(number_format($daily_rate * $no_absences,3)) ?></dd>
                                            <dt><b>Tardy/Undertime (Mins)</b></dt>
                                            <dd class="ps-4"><?php echo $late_undertime." = ".(number_format($per_minute * $late_undertime,3)) ?></dd>
                                            <dt><b>Over Time (mins)</b></dt>
                                            <dd class="ps-4"><?php echo $ot_min." = ".(number_format($per_minute * $ot_min,3)) ?></dd>
                                        </dl>
                                    </td>
                                    <td class="py-1">
                                        <table class="w-100">
                                            <colgroup>
                                                <col width="50%">
                                                <col width="50%">
                                            </colgroup>
                                            <tbody>
                                                <?php 
                                                $earnings = $conn->query("SELECT * FROM `earning_list` where payroll_id = '{$payroll_id}'");
                                                while($row=$earnings->fetchArray()):
                                                ?>
                                                <tr>
                                                    <td class="py-1">
                                                        <?php echo $row['name'] . ($row['taxable'] == 1 ? " (<small class='text-muted'>taxable</small>)" : "") ?>
                                                    </td>
                                                    <td class="py-1 text-end"><?php echo number_format($row['amount'],2) ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="py-1">
                                        <table class="w-100">
                                            <colgroup>
                                                <col width="50%">
                                                <col width="50%">
                                            </colgroup>
                                            <tbody>
                                                <?php 
                                                $deductions = $conn->query("SELECT * FROM `deduction_list` where payroll_id = '{$payroll_id}'");
                                                while($row=$deductions->fetchArray()):
                                                ?>
                                                <tr>
                                                    <td class="py-1">
                                                        <?php echo $row['name'] ?>
                                                    </td>
                                                    <td class="py-1 text-end"><?php echo number_format($row['amount'],2) ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end bg-info bg-opacity-25 py-1" colspan="2">Gross Income</th>
                                    <th class='text-end py-1'><?php echo number_format($gross_income,2) ?></th>
                                </tr>
                                <tr>
                                    <th class="text-end bg-info bg-opacity-25 py-1" colspan="2">Taxable Income</th>
                                    <th class='text-end py-1'><?php echo number_format($taxable_income,2) ?></th>
                                </tr>
                                <tr>
                                    <th class="text-end bg-info bg-opacity-25 py-1" colspan="2">Widthholding Tax</th>
                                    <th class='text-end py-1'><?php echo number_format($withholding_tax,2) ?></th>
                                </tr>
                                <tr>
                                    <th class="text-end bg-info bg-opacity-25 py-1" colspan="2">Total Deductions</th>
                                    <th class='text-end py-1'><?php echo number_format($total_deduction,2) ?></th>
                                </tr>
                                <tr>
                                    <th class="text-end bg-info bg-opacity-25 py-1" colspan="2">Total Earnings</th>
                                    <th class='text-end py-1'><?php echo number_format($total_earnings,2) ?></th>
                                </tr>
                                <tr>
                                    <th class="text-end bg-info bg-opacity-25 py-1" colspan="2">Net Pay</th>
                                    <th class='text-end py-1 fs-5 fw-bold'><?php echo number_format($net_pay,2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
        $(document).trigger('scroll')
        $('.content-holder').each(function(){
            var _this = $(this)
            if($(this).get(0).offsetHeight < $(this).get(0).scrollHeight){
                var _el = $("<a href='javascript:void(0)'>")
                    _el.text("Read More")
                console.log(_this.get(0))
                _this.after(_el)
                _el.click(function(){
                    if($(this).text() == "Read More"){
                        _this.removeClass('truncate-5')
                        $(this).text("Read Less")
                    }else{
                        _this.addClass('truncate-5')
                        $(this).text("Read More")
                    }
                })
            }
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Announcement','edit_announcement.php?id='+$(this).attr('data-id'))
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this Announcement?",'delete_data',[$(this).attr('data-id')])
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
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_announcement',
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
</script>
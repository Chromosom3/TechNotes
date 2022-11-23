
<style>
    .logo-img{
        width:45px;
        height:45px;
        object-fit:scale-down;
        background : var(--bs-light);
        object-position:center center;
        border:1px solid var(--bs-dark);
        border-radius:50% 50%;
    }
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Report</h3>
        <div class="card-tools align-middle">
            <a class="btn btn-success btn-sm py-1 rounded-0" href="javascript:void(0)" id="print"><i class="fa fa-print"></i> Print</a>
        </div>
    </div>
    <div class="card-body">
        <div id="outprint">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="20%">
                <col width="30%">
                <col width="30%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Date Created</th>
                    <th class="text-center p-0">Enrollee</th>
                    <th class="text-center p-0">Exam Title</th>
                    <th class="text-center p-0">Score</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT a.*,(e.lastname || ', ' || e.firstname || ' ' || e.middlename) as name,e.reference_code,ex.name as exam FROM answered_list a inner join enrollee_list e on a.enrollee_id = e.enrollee_id inner join exam_set_list ex on a.exam_id = ex.exam_id where ex.status = 1 order by `name` asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1 text-end"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                    <td class="py-0 px-1 lh-1">
                        <small><?php echo $row['reference_code'] ?></small><br>
                        <small><?php echo $row['name'] ?></small>
                    </td>
                    <td class="py-0 px-1"><?php echo $row['exam'] ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['score']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#print').click(function(){
            var _p = $('#outprint').clone()
            var _h = $('head').clone()
            var el = $('<div>')
            el.append(_h)
            el.append('<h2 class="text-center fw-bold">List of Exam Result</h2>')
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
</script>
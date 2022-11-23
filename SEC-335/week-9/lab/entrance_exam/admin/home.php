<h3>Welcome to Local Offices Contact Directories Site</h3>
<hr>
<div class="col-12">
    <div class="row gx-3 row-cols-4">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-building fs-3 text-success"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Department</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $department = $conn->query("SELECT count(department_id) as `count` FROM `department_list` where status = 1 ")->fetchArray()['count'];
                                echo $department > 0 ? number_format($department) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-th-list fs-3 text-primary"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Course</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $course = $conn->query("SELECT count(course_id) as `count` FROM `course_list` where status = 1 ")->fetchArray()['count'];
                                echo $course > 0 ? number_format($course) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-user-tie fs-3 text-warning"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Enrollees</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $enrollees = $conn->query("SELECT count(enrollee_id) as `count` FROM `enrollee_list` where status = 1 ")->fetchArray()['count'];
                                echo $enrollees > 0 ? number_format($enrollees) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-users fs-3 text-primary"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Users</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $admin = $conn->query("SELECT count(admin_id) as `count` FROM `admin_list`")->fetchArray()['count'];
                                echo $admin > 0 ? number_format($admin) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.restock').click(function(){
            uni_modal('Add New Stock for <span class="text-primary">'+$(this).attr('data-name')+"</span>","manage_stock.php?pid="+$(this).attr('data-pid'))
        })
        $('table#inventory').dataTable()
    })
</script>
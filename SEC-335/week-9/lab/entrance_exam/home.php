<div class="container py-5">
    <h3><b>List of Exam/Test You must Take</b></h3>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th class="py-1 px-2">#</th>
                        <th class="py-1 px-2">Exam Title</th>
                        <th class="py-1 px-2">Information</th>
                        <th class="py-1 px-2">Status</th>
                        <th class="py-1 px-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $qry = $conn->query("SELECT * FROM exam_set_list where ('{$_SESSION['course_id']}' in (`course_ids`) or is_for_all_course = 1) and `status` = 1");
                    $i = 1;
                    while($row=$qry->fetchArray()):
                        $items = $conn->query("SELECT count(question_id) as `count` FROM `question_list` where qcategory_id in (SELECT qcategory_id FROM `question_category_list` where exam_id = '{$row['exam_id']}' ) ")->fetchArray()['count'];
                        $items = $items > 0 ? $items : 0;
                        $taken = $conn->query("SELECT count(answered_id) as `count` FROM `answered_list` where enrollee_id = '{$_SESSION['enrollee_id']}' and exam_id = '{$row['exam_id']}'")->fetchArray()['count'];
                        $is_taken = $taken > 0 ? 1 : '';
                    ?>
                    <tr>
                        <td class="py-1 px-2"><?php echo $i++ ?></td>
                        <td class="py-1 px-2"><?php echo $row['name'] ?></td>
                        <td class="py-1 px-2">
                            <small><span class="tex-muted">Duration: </span><?php echo $row['duration'] ?> mins.</small> <br>
                            <small><span class="tex-muted">No. of Items: </span><?php echo $items ?></small> <br>
                        </td>
                        <td class="py-1 px-2 text-center">
                            <?php if($is_taken == 1): ?>
                                <span class="badge bg-success">Taken</span>
                            <?php else: ?>
                                <span class="badge bg-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-1 px-2 text-center"">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0"  aria-expanded="false"  <?php echo $is_taken == 1 ? 'disabled' : 'data-bs-toggle="dropdown"' ?>>
                                Action
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <li><a class="dropdown-item" href="./?page=take_exam&id=<?php echo $row['exam_id'] ?>">Take Exam</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
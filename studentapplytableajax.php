<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . "/config/config.php");

if (isset($_GET['fetch_student_id'])) {
    $student_id = intval($_GET['fetch_student_id']);
    $sql_apps = "
        SELECT il.Int_Position AS position_applied, e.Comp_Name AS company,
               sa.App_Date, sa.App_Status
        FROM student_application sa
        JOIN intern_listings il ON sa.InternshipID = il.InternshipID
        JOIN employer e ON il.EmployerID = e.EmployerID
        WHERE sa.StudentID = $student_id
        ORDER BY sa.App_Date DESC";
    $res_apps = mysqli_query($conn, $sql_apps);
    ?>
    <table class="applications-table">
      <thead>
        <tr>
          <th>Position Applied</th>
          <th>Company</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($res_apps && mysqli_num_rows($res_apps) > 0): ?>
        <?php while($app = mysqli_fetch_assoc($res_apps)): ?>
          <tr>
            <td><?php echo htmlspecialchars($app['position_applied']); ?></td>
            <td><?php echo htmlspecialchars($app['company']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($app['App_Date'])); ?></td>
            <td>
              <?php
              $status = strtolower($app['App_Status']);
              $badge_class = 'badge-other';
              if ($status == 'pending') $badge_class = 'badge-pending';
              else if ($status == 'accepted') $badge_class = 'badge-accepted';
              else if ($status == 'rejected') $badge_class = 'badge-rejected';
              else if ($status == 'interview') $badge_class = 'badge-interview';
              else if ($status == 'offered') $badge_class = 'badge-offered';
              else if ($status == 'in review') $badge_class = 'badge-inreview';
              else if ($status == 'declined') $badge_class = 'badge-declined';
              ?>
              <span class="status-badge <?php echo $badge_class; ?>">
                <?php echo ucfirst($status); ?>
              </span>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" style="text-align:center;">No applications found.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
    <?php
}
if (isset($conn)) mysqli_close($conn);
?>
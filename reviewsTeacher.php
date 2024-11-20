<?php
  // الاتصال بقاعدة البيانات
  include('connectiondb.php'); 

  // بدء الجلسة للحصول على teacher_id
  session_start();
  if (!isset($_SESSION['teacher_id'])) {
    die("Error: teacher_id is not set in the session.");
}
  $teacher_id = $_SESSION['teacher_id']; // تأكد من أن teacher_id تم تخزينه في الجلسة

  // استعلام لحساب التقييم العام
  $avg_rating_query = "SELECT AVG(rating) as average_rating
                       FROM reviews r
                       INNER JOIN courses c ON r.course_id = c.course_id
                       WHERE c.teacher_id = ?";
  $stmt = $conn->prepare($avg_rating_query);
  $stmt->bind_param("i", $teacher_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $average_rating = round($row['average_rating'], 1); // حساب التقييم المتوسط

  // استعلام لجلب التقييمات المرتبطة بالكورسات التي يدرسها المعلم
  $query = "
    SELECT r.student_id, r.feedback, r.rating, c.title
    FROM reviews r
    INNER JOIN courses c ON r.course_id = c.course_id
    WHERE c.teacher_id = ?
  ";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $teacher_id);
  $stmt->execute();
  $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reviews & Ratings</title>
  <link rel="stylesheet" href="rawan.css">
</head>
<body>

  <!-- Header -->
  <header>
    <div class="container">
        <div class="logo-text-container">
            <img src="logo (4).png" alt="Langbloom Logo" class="logo">
            <span class="website-name">Langbloom</span>
        </div>
        <nav>
            <ul>
              <li><a href="homePageTeacher.html">Home</a></li>
              <li><a href="view course As teacher.html">Courses</a></li>
              <li><a href="incomingSession-teacher.html">Sessions</a></li>
              <li><a href="reviewsTeacher.html">Reviews & Ratings</a></li>
              <li><a href="requestPage.html">Requests</a></li>
              <li><a href="#footer">Contact us</a></li>
            </ul>
        </nav>
        <div class="user-icon">
            <img src="manageIcon1.png" alt="User Icon">
            <div class="dropdown">
                <a href="#">Manage Profile</a>
                <a href="#">Log out</a>
            </div>
        </div>
    </div>
  </header>

  <!-- Reviews Section -->
  <section class="reviews">
    <h2>Reviews & Ratings</h2>
    <p>Your Overall Rating: <span class="rating-teacher"><?php echo $average_rating; ?> of 5</span></p>
    <div class="stars-teacher">
      <?php 
        $full_stars = floor($average_rating);
        $half_star = ($average_rating - $full_stars) >= 0.5 ? '★' : '☆';
        $empty_stars = 5 - $full_stars - ($half_star == '★' ? 1 : 0);
        echo str_repeat('★', $full_stars) . $half_star . str_repeat('☆', $empty_stars); 
      ?>
    </div>

    <div class="review-list">
      <?php
        if ($result->num_rows > 0) {
          // إذا تم العثور على التقييمات في قاعدة البيانات
          while ($row = $result->fetch_assoc()) {
            $student_id = $row['student_id'];
            $feedback = $row['feedback'];
            $rating = $row['rating'];
            $title = $row['title'];
            echo "
            <div class='review'>
              <h3>$student_id (Course: $title)</h3>
              <p>$feedback</p>
              <div class='stars-teacher'>" . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . "</div>
            </div>
            ";
          }
        } else {
          echo "<p>No reviews available.</p>";
        }
      ?>
    </div>
  </section>

  <!-- Footer -->
  <footer id="footer">
    <div class="container footer-container">
        <div class="footer-section footer-logo">
            <img src="whiteLogo.png" alt="Langbloom Logo" class="footer-logo-img">
            <h3>Langbloom.</h3>
            <p>Follow on social service</p>
            <div class="social-icons">
                <a href="#"><img src="facbook.png" alt="Facebook"></a>
                <a href="#"><img src="instagram.png" alt="Instagram"></a>
            </div>
        </div>
        <div class="footer-section">
            <h3>Links</h3>
            <ul>
                <li><a href="homePageTeacher.html">Home</a></li>
                <li><a href="#about">About us</a></li>
                <li><a href="#footer">Contact us</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Support</h3>
            <p>Riyadh - Saudi Arabia</p>
            <p>+966555555555</p>
        </div>
    </div>
    <p class="copyright">&copy; Copyright 2024 all rights reserved</p>
  </footer>

</body>
</html>

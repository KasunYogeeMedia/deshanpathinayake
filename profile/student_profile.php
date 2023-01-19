<?php

session_start();

//$success_payment = $_SESSION['success'];


$a = time() + 60 * 60 * 24 * 30;

$exp_date = date("Y-m-d", $a);

require_once '../dashboard/dbconfig4.php';

include '../dashboard/conn.php';

if (!isset($_SESSION['reid'])) {

	header('location:../login.php');

	die();
}

class imageUpload

{

	var $name = '';

	var $upload_path = '../uploads/images/';

	var $max_file_size = 5000000;



	function __construct($name)

	{

		$this->name = $name;
	}

	private function checkExt($ext)

	{

		if ($ext != "jpg" && $ext != "png" && $ext != "jpeg" && $ext != "gif") {

			return 0;
		} else {

			return 1;
		}
	}

	private function checkFileSize($size)

	{

		if ($size > $this->max_file_size) {

			return 0;
		} else {

			return 1;
		}
	}

	private function clearImageName($string, $separator = '-')

	{

		$accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';

		$special_cases = array('&' => 'and', "'" => '');

		$string = mb_strtolower(trim($string), 'UTF-8');

		$string = str_replace(array_keys($special_cases), array_values($special_cases), $string);

		$string = preg_replace($accents_regex, '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));

		$string = preg_replace("/[^a-z0-9]/u", "$separator", $string);

		$string = preg_replace("/[$separator]+/u", "$separator", $string);

		return $string;
	}

	function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function setUploadPath($path)

	{

		$this->upload_path = $path;
	}

	public function setMaxfileSize($size)

	{

		$this->max_file_size = $size;
	}

	public function upload()

	{

		$img = basename($_FILES[$this->name]["name"]);

		$ext = pathinfo($img, PATHINFO_EXTENSION);

		$name = pathinfo($img, PATHINFO_FILENAME);

		$size = $_FILES[$this->name]["size"];

		if (!$this->checkExt($ext) || !$this->checkFileSize($size) || !getimagesize($_FILES[$this->name]["tmp_name"])) {

			return 0;
		} else {

			//$img_name = random_string(50);

			$img_name = $this->generateRandomString();

			$img_name = $img_name . '.' . $ext;

			$img_path = $this->upload_path . $img_name;

			//echo $img_path;



			if (move_uploaded_file($_FILES[$this->name]["tmp_name"], $img_path)) {

				return $img_name;
			} else {

				return 0;
			}
		}
	}
}

$image_qury = mysqli_query($conn, "SELECT * FROM lmsregister WHERE reid='" . $_SESSION['reid'] . "'");

$image_resalt = mysqli_fetch_array($image_qury);


if ($image_resalt['image'] == "") {

	$dis_image_path = "images/hd_dp.jpg";
} else {

	$dis_image_path = "uploadslip/" . $image_resalt['image'];
}

$user_qury = mysqli_query($conn, "SELECT * FROM lmsregister WHERE reid='$_SESSION[reid]'");

$user_resalt = mysqli_fetch_array($user_qury);

if (isset($_POST['submit_bt'])) {

	date_default_timezone_set("Asia/Colombo");

	$change_name = time();

	$upload_path = "uploadslip/";

	$upload_file = $upload_path . basename($change_name . $_FILES["fileName"]["name"]);

	$upload_real = str_replace(" ", "_", $upload_file);


	$img = new imageUpload("fileName");
	$img->setUploadPath("uploadslip/");

	if (!$database_name = $img->upload()) {

		$error = "Please check again your file!";
	}

	$created_at = date("Y-m-d H:i:s");



	foreach ($_POST['select_payment'] as $select_payment) {

		//echo $select_payment;

		$select_payment = explode(",", $select_payment); //teacher id,subject id, amount

		$subject_qury = mysqli_query($conn, "SELECT fees_valid_period FROM lmssubject WHERE sid='$select_payment[1]'");

		$subject_resalt = mysqli_fetch_array($subject_qury);

		if ($subject_resalt['fees_valid_period'] == "EOM") {

			$exp_date = date("Y-m-t", strtotime(date("Y-m-d")));
		} else if ($subject_resalt['fees_valid_period'] == "150D") {

			$exp_date = date('Y-m-d', strtotime('+150 day'));
		} else {

			$exp_date = date('Y-m-d', strtotime('+1 month'));
		}

		$year = explode('-', $_POST['paymonth'])[0];
		$month = explode('-', $_POST['paymonth'])[1];
		$this_month = date('m', strtotime('now'));
		$exp_date = date("Y-m-t", strtotime($_POST['paymonth']));

		//------------------------------

		$subject_valid_days = $subject_resalt['fees_valid_period'];

		$paying_month = $_POST['paymonth'];

		if (date("Y-m", strtotime($paying_month)) < date("Y-m")) {
			echo "Invalid month selected";
			exit;
		} else {

			if ($subject_valid_days == 1) {

				if (date("Y-m-d") <= date("Y-m-t", strtotime(date($paying_month)))) {

					//$fina_date = $this->db->query("SELECT DATE_ADD('".date('Y-m-d')."',INTERVAL + ".$subject_valid_days." DAY) as dd ")->row()->dd;Bank Payment

					$Q = mysqli_query($conn, "SELECT DATE_ADD('" . date('Y-m-d') . "',INTERVAL + " . $subject_valid_days . " DAY) as dd ");
					$R = mysqli_fetch_array($Q);
					$fina_date = $R['dd'];
				}
			} else if ($subject_valid_days == 30) {

				if (date("Y-m-d") <= date("Y-m-t", strtotime(date($paying_month)))) {

					$fina_date = date("Y-m-t", strtotime(date($paying_month)));
				} else {

					//$fina_date = $this->db->query("SELECT DATE_ADD('".date('Y-m-d')."',INTERVAL + ".$subject_valid_days." DAY) as dd ")->row()->dd;
					$Q = mysqli_query($conn, "SELECT DATE_ADD('" . date('Y-m-d') . "',INTERVAL + " . $subject_valid_days . " DAY) as dd ");
					$R = mysqli_fetch_array($Q);
					$fina_date = $R['dd'];
				}
			} else if ($subject_valid_days == 40) {

				if (date("Y-m-d") <= date("Y-m-t", strtotime(date($paying_month)))) {

					//$fina_date = $this->db->query("SELECT DATE_ADD('".date("Y-m-t", strtotime(date($paying_month)))."',INTERVAL + ".($subject_valid_days-30)." DAY) as dd ")->row()->dd;
					$Q = mysqli_query($conn, "SELECT DATE_ADD('" . date("Y-m-t", strtotime(date($paying_month))) . "',INTERVAL + " . ($subject_valid_days - 30) . " DAY) as dd ");
					$R = mysqli_fetch_array($Q);
					$fina_date = $R['dd'];
				}
			} else if ($subject_valid_days == 45) {

				if (date("Y-m-d") <= date("Y-m-t", strtotime(date($paying_month)))) {

					//$fina_date = $this->db->query("SELECT DATE_ADD('".date("Y-m-t", strtotime(date($paying_month)))."',INTERVAL + ".($subject_valid_days-30)." DAY) as dd ")->row()->dd;
					$Q = mysqli_query($conn, "SELECT DATE_ADD('" . date("Y-m-t", strtotime(date($paying_month))) . "',INTERVAL + " . ($subject_valid_days - 30) . " DAY) as dd ");
					$R = mysqli_fetch_array($Q);
					$fina_date = $R['dd'];
				}
			} else if ($subject_valid_days == 90) {

				if (date("Y-m-d") <= date("Y-m-t", strtotime(date($paying_month)))) {

					//$fina_date = $this->db->query("SELECT DATE_ADD('".date("Y-m-t", strtotime(date($paying_month)))."',INTERVAL + ".($subject_valid_days-30)." DAY) as dd ")->row()->dd;
					$Q = mysqli_query($conn, "SELECT DATE_ADD('" . date("Y-m-t", strtotime(date($paying_month))) . "',INTERVAL + " . ($subject_valid_days - 30) . " DAY) as dd ");
					$R = mysqli_fetch_array($Q);
					$fina_date = $R['dd'];
				}
			}

			$exp_date = $fina_date;
		}

		//-----------------------

		$sql = "SELECT * FROM lmspayment WHERE pay_month ='" . $paying_month . "-01' AND userID=" . $_SESSION['reid'] . " AND pay_sub_id = '" . $select_payment[1] . "'";

		$query = mysqli_query($conn, $sql);

		if (mysqli_fetch_array($query)) {

			$R = mysqli_fetch_array($query);

			if ($R['status'] == 1) {
				$error = "ඔබ දැනටමත් මෙම මාසය සදහා පන්ති ගාස්තු ගෙවා ඇත!!";
			} else {
				$error = "අපගේ පද්ධතියේ දත්ත අනුව ඔබ දැනටමත් මෙම මාසය සදහා පන්ති ගාස්තු ගෙවා ඇත. එය තහවුරු කල සැනින් ඔබට දැනුම් දෙනු ඇත";
			}
		}

		if (!isset($error)) {

			$sql = "INSERT INTO lmspayment (`fileName`, `userID`, `feeID`, `pay_sub_id`, `amount`, `accountnumber`, `bank`, `branch`, `paymentMethod`, `created_at`, `expiredate`, `session_id`, `status`, `order_status`,`pay_month`)

				VALUES ('$database_name', '$_SESSION[reid]', '$select_payment[0]', '$select_payment[1]', '$select_payment[2]', '0', 'Pay Bank', 'Online Class', 'Bank', '$created_at', '$exp_date', '0', '0', '0' , '" . $paying_month . "-01" . "')";

			//echo $sql;exit;

			mysqli_query($conn, $sql);
		} else {

			header("location:student_profile.php?error='" . $error);
			die();
		}
	}

	echo "<script>window.location='student_profile.php?payed';</script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, shrink-to-fit=9">
	<meta name="description" content="https://siyosip.lk/lms/">
	<meta name="author" content="https://siyosip.lk/lms/">
	<title>My Profile | Online Learning Platforms | Student Panel</title>
	<?php
	require_once 'headercss.php';
	?>
</head>

<body>

	<?php
	require_once 'header.php';
	?>

	<?php
	require_once 'sidebarmenu.php';
	?>

	<!-- Body Start -->
	<div class="wrapper">
		<div class="sa4d25">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 col-lg-12 col-xl-12 col-sm-12">

						<?php if (isset($_GET['success'])) { ?>

							<div style="color:#ffffff; border-radius: 4px; font-family: Rubik; font-weight: bold; font-size:18px; text-align: center; padding: 20px; margin-bottom: 20px; background-color:green;">

								<i class="fa fa-check-circle"></i> Card Payment මගින් ඔබ සාර්ථකව පන්ති ගාස්තු ගෙවා ඇත !!

							</div>

						<?php } else if (isset($fail)) { ?>
							<div style="color:#ffffff; border-radius: 4px; font-family: Rubik; font-weight: bold; font-size:18px; text-align: center; padding: 20px; margin-bottom: 20px; background-color:red;">

								<i class="fa fa-times-circle"></i> Card Payment Fail !!

							</div>

						<?php }
						unset($_SESSION["success"]);
						unset($_SESSION["fail"]);
						?>

						<?php if (isset($_GET['payed'])) { ?>

							<div style="color:#ffffff; border-radius: 4px; font-family: Rubik; font-weight: bold; font-size:18px; text-align: center; padding: 20px; margin-bottom: 20px; background-color:green;">

								<i class="fa fa-check-circle"></i> ඔබගේ බැංකු රිසිට්පත ඔබ සාර්ථකව UPLOAD කරන ලදී.එය අනුමත වූ විට ඔබට SMS එකක් මගින් දැනුම් දෙනු ඇත !!

							</div>

						<?php } ?>

						<?php if (isset($_GET['error'])) { ?>

							<div style="color:#ffffff; border-radius: 4px; font-family: Rubik; font-weight: bold; font-size:18px; text-align: center; padding: 20px; margin-bottom: 20px; background-color:red;">

								<i class="fa fa-times-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>

							</div>

						<?php } ?>

					</div>
					<div class="col-lg-12">
						<h2 class="st_title">Main Menu</h2>
						<hr>
					</div>
					<div class="section-border">
						<h2 class="section-border-heading">Free Classes</h2>
						<div class="row">
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Free Live Classes</h2><br>
										<h5></h5>

										<a href="free_class.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right">
										<img src="images/dashboard/Free Live Class.png" alt="">
									</div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Download Free Class
											Tutes</h2><br>
										<h5></h5> <a href="free_class_tutes.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Free Class Tute.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Free Exams</h2><br>
										<h5></h5>

										<a href="exam_list.php?type=0" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right">
										<img src="images/dashboard/Free Exams.png" alt="">
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="section-border">
						<h2 class="section-border-heading">Paid Classes</h2>
						<div class="row">
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Paid Live Classes</h2><br>
										<h5></h5> <a href="online_class.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Paid Live Class.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Download Paid Class
											Tutes</h2><br>
										<h5></h5> <a href="online_class_tutes.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Download Paid Class Tute.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Paid Paper Classes</h2><br>
										<h5></h5> <a href="paper_class.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Paid Revision Class.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Download Paid Paper Class
											Tutes</h2><br>
										<h5></h5> <a href="paper_class_tutes.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Download Paid revision class tute.png" alt=""> </div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Paid Exams<h2><br>
												<h5></h5>

												<a href="exam_list.php?type=1" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right">
										<img src="images/dashboard/Paid Exams.png" alt="">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="section-border">
						<h2 class="section-border-heading">Lesson Recordings/Videos</h2>
						<div class="row">
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>This Month's Recordings</h2><br>
										<h5></h5> <a href="paid_lesson.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/This month Recordings.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>All Previous Recordings</h2><br>
										<h5></h5> <a href="old_video_lessons.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/All Previouus recording.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Free Recorded Classes</h2><br>
										<h5></h5> <a href="free_lesson.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Free Recode Class.png" alt=""> </div>
								</div>
							</div>
						</div>
					</div>
					<div class="section-border">
						<h2 class="section-border-heading">Profile & Payments</h2>
						<div class="row">
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Your Profile</h2><br>
										<h5></h5> <a href="edit_profile.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Your Profile.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Bank Payments History</h2><br>
										<h5></h5> <a href="bank_payment.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Bank Payment History.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Card Payments History</h2><br>
										<h5></h5> <a href="card_payment.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Card Payment History.png" alt=""> </div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Manual Payments History</h2><br>
										<h5></h5> <a href="manual_payment.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Manual Payment History.png" alt=""> </div>
								</div>
							</div>
						</div>
					</div>
					<div class="section-border">
						<h2 class="section-border-heading">Feedback</h2>
						<div class="row">
							<div class="col-xl-3 col-lg-6 col-md-6">
								<div class="card_dash">
									<div class="card_dash_left">
										<h2>Rate Your Learning Experience</h2><br>
										<h5></h5> <a href="reviews.php" class="btn btn-success">View More</a>
									</div>
									<div class="card_dash_right"> <img src="images/dashboard/Rate Your Experince.png" alt=""> </div>
								</div>
							</div>
						</div>
					</div>
					<div class="card_dash">
						<a href="edit_profile.php" class="btn btn-success btn-block">Select and Update Your Course/Subjects</a>
					</div>
				</div>
				<div class="row">
					<div class="col-xl-12">
						<div class="section3126">
							<div class="row">
								<div class="col-md-6">
									<?php

									$reid = $_SESSION['reid'];

									$stmt = $DB_con->prepare('SELECT * FROM lmsregister where reid="' . $reid . '"');

									$stmt->execute();

									if ($stmt->rowCount() > 0) {

										while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

											extract($row);

											$reg_id = $row["contactnumber"];

									?>
											<div class="right_side">
												<div class="fcrse_2 mb-30">
													<div class="tutor_img">
														<?php if ($row['image'] == "") {
															$pro_img = "images/hd_dp.jpg";
														} else {
															$pro_img = "uploadImg/" . $row['image'];
														} ?><img src="<?php echo $pro_img; ?>" id="dis_image" style="width: 200px; height: 200px; border: 1px solid #EEE; border-radius: 100%; cursor: pointer; object-fit: cover; background-position: center;">
													</div>
													<div class="tutor_content_dt">
														<div class="tutor150"> <a href="#" class="tutor_name">Hi,<?php echo $row['fullname']; ?></a>
															<div class="mef78" title="Verify"> <i class="uil uil-check-circle"></i> </div>
														</div>
														<div class="tutor_cate">Address : <?php echo $row['address']; ?> </div>
														<hr>
														<div class="tutor_cate">Your Username : <?php echo "0" . (int)$row['contactnumber']; ?> </div> <a href="edit_profile.php" class="btn btn-primary">Go To Your Profile</a>
													</div>
												</div>
											</div>
									<?php

										}
									}

									?>
								</div>
								<div class="col-md-6">
									<div class="value_props">
										<h3>My Details</h3>
										<h4></h4>
										<table class="table table-bordered tabl-div">

											<tbody>
												<?php

												$reid = $_SESSION['reid'];

												$stmt = $DB_con->prepare('SELECT * FROM lmsregister where reid="' . $reid . '"');

												$stmt->execute();

												if ($stmt->rowCount() > 0) {

													while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

														extract($row);

														$reg_id = $row["contactnumber"];

												?>
														<tr>

															<td style="font-weight:bold;font-family:emoji;border: 4px solid #031133;color:#000000;">Name</td>
															<td style="font-weight:bold;font-family:emoji;border: 4px solid #031133;color:#000000;"><?php echo $row['fullname']; ?></td>
														</tr>
														<tr>

															<td style="font-weight:bold;font-family:emoji;border: 4px solid #031133;color:#000000;">Student Reg Number</td>
															<td style="font-weight:bold;font-family:emoji;border: 4px solid #031133;color:#000000;"><?php echo $row['stnumber']; ?></td>
														</tr>
														<tr>

															<td style="font-weight:bold;font-family:emoji;border: 4px solid #031133;color:#000000;">Contact</td>
															<td style="font-weight:bold;font-family:emoji;border: 4px solid #031133;color:#000000;"><?php echo "0" . (int)$row['contactnumber']; ?> <i class="text-danger">(User Name)</i></td>
														</tr>
														<tr>

															<td style="font-weight:bold;font-family:emoji;border: 4px solid #031133;color:#000000;">Address </td>
															<td style="font-weight:bold;font-family:emoji;border: 4px solid #031133;color:#000000;"><?php echo $row['address']; ?></td>
														</tr>
												<?php

													}
												}

												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6 new-sce-1">
									<div class="value_props">
										<h3>ගාස්තු ගෙවීම් සම්බන්ධව 👇
										</h3>
										<div class="value_content">

											⭕️ පියවර අංක 1 <br> <br>
											පහත ගිණුම් අංක අතරින් ඔබට පහසු බැංකු ගිණුමක් තෝරාගෙන ගෙවීම් කල හැකිය. <br>
											<br>
											✳️ BANK OF CEYLON (BOC) <br>
											ගිණුම් අංකය - 87945248 <br>
											නම - D.P.M. Pathinayaka <br>
											ශාඛාව - kandy city centre <br>
											<br>
											✳️ Peoples Bank <br>
											ගිණුම් අංකය - 357200130007747 <br>
											නම - D.P.M. Pathinayaka <br>
											ශාඛාව - kandy city centre <br>
											<br>

											✳️ Commercial Bank <br>
											ගිණුම් අංකය - 8114004274 <br>
											නම - D.P.M. Pathinayaka <br>
											ශාඛාව - kandy city centre <br>
											<br>
											✳️ Sampath Bank <br>
											ගිණුම් අංකය - 112354628689 <br>
											නම - D.P.M. Pathinayaka <br>
											ශාඛාව - kandy city centre <br>
											<br>
											ඉහත සියලු බැංකු ගිණුම් සදහා Card Payment , Online Payment සිදුකල හැකිය.
											<br>

										</div>
									</div>
								</div>
								<div class="col-lg-6 new-sce-1">
									<div class="value_props">
										<!-- <h3>ගෙවීම් කළ හැකි ගිණුම් අංකය</h3> -->
										<div class="value_content">
											⭕️ පියවර අංක 2 <br> <br>

											1. රිසිට් පතේ මුද්‍රිත පැත්තේ ඉඩ ඇති හිස් ප්‍රදේශයක ඔබගේ <br> <br>

											👉 Web Username <br>

											👉 ඔබගේ ජාතික හැදුනුම්පත් අංකය <br>

											👉 ඔබගේ නම <br>

											👉 සම්බන්ධ වන පන්තිය <br>

											👉 ගෙවීම් අදාල මාසය <br> <br>

											⭕️⭕️පැහැදිලිව පෑනකින් සදහන් කරන්න. <br> <br>

											එම රිසිට් පතේ පැහැදිලි සම්පූර්ණ photo එකක් ලබාගෙන වෙබ් අඩවියට Upload කරන්න <br> <br>

											➡️ රිසිට් පතේ ලියූ විස්තර ටිපෙට්ස් කිරීම , ඉරි වලින් කැපීම හෝ කුරුටු ගෑම හා සම්පූර්ණ ‍රිසිට් පතම දිස්නොවන photo එවීම වැනි දේවල් වලින් රිසිට් පතේ වලංගු භාවය අහිමි වේ. <br> <br>

											✅ online payment කරන අයට app එකේ පිරවීමට එන කොටසේ <br>

											📱 Remark / Discription <br>
											ස්ථානයේ ඔබගේ Username අනිවාර්යයෙන් සදහන් කල යුතුය. <br>
											<br>
											එම ස්ථානය සහ ගෙවීම් සාර්ථකයි ලෙස තිරයේ දිස්වන පණිවිඩයේ දිනය, වේලාව සදහන් photo එකක් හෝ Screan shot එකක් ලබාගෙන වෙබ් අඩවියට upload කරන්න. <br>
										</div>

									</div>
								</div>

								<div class="col-lg-6 new-sce-1">
									<div class="value_props">
										<h3>විෂයක් තෝරා ගන්නා ආකාරය</h3>
										<br>
										<p>ගුරුවරයා තෝරා ගැනීමට ඔබට නොපෙන්වයි හෝ ඔබට අදාල නොවන විෂයන් පෙන්වන්නේ නම් Your Profile ගොස් වෙන ශ්‍රේණියක් select කර නැවත ඔබට අදාල ශ්‍රේණිය select කරන්න. පසුව ලැබෙන ලැයිස්තුවෙන් ඔබට සම්බන්ධ විය යුතු විෂයන් සහ ගුරුවරුන් තෝරා ගත හැක. එසේ තෝරාගෙන update profile click කරන්න.</p>
										<form method="post" enctype="multipart/form-data">
											<table class="table table-bordered tabl-div">
												<?php

												$selected_subjects  = array();

												$query1 = mysqli_query($conn, "SELECT * FROM lmsreq_subject WHERE sub_req_reg_no =" . $reg_id);

												while ($result = mysqli_fetch_assoc($query1)) {

													$sub_id = $result['sub_req_sub_id'];

													array_push($selected_subjects, $sub_id);
												}

												$tea_qury = mysqli_query($conn, "SELECT tid,systemid,fullname FROM lmstealmsr ORDER BY fullname");

												while ($tea_resalt = mysqli_fetch_assoc($tea_qury)) {

												?>

													<thead>
														<tr style="background-color: #8b8c90;">
															<td colspan="6" style="color: #ffffff;border:4px solid #8b8c90;"><?php echo $tea_resalt['fullname']; ?></td>
														</tr>
													</thead>
													<tbody>
														<?php

														$tec_sub_qury = mysqli_query($conn, "SELECT ss.sid,ss.name,ss.price

FROM lmstealmsr_multiple sm INNER JOIN lmssubject ss ON sm.tealmsr_contain_id=ss.sid

WHERE sm.tealmsr_type='3' and sm.tealmsr_system_id='$tea_resalt[systemid]'

ORDER BY ss.name");

														while ($tec_sub_resalt = mysqli_fetch_assoc($tec_sub_qury)) {

															//check paid subject

															$check_paid = mysqli_query($conn, "SELECT * FROM lmspayment WHERE pay_sub_id='$tec_sub_resalt[sid]' and userID='$_SESSION[reid]' and status='1'");

															$paid_resalt = mysqli_fetch_array($check_paid);

															if (in_array($tec_sub_resalt['sid'], $selected_subjects)) {

														?>
																<tr>
																	<td><input style="font-weight:bold;margin: 10px;color:#000000;" class="subject_select" type="checkbox" name="select_payment[]" value="<?php echo $tea_resalt['tid'] . "," . $tec_sub_resalt['sid'] . "," . $tec_sub_resalt['price']; ?>" data-subject-fee="<?php echo $tec_sub_resalt['price']; ?>" data-subject-id="<?php echo $tec_sub_resalt['sid']; ?>"></td>

																	<td style="font-weight:bold;margin: 10px;color:#000000;"><?php echo $tec_sub_resalt['name']; ?></td>

																	<td style="font-weight:bold;margin: 10px;color:#000000;"><?php echo number_format((float)$tec_sub_resalt['price'], 2); ?></td>
																	<!--kasun 2021.12.01 change color to black from white-->
																</tr>
													<?php

															}
														}
													}

													?>
													</tbody>
											</table>
									</div>
								</div>
								<div class="col-lg-6 new-sce-1 paymet-det">
									<div class="value_props">
										<h3>Card Payment</h3>
										<?php
										$today_time = date("Y-m-d");
										$payment_qury = mysqli_query($conn, "SELECT * FROM lmspayment WHERE paymentMethod='Card' and userID='$_SESSION[reid]' and status='1' ORDER BY pid DESC");
										while ($payment_resalt = mysqli_fetch_array($payment_qury)) {
										?>
											<span class="badge badge-success" style="font-size:14px;color:#000000;">Paid Month : <i class="fa fa-check-circle"></i> <?php echo date_format(date_create($payment_resalt['pay_month']), "F"); ?></span>
										<?php
										}
										?>
										<h4>Select Month</h4>
										<input id="select_month" class="form-control" type="month" name="paymonth" value="<?php echo date("Y-m"); ?>">
										<br>
										<label for="fileName1"><img src="images/card payment.png" id="yourImgTag1" style="width:20%;cursor: pointer;" /></label>
										<ul>
											<li>
												<button type="submit" name="submit_bt" id="pay-by-card" class="btn btn-success btn-block" disabled="true" style="font-weight:bold;font-size:14px;">කාඩ් එකෙන් ගෙවන්න | Rs. <span class="payment_ammount">0.00</span></button>
											</li>
										</ul>
									</div>
									<hr style="border:2px solid #28a745">
									<div class="value_props">
										<h3>Bank Payment</h3>
										<hr style="border:2px solid #8b8c90">
										<?php
										$today_time = date("Y-m-d");
										$payment_qury = mysqli_query($conn, "SELECT * FROM lmspayment WHERE paymentMethod='Bank' and userID='$_SESSION[reid]' and status='1' ORDER BY pid DESC");
										while ($payment_resalt = mysqli_fetch_array($payment_qury)) {
										?>
											<span class="badge badge-success" style="font-size:14px;color:#000000;">Paid Month : <i class="fa fa-check-circle"></i> <?php echo date_format(date_create($payment_resalt['pay_month']), "F"); ?></span>
										<?php
										}
										?>
										<h4>Select Month</h4>
										<input type="month" class="form-control" name="paymonth" value="<?php echo date("Y-m"); ?>">
										<br>

										<label class="control-label" for="basicinput">සාමාන්‍ය පන්ති ගාස්තු ගෙවන දරුවන් සම්බන්ධ වන විෂයන් ඉදිරියේ හරි ලකුණු යොදා (click on the relevant tick box) මෙහි bank receipt පතෙහි photo එකක් හෝ screenshot එකක් මෙතනින් upload කරන්න. (Pdf file upload කල නොහැක)</label>
										<label for="fileName"><img src="images/payslip.png" id="yourImgTag" style="width:40%;cursor: pointer;" /></label>

										<input type="file" name="fileName" id="fileName" value="" class="form-control" required onChange="JavaScript:dis_name(this.value);">
										<br>
										<ul>
											<li>
												<input type="text" name="amount" id="payment_ammount" hidden>
												<button type="submit" name="submit_bt" id="bank-pay-button" class="btn btn-primary btn-block" disabled="true" style="font-weight:bold;font-size:14px;">බැංකු රිසිට්පතෙන් ගෙවන්න | Rs. <span class="payment_ammount">0.00</span></button>
											</li>
										</ul>
										<script>
											function dis_name(file_name) {

												var input = document.getElementById("fileName");

												var fReader = new FileReader();

												fReader.readAsDataURL(input.files[0]);

												fReader.onloadend = function(event) {

													var img = document.getElementById("yourImgTag");

													img.src = event.target.result;

												}

											}
										</script>
									</div>

								</div>
							</div>
							</form>
						</div>
					</div>

				</div>
			</div>

		</div>
		<!-- Body End -->
		<?php
		require_once 'footer.php';
		?>
		<?php
		require_once 'footerjs.php';
		?>

		<script type="text/javascript">
			var total = 0;

			$(".subject_select").click(function(argument) {

				var fee = $(this).data("subject-fee");

				if ($(this).prop("checked") == true) {

					total += fee;

				} else {

					total -= fee;

				}

				$(".payment_ammount").html(total);

				$("#payment_ammount").val(total);

				$("#online_pay").val(total);


				if (total == 0) {

					$("#pay-by-card").attr("disabled", "true");

					$("#bank-pay-button").attr("disabled", "true");

				} else {

					$("#pay-by-card").removeAttr("disabled");

					$("#bank-pay-button").removeAttr("disabled");

				}
			})

			$("#pay-by-card").click(function(e) {


				if ($("#select_month").val() == '') {
					alert("Please select the payment month!");
					return 0;
				}
				e.preventDefault();
				var value = $(".subject_select").serialize();
				var month = $("#select_month").val();
				var currString = "<?php echo $url ?>";
				// window.open("https://guruniwasainstitute.lk/lms/profile/online_pay.php?" + value + "&month=" + month , '_blank');
				window.location.href = currString + "/profile/online_pay.php?" + value + "&month=" + month;


			})


			window.addEventListener("pageshow", function(event) {
				var historyTraversal = event.persisted ||
					(typeof window.performance != "undefined" &&
						window.performance.navigation.type === 2);
				if (historyTraversal) {
					// Handle page restore.
					window.location.reload();
				}
			});
		</script>

</body>

</html>
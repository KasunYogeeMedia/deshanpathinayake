<?php
session_start();
error_reporting(0);

require_once 'dashboard/conn.php';
require_once 'dashboard/config.php';
require_once 'dashboard/dbconfig4.php';



$success_msg = 0;

if (isset($_POST['register'])) {
	$stnumber = mysqli_real_escape_string($con, $_POST['stnumber']);
	// $email = mysqli_real_escape_string($con, $_POST['email']);
	$dob = mysqli_real_escape_string($con, $_POST['dob']);
	//$gender = mysqli_real_escape_string($con, $_POST['gender']);
	//$school = mysqli_real_escape_string($con, $_POST['school']);
	$district = mysqli_real_escape_string($con, $_POST['district']);
	//$town = mysqli_real_escape_string($con, $_POST['town']);
	//$pcontactnumber = (int)mysqli_real_escape_string($conn, $_POST['pcontactnumber']);
	//$pemail = mysqli_real_escape_string($con, $_POST['pemail']);
	//$pname = mysqli_real_escape_string($con, $_POST['pname']);
	$fullname = mysqli_real_escape_string($con, $_POST['fullname']);
	//$address = mysqli_real_escape_string($con, $_POST['address']);
	$contactnumber = (int)mysqli_real_escape_string($conn, $_POST['contactnumber']);
	$to = "0" . (int)mysqli_real_escape_string($conn, $_POST['contactnumber']);
	$level = mysqli_real_escape_string($conn, $_POST['level']);
	$password = md5(mysqli_real_escape_string($con, $_POST['password']));
	$re_password = md5(mysqli_real_escape_string($con, $_POST['re_password']));

	if ($password == $re_password) {

		$amilack_mobile_qury = mysqli_query($con, "SELECT * FROM lmsregister WHERE contactnumber='$contactnumber'");
		if (mysqli_num_rows($amilack_mobile_qury) > 0) {
			//user allready
			$success_msg = 1;
		} else {
			//pass
			if (mysqli_query($con, "INSERT INTO lmsregister (stnumber,dob,gender,school,district,town,pcontactnumber,pemail,pname,fullname,contactnumber, address, level,password, image, add_date, status, ip_address, relogin, reloging_ip, payment, verifycode) VALUES ('$stnumber','$dob','$gender','$school','$district','$town','$pcontactnumber','$pemail','$pname','$fullname','$contactnumber','$address','$level','$password','', CURRENT_TIMESTAMP, '1', '', '0', '0', '0', '')")) {

				if (!empty($_POST['subjects'])) {
					foreach ($_POST['subjects'] as $subject_id) {
						mysqli_query($conn, "INSERT INTO lmsreq_subject(sub_req_reg_no, sub_req_sub_id) VALUES ('$contactnumber','$subject_id')");
					}
				}

				$to = "+94" . (int)mysqli_real_escape_string($conn, $_POST['contactnumber']);
				$message_text = "Congratulations on joining Click to Class! To log in please use the below details.\nUser name: $contactnumber\npassword: $_POST[password]\n";
				$message = urlencode($message_text);
				send_sms($to, $message_text);

				echo "<img src=''>";

				echo "<script>window.location='login.php?success';</script>";
			} else {
				//error
				$success_msg = 3;
			}
		}
	} else {
		//password error
		$success_msg = 2;
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, shrink-to-fit=9">
	<meta name="description" content="Online Learning Platforms ">
	<meta name="author" content="Online Learning Platforms ">
	<title>Register | Online Learning Platforms </title>

	<!-- Favicon Icon -->
	<link rel="icon" type="image/png" href="images/fav.png">

	<!-- Stylesheets -->
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,500' rel='stylesheet'>
	<link href='profile/vendor/unicons-2.0.1/css/unicons.css' rel='stylesheet'>
	<link href="profile/css/vertical-responsive-menu.min.css" rel="stylesheet">
	<link href="profile/css/style.css" rel="stylesheet">
	<link href="profile/css/responsive.css" rel="stylesheet">
	<link href="profile/css/night-mode.css" rel="stylesheet">

	<!-- Vendor Stylesheets -->
	<link href="profile/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
	<link href="profile/vendor/OwlCarousel/assets/owl.carousel.css" rel="stylesheet">
	<link href="profile/vendor/OwlCarousel/assets/owl.theme.default.min.css" rel="stylesheet">
	<link href="profile/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="profile/vendor/semantic/semantic.min.css">


	<style>
		input {
			border-radius: 0;
			box-shadow: none;
			height: 40px;
			font-size: 13px;
			line-height: 20px;
			padding: 9px 12px;
			width: 100%;
			border: 1px solid rgba(0, 0, 0, 0.2);
		}

		input:focus {

			border-radius: 0;
			box-shadow: none;
			height: 40px;
			font-size: 13px;
			line-height: 20px;
			padding: 9px 12px;
			width: 100%;
			border: 1px solid rgba(0, 0, 0, 0.2);
		}

		input.valid {
			border: 1px solid green;
			border-radius: 0;
			box-shadow: none;
			height: 40px;
			font-size: 13px;
			line-height: 20px;
			padding: 9px 12px;
			width: 100%;
		}

		input.invalid {
			border: 1px solid red;
			border-radius: 0;
			box-shadow: none;
			height: 40px;
			font-size: 13px;
			line-height: 20px;
			padding: 9px 12px;
			width: 100%;
		}

		input.invalid+.error-message {
			display: initial;
			color: red;
		}

		.error-message {
			display: none;
		}

		.error_Msg,
		.error_Msg2,
		.error_Msg3,
		.error_Msg4,
		.error_Msg5,
		.error_Msg6,
		.error_Msg7,
		.error_Msg8,
		.error_Msg9,
		.error_Msg10,
		.error_Msg11,
		.error_Msg12,
		.error_Msg13 {
			color: #fa4b2a;
			padding-left: 10px;
			font-family: Verdana;
			width: 100%;
		}

		.btn-primary:hover {
			border-color: #E62129;
		}
	</style>
	<!-- Google Tag Manager -->
	<script>
		(function(w, d, s, l, i) {
			w[l] = w[l] || [];
			w[l].push({
				'gtm.start': new Date().getTime(),
				event: 'gtm.js'
			});
			var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s),
				dl = l != 'dataLayer' ? '&l=' + l : '';
			j.async = true;
			j.src =
				'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
			f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer', 'GTM-5RH22F3');
	</script>
	<!-- End Google Tag Manager -->


</head>

<body>
	<!-- Signup Start -->
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5RH22F3" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<div class="sign_in_up_bg">
		<div class="container">
			<div class="row justify-content-lg-center justify-content-md-center">
				<div class="col-lg-12">
					<div class="main_logo25" id="logo">
						<!--<a href="index.php"><img src="./inc/images/logo.png" alt="" style="text-align:center;"></a>-->
						<!--<a href="index.php"><img class="img-responsive" src="./inc/images/logo.png" alt="Click to Class"></a>-->
					</div>
				</div>

				<div class="col-lg-6 col-md-8">
					<div class="sign_form">
						<h2>Welcome to <?php echo $application_name; ?> </h2>
						<p>Register Now & Start
							Learning Today!</p>
						<form method="POST" id="myform">
							<?php if ($success_msg == 1) { ?><div class="alert alert-primary" style="font-weight:bold;background-color:#007bff;color:#ffffff;">Sorry! You are already registered.</div><?php } ?>
							<?php if ($success_msg == 2) { ?><div class="alert alert-danger" style="font-weight:bold;background-color:#dc3545;color:#ffffff;">Error! The Re-Enter Password you entered does not match.</div><?php } ?>
							<?php if ($success_msg == 3) { ?><div class="alert alert-danger" style="font-weight:bold;background-color:#dc3545;color:#ffffff;">Error! Your entered details something is wrong. Please try again.</div><?php } ?>
							<?php if (isset($_GET['success'])) { ?><div class="alert alert-success" style="font-weight:bold;background-color:#E62129;color:#ffffff;"> Thanks for registering! Sign in now and start learning right away! </div><?php } ?>
							<div class="row">
								<div class="col-lg-6">
									<div class="single-form">
										<?php
										$code_feed = "0123456789";
										$code_length = 5;  // Set this to be your desired code length
										$final_code = "";
										$feed_length = strlen($code_feed);

										for ($i = 0; $i < $code_length; $i++) {
											$feed_selector = rand(0, $feed_length - 1);
											$final_code .= substr($code_feed, $feed_selector, 1);
										}

										?>
										<label style="font-weight:bold;text-align:left;">Student Number</label>
										<input name="stnumber" required type="text" class="form-control stnumber" value="<?php echo $reg_prefix; ?>-<?php echo $final_code; ?>" readonly>
									</div>
								</div>
								<!-- <div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Email</label>
										<input name="email" type="text" class="form-control email" placeholder="Enter Your Email" value="<?php if (isset($_POST['email'])) {
																																				echo $_POST['email'];
																																			} ?>">
									</div>
								</div> -->
							
							
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Full Name</label>
										<input name="fullname" required type="text" class="form-control fullname" placeholder="Enter Full Name" value="<?php if (isset($_POST['fullname'])) {
																																							echo $_POST['fullname'];
																																						} ?>">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Phone Number (Will be used for logging in)</label>
										<input name="contactnumber" type="text" required placeholder="Enter Phone Number" class="form-control phone_val" value="<?php if (isset($_POST['contactnumber'])) {
																																									echo $_POST['contactnumber'];
																																								} ?>" maxlength="10" minlength="10">
									</div>
								</div>
							
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Date Of Birth</label>
										<input name="dob" required type="date" class="form-control dob" placeholder="Enter Your Birthday" value="<?php if (isset($_POST['dob'])) {
																																						echo $_POST['dob'];
																																					} ?>">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Grade</label><br>
										<span id="class_load">
											<select name="level" required id="class_val" onChange="JavaScript:select_subject(this.value);" class="form-control simple" style="width:100%;">
												<option value="" hidden="yes">Select Grade</option>
												<?php
												$stmt = $DB_con->prepare('SELECT * FROM lmsclass ORDER BY cid');
												$stmt->execute();
												if ($stmt->rowCount() > 0) {
													while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
														extract($row);
												?>
														<option value="<?php echo $row['cid']; ?>"><?php echo $row['name']; ?></option>
												<?php }
												}
												?>
											</select>
										</span>
									</div>
								</div>
							</div>
							<script>
								function select_subject(sub_val) {
									var xhttp = new XMLHttpRequest();
									xhttp.onreadystatechange = function() {
										if (this.readyState == 4 && this.status == 200) {
											document.getElementById("sub_load").innerHTML = this.responseText;
										}
									};
									xhttp.open("GET", "sub_load.php?cid=" + sub_val, true);
									xhttp.send();
								}
							</script>
							<?php /*
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Gender</label>
										<select name="gender" required class="form-control gender">
											<option value="male">Male</option>
											<option value="female">Female</option>
										</select>
									</div>
								</div>
								
							</div>*/ ?>

							<?php /*
								<div class="row">
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Address</label>
										<input name="address" type="text" required placeholder="Enter Address" class="form-control phone_val" value="<?php if (isset($_POST['address'])) {
																																							echo $_POST['address'];
																																						} ?>">
									</div>
								</div>
								*/ ?>

							<div class="row">
								<div class="col-lg-12">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Subject</label>
										<br>
										<div id="sub_load">
											<hr>
											Subject Not Found
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<?php /*
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">School</label>
										<input name="school" required type="text" class="form-control school" placeholder="Enter Your School" value="<?php if (isset($_POST['school'])) {
																																							echo $_POST['school'];
																																						} ?>">
									</div>
								</div>
								*/ ?>
								<div class="col-lg-12">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">District</label>
										<select name="district" required class="form-control district">
											<option value="Ampara">Ampara</option>
											<option value="Anuradhapura">Anuradhapura</option>
											<option value="Badulla">Badulla</option>
											<option value="Batticaloa">Batticaloa</option>
											<option value="Colombo">Colombo</option>
											<option value="Galle">Galle</option>
											<option value="Gampaha">Gampaha</option>
											<option value="Hambantota">Hambantota</option>
											<option value="Jaffna">Jaffna</option>
											<option value="Kalutara">Kalutara</option>
											<option value="Kandy">Kandy</option>
											<option value="Kegalle">Kegalle</option>
											<option value="Kilinochchi">Kilinochchi</option>
											<option value="Kurunegala">Kurunegala</option>
											<option value="Mannar">Mannar</option>
											<option value="Matale">Matale</option>
											<option value="Matara">Matara</option>
											<option value="Monaragala">Monaragala</option>
											<option value="Mullaitivu">Mullaitivu</option>
											<option value="Nuwara-Eliya">Nuwara Eliya</option>
											<option value="Polonnaruwa">Polonnaruwa</option>
											<option value="Puttalam">Puttalam</option>
											<option value="Ratnapura">Ratnapura</option>
											<option value="Trincomalee">Trincomalee</option>
											<option value="Vavuniya">Vavuniya</option>
										</select>
									</div>
								</div>
							</div>
							<?php /*
							<div class="row">
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Town/City</label>
										<input name="town" type="text" class="form-control school" placeholder="Enter Your Town/City" value="">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Parent/Guardian's Name</label>
										<input name="pname" type="text" class="form-control" placeholder="Enter Parent/Guardian's name" value="">
									</div>
								</div>
							</div>

							<!-- newly aded section end -->
							<?php /*<div class="row">
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Parents/Guardian's Email</label>
										<input name="pemail" type="email" placeholder="Enter Parent/Guardian's Email" class="form-control" value="">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Parent/Guardian's
											Phone number</label>
										<input name="pcontactnumber" type="text" placeholder="Enter Parent Phone Number" class="form-control pcontactnumber" value="" maxlength="10" minlength="10">
									</div>
								</div>
							</div> */ ?>

							<div class="row">
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Password</label>
										<input name="password" type="password" class="form-control password" placeholder="Enter more than 8 characters" minlength="8">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="single-form">
										<label style="font-weight:bold;text-align:left;">Confirm Password</label>
										<input name="re_password" type="password" class="form-control passwordcon" placeholder="Enter your password again" minlength="8">
									</div>
								</div>
							</div>
							<!-- newly aded section start -->
							<div class="row">
								<div class="col" style="max-width:10%;">
									<div class="single-form">
										<input style="width: 20px;margin-top: 10px;" type="checkbox" name="agree" id="agree_checkbox" value="yes" />

									</div>
								</div>
								<div class="col">
									<div class="single-form">
										<label style="font-weight:bold; text-align:left" for="agree"> I agree to Click to Class's <a href="https://clicktoclass.lk/terms-conditions/" target="_blank">Terms & Conditions</a> and Privacy Policy
											and allow my child to be enrolled onto the platform </label>
									</div>
									<div id="result" style="background: yellow;"></div>
								</div>
							</div>

							<!-- newly aded section end -->
							<br>
							<div class="row">
								<div class="col-lg-12">
									<div class="single-form">
										<input type="submit" id="submit" disabled="" name="register" value="Register" class="btn btn-primary btn-block" style="background:#E62129;color:#ffffff; border-color: #E62129;">
									</div>
								</div>
							</div>
						</form>
						<p class="mb-0 mt-30">Already have an account? <a href="login.php">Log In</a></p>
					</div>
					<div class="sign_footer">© 2021 <?php echo $application_name; ?> | All Rights Reserved | Developed By Yogeemedia</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Signup End -->

	<script src="profile/js/jquery-3.3.1.min.js"></script>
	<script src="profile/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="profile/vendor/OwlCarousel/owl.carousel.js"></script>
	<script src="profile/vendor/semantic/semantic.min.js"></script>
	<script src="profile/js/custom.js"></script>
	<script src="profile/js/night-mode.js"></script>
	<script>
		$(document).ready(function() {
			$('input[type="checkbox"]').click(function() {
				if ($(this).prop("checked") == true) {
					$(':input[type="submit"]').prop('disabled', false);
				} else if ($(this).prop("checked") == false) {
					$(':input[type="submit"]').prop('disabled', true);
				}
			});
		});
	</script>
</body>

</html>
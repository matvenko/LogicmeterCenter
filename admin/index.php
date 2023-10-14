<?php
print_r($_SESSION);
require_once ("../functions/functions.php");
require_once ("../functions/db.php");
if (is_post('login')) {
	require_once ("../conf.php");
	$query = new sql_func();
	if ($query->amount_fields("users", "mail = '" . post('username') . "' AND password = '" . md5(post('password')) . "'") == 1) {
		$row_user = $query->select_obj_sql("users", "*", "mail = '" . post('username') . "' AND password = '" . md5(post('password')) . "'");
		@session_start();
		$_SESSION ['login'] ['username'] = $row_user->mail;
		$_SESSION ['login'] ['password'] = $row_user->password;
		$_SESSION ['login'] ['name_surname'] = $row_user->name_surname;
		$_SESSION ['login'] ['user_id'] = $row_user->id;
		$_SESSION ['login'] ['address'] = $row_user->address;
		$_SESSION ['login'] ['tel'] = $row_user->tel;
		$_SESSION ['login'] ['city'] = $row_user->city;
		$_SESSION ['login'] ['ubani'] = $row_user->ubani;
		
		@header("Location: ../admin.php?module=math");
		exit();
	} else
		$error = "Login Error";
}

?>

<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../style/style.css" rel="stylesheet" type="text/css">
<title>Admin</title>
</head>

<body style="background: #fff">
	<form method="post">
		<table border="0" width="100%" id="table1" height="100%">
			<tr>
				<td align="center"><div class="error"><?php echo $error?></div>
					<table border="0" width="300" id="table2">
						<tr>
							<td align="left" class="table_head" colspan="2"><b>Sign in</b></td>
						</tr>
						<tr>
							<td align="right" class="table_td1" width="42%"><b>Username:</b></td>
							<td width="56%" class="admin_table_td1"><input type="text"
								name="username" size="20"></td>
						</tr>
						<tr>
							<td align="right" class="table_td1" width="42%"><b>Password:</b></td>
							<td width="56%" class="admin_table_td1"><input type="password"
								name="password" size="20"></td>
						</tr>
						<tr>
							<td align="center" class="table_head" colspan="2"><input
								type="submit" value="Login" name="login"></td>
						</tr>
					</table>


				</td>
			</tr>
		</table>
	</form>

</body>

</html>

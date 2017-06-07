<?php
/* WP File Upload - original hack by shockingbird.com */

$standalone="1";
require_once("./admin-header.php");

if ($user_level == 0) //Checks to see if user has logged in
die ("Cheatin' uh ?");

if (!$use_fileupload) //Checks if file upload is enabled in the config
die ("The admin disabled this function");

$allowed_types = explode(" ", trim($fileupload_allowedtypes));

?><html>
<head>
<title>WordPress &raquo; Upload images/files</title>
<style type="text/css">
<!--
body {

	margin: 30px;
}
<?php
if (!$is_NS4) {
?>
textarea,input,select {
	background-color: white;
  border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}
<?php if (!$is_gecko) { ?>
.checkbox {
	border-width: 0px;
	border-color: transparent;
	border-style: solid;
	padding: 0px;
	margin: 0px;
}
.uploadform {
	background-color: white;
<?php if ($is_winIE) { ?>
	filter: alpha(opacity:100);
<?php } ?>
	border-width: 1px;
	border-color: #333333;
	border-style: solid;
	padding: 2px;
	margin: 1px;
	width: 265px;
	height: 24px;
}
<?php } ?>
<?php
}
?>
-->
</style>
<script type="text/javascript">
<!-- // idocs.com's popup tutorial rules !
function targetopener(blah, closeme, closeonly) {
	if (! (window.focus && window.opener))return true;
	window.opener.focus();
	if (! closeonly)window.opener.document.post.content.value += blah;
	if (closeme)window.close();
	return false;
}
//-->
</script>
</head>
<body>

<table align="center" width="100%" height="100%" cellpadding="15" cellspacing="0" border="1" style="border-width: 1px; border-color: #cccccc;">
	<tbody>
	<tr>
	<td valign="top" style="background-color: transparent; <?php if ($is_gecko || $is_macIE) { ?>background-image: url('../wp-images/bgbookmarklet3.gif');<?php } elseif ($is_winIE) { ?>background-color: #cccccc; filter: alpha(opacity:60);<?php } ?>;">
<?php

if (!$HTTP_POST_VARS["submit"]) {
	$i = implode(", ", $allowed_types);
?>
	<p><strong>File upload</strong></p>
	<p>You can upload files of type:<br /><em><?php echo $i ?></em></p>
	<p>The maximum size of the file should be:<br /><em><?php echo $fileupload_maxk ?> KB</em></p>
	<form action="upload.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $fileupload_maxk*1024 ?>" />
	<input type="file" name="img1" size="30" class="uploadform" />
	<br /><br />
	Description:<br />
	<input type="text" name="imgdesc" size="30" class="uploadform" />
	<br /><br />
	<input type="submit" name="submit" value="upload !" class="search" />
	</form>
	</td>
	</tr>
	</tbody>
</table>
</body>
</html><?php die();
}



?>



<?php  //Makes sure they choose a file

//print_r($HTTP_POST_FILES);
//die();

if (!empty($HTTP_POST_VARS)) { //$img1_name != "") {

	$imgalt = (isset($HTTP_POST_VARS['imgalt'])) ? $HTTP_POST_VARS['imgalt'] : $imgalt;

	$img1_name = (strlen($imgalt)) ? $HTTP_POST_VARS['imgalt'] : $HTTP_POST_FILES['img1']['name'];
	$img1_type = (strlen($imgalt)) ? $HTTP_POST_VARS['img1_type'] : $HTTP_POST_FILES['img1']['type'];
	$imgdesc = str_replace('"', '&amp;quot;', $HTTP_POST_VARS['imgdesc']);

	$imgtype = explode(".",$img1_name);
	$imgtype = $imgtype[count($imgtype)-1];

	if (in_array($imgtype, $allowed_types) == false) {
	    die("File $img1_name of type $imgtype is not allowed.");
	}

	if (strlen($imgalt)) {
		$pathtofile = $fileupload_realpath."/".$imgalt;
		$img1 = $HTTP_POST_VARS['img1'];
	} else {
		$pathtofile = $fileupload_realpath."/".$img1_name;
		$img1 = $HTTP_POST_FILES['img1']['tmp_name'];
	}

	// makes sure not to upload duplicates, rename duplicates
	$i = 1;
	$pathtofile2 = $pathtofile;
	$tmppathtofile = $pathtofile2;
	$img2_name = $img1_name;

	while (file_exists($pathtofile2)) {
	    $pos = strpos($tmppathtofile, '.'.trim($imgtype));
	    $pathtofile_start = substr($tmppathtofile, 0, $pos);
	    $pathtofile2 = $pathtofile_start.'_'.zeroise($i++, 2).'.'.trim($imgtype);
	    $img2_name = explode('/', $pathtofile2);
	    $img2_name = $img2_name[count($img2_name)-1];
	}

	if (file_exists($pathtofile) && !strlen($imgalt)) {
		$i = explode(" ",$fileupload_allowedtypes);
		$i = implode(", ",array_slice($i, 1, count($i)-2));
		$moved = move_uploaded_file($img1, $pathtofile2);
		// if move_uploaded_file() fails, try copy()
		if (!$moved) {
			$moved = copy($img1, $pathtofile2);
		}
		if (!$moved) {
			die("Couldn't Upload Your File to $pathtofile2.");
		} else {
			@unlink($img1);
		}
	
	// duplicate-renaming function contributed by Gary Lawrence Murphy
	?>
	<p><strong>Duplicate File?</strong></p>
	<p><b><em>The filename '<?php echo $img1_name; ?>' already exists!</em></b></p>
	<p> filename '<?php echo $img1; ?>' moved to '<?php echo "$pathtofile2 - $img2_name"; ?>'</p>
	<p>Confirm or rename:</p>
	<form action="upload.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $fileupload_maxk*1024 ?>" />
	<input type="hidden" name="img1_type" value="<?php echo $img1_type;?>" />
	<input type="hidden" name="img1_name" value="<?php echo $img2_name;?>" />
	<input type="hidden" name="img1_size" value="<?php echo $img1_size;?>" />
	<input type="hidden" name="img1" value="<?php echo $pathtofile2;?>" />
	Alternate name:<br /><input type="text" name="imgalt" size="30" class="uploadform" value="<?php echo $img2_name;?>" /><br />
	<br />
	Description:<br /><input type="text" name="imgdesc" size="30" class="uploadform" value="<?php echo $imgdesc;?>" />
	<br />
	<input type="submit" name="submit" value="confirm !" class="search" />
	</form>
	</td>
	</tr>
	</tbody>
</table>
</body>
</html><?php die();

	}

	if (!strlen($imgalt)) {
		@$moved = move_uploaded_file($img1, $pathtofile); //Path to your images directory, chmod the dir to 777
		// move_uploaded_file() can fail if open_basedir in PHP.INI doesn't
		// include your tmp directory. Try copy instead?
		if(!moved) {
			$moved = copy($img1, $pathtofile);
		}
		// Still couldn't get it. Give up.
		if (!moved) {
			die("Couldn't Upload Your File to $pathtofile.");
		} else {
			@unlink($img1);
		}
	} else {
		rename($img1, $pathtofile)
		or die("Couldn't Upload Your File to $pathtofile.");
	}

}


if ( ereg('image/',$img1_type)) {
	$piece_of_code = "&lt;img src=&quot;$fileupload_url/$img1_name&quot; alt=&quot;$imgdesc&quot; /&gt;"; 
} else {
	$piece_of_code = "&lt;a href=&quot;$fileupload_url/$img1_name&quot; title=&quot;$imgdesc&quot; /&gt;$imgdesc&lt;/a&gt;"; 
};

?>

<p><strong>File uploaded !</strong></p>
<p>Your file <b><?php echo "$img1_name"; ?></b> was uploaded successfully !</p>
<p>Here's the code to display it:</p>
<p><form>
<!--<textarea cols="25" rows="3" wrap="virtual"><?php echo "&lt;img src=&quot;$fileupload_url/$img1_name&quot; border=&quot;0&quot; alt=&quot;&quot; /&gt;"; ?></textarea>-->
<input type="text" name="imgpath" value="<?php echo $piece_of_code; ?>" size="38" style="padding: 5px; margin: 2px;" /><br />
<input type="button" name="close" value="Add the code to your post !" class="search" onClick="targetopener('<?php echo $piece_of_code; ?>')" style="margin: 2px;" />
</form>
</p>
<p><strong>Image Details</strong>: <br />
name: 
<?php echo "$img1_name"; ?>
<br />
size: 
<?php echo round($img1_size/1024,2); ?> KB
<br />
type: 
<?php echo "$img1_type"; ?>
</p>
<p align="right">
<form>
<input type="button" name="close" value="Close this window" class="search" onClick="window.close()" />
</form>
</p>
</td>
</tr>
</tbody>
</table>

</body>

</html>

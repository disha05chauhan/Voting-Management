<?php
function redirect($url=NULL)
{
	if(is_null($url)) $url=curPageURL();
	if(headers_sent())
	{
		echo "<script>window.location='".$url."'</script>";
	}
	else
	{
		header("Location:".$url);
	}
	exit;
}

function chkHeader()
{
	if(strpos($_SERVER['HTTP_REFERER'],URL_ROOT)==0) return true;
	return false;
}

function setMsgPage($mod, $sec, $type, $note)
{
	//possible values for type
	//success
	//information
	//warning
	//error
	if(!isset($_SESSION['msg_er'])) $_SESSION['msg_er']=array();
	if(!isset($_SESSION['msg_er'][$mod])) $_SESSION['msg_er'][$mod]=array();
	if(!isset($_SESSION['msg_er'][$mod][$sec])) $_SESSION['msg_er'][$mod][$sec]=array();
	
	$_SESSION['msg_er'][$mod][$sec]['page']=array(
												  'type'=>$type,
												  'note'=>$note
												  );
}

function getMsgPage($mod, $sec)
{
	$return='';
	if(isset($_SESSION['msg_er'][$mod][$sec]['page']) && is_array($_SESSION['msg_er'][$mod][$sec]['page']) && count($_SESSION['msg_er'][$mod][$sec]['page'])>0)
	{
		$class=$_SESSION['msg_er'][$mod][$sec]['page']['type'];
		$return="<div class=\"message ".$class."\">";
		$return.=$_SESSION['msg_er'][$mod][$sec]['page']['note'];
		$return.="</div>";
		
		unset($_SESSION['msg_er'][$mod][$sec]['page']);
	}
	
	clearErMsg($mod,$sec);
	
	return $return;
}

function setMsgField($mod, $sec, $field, $type, $note)
{
	//possible values for type
	//success
	//information
	//warning
	//error
	
	if(!isset($_SESSION['msg_er'])) $_SESSION['msg_er']=array();
	
	if(!isset($_SESSION['msg_er'][$mod])) $_SESSION['msg_er'][$mod]=array();
	if(!isset($_SESSION['msg_er'][$mod][$sec])) $_SESSION['msg_er'][$mod][$sec]=array();
	
	if(!isset($_SESSION['msg_er'][$mod][$sec]['field'])) $_SESSION['msg_er'][$mod][$sec]['field']=array();
	
	$_SESSION['msg_er'][$mod][$sec]['field'][$field]=array(
														   'type'=>$type,
														   'note'=>$note
														   );
}

function getMsgField($mod, $sec, $field)
{
	$return='';
	if(isset($_SESSION['msg_er'][$mod][$sec]['field'][$field]) && is_array($_SESSION['msg_er'][$mod][$sec]['field'][$field]) && count($_SESSION['msg_er'][$mod][$sec]['field'][$field])>0)
	{
		$class=$_SESSION['msg_er'][$mod][$sec]['field'][$field]['type'];
		$return="<span class=\"message ".$class."\">";
		$return.=$_SESSION['msg_er'][$mod][$sec]['field'][$field]['note'];
		$return.="</span>";
		unset($_SESSION['msg_er'][$mod][$sec]['field'][$field]);
	}
	if(isset($_SESSION['msg_er'][$mod][$sec]['field']) && is_array($_SESSION['msg_er'][$mod][$sec]['field']) && count($_SESSION['msg_er'][$mod][$sec]['field'])===0) unset($_SESSION['msg_er'][$mod][$sec]['field']);
	
	clearErMsg($mod,$sec);
	
	return $return;
}

function clearErMsg($mod,$sec)
{
	if(isset($_SESSION['msg_er'][$mod][$sec]) && is_array($_SESSION['msg_er'][$mod][$sec]) && count($_SESSION['msg_er'][$mod][$sec])===0) unset($_SESSION['msg_er'][$mod][$sec]);
	
	if(isset($_SESSION['msg_er'][$mod]) && is_array($_SESSION['msg_er'][$mod]) && count($_SESSION['msg_er'][$mod])===0) unset($_SESSION['msg_er'][$mod]);
	
	if(isset($_SESSION['msg_er']) && is_array($_SESSION['msg_er']) && count($_SESSION['msg_er'])===0) unset($_SESSION['msg_er']);
}

function setSort($mod,$sec,$val)
{
	if(!isset($_SESSION['sort'])) $_SESSION['sort']=array();
	if(!isset($_SESSION['sort'][$mod])) $_SESSION['sort'][$mod]=array();
	
	$_SESSION['sort'][$mod][$sec]=$val;
}

function getSort($mod,$sec)
{
	return $_SESSION['sort'][$mod][$sec];
}

function curPageURL() 
{
	$pageURL = 'http';
 	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 	$pageURL .= "://";
 	if ($_SERVER["SERVER_PORT"] != "80") 
	{
  		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 	} 
	else 
	{
  		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 	}
 	return $pageURL;
}
function urlStr($str)
{
	return str_replace(" ","-",strtolower($str));
}
function getQueryString($aryQueryStr)
{
	$aryMatch=array();
	foreach($aryQueryStr as $opt=>$val) { $aryMatch[]=$opt.'='.urlencode($val); }
	return '?'.implode('&',$aryMatch);
}

function selected($needle,$haystack)
{
	if(is_array($haystack) && in_array($needle,$haystack)) { return 'selected="selected"'; }
	elseif(!is_array($haystack) && $needle===$haystack) { return 'selected="selected"'; }
	else { return ''; }
}

function checked($needle,$haystack)
{
	if(is_array($haystack) && in_array($needle,$haystack)) { return 'checked="checked"'; }
	elseif(!is_array($haystack) && $needle===$haystack) { return 'checked="checked"'; }
	else { return ''; }
}

function isValidDate($val)
{
	if(preg_match(REGX_DATE,$val))
	{
		list($year,$month,$date)=explode("-",$val);
		if(checkdate($month,$date,$year)) return true;
	}
	return false;
}

function getPaging($refUrl,$aryOpts,$pgCnt,$curPg)
{
	$maxPage = 6;
	$downto = 1;
	if($pgCnt>=$maxPage)
	{
		$upto = $maxPage;
	}
	else
	{
		$upto = $pgCnt;
	}
	
	$return='';
	$return.='<ul>';
	if($curPg>1)
	{
		if($pgCnt == $curPg)
		{
			$upto = $curPg;
		}
		elseif($pgCnt>=$maxPage)
		{
			$upto = $curPg + 5;
			if($upto > $pgCnt)
			{
				$upto = $pgCnt;
			}
		}
		else
		{
			$upto = $pgCnt;
		}
		if($curPg >= 6)
		{
			$downto = $curPg - 5;
		}
		
		$aryOpts['pg']=1;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">First</a></li>';
		
		$aryOpts['pg']=$curPg-1;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">Prev</a></li>';
	}
	for($i=$downto;$i<=$upto;$i++)
	{
		$aryOpts['pg']=$i;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'"';
		if($curPg==$i) $return.='class="active"';
		$return.='" >'.$i.'</a></li>';
	}
	if($curPg<$pgCnt)
	{
		
		$aryOpts['pg']=$curPg+1;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">Next</a></li>';
		$aryOpts['pg']=$pgCnt;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">Last</a></li>';
		$aryOpts['pg']=1;
		//$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">First</a></li>';
	}

	$return.='<div class="clearfix"></div></ul>';
	return $return;
}

function isAdmin()
{
	if(isset($_SESSION[LOGIN_ADMIN]) && is_array($_SESSION[LOGIN_ADMIN]) && isset($_SESSION[LOGIN_ADMIN]['id'])) return true;
	return false;
}

function getFileSize($path)
{
	if(is_array($path) && count($path)>0)
	{
		//if(!file_exists($path)) return 0;
		//if(is_file($path)) return filesize($path);
		$ret = 0;
		foreach($path as $file)
			$ret+=getFileSize($file);
		return $ret;
	}
	else
	{
		if(!file_exists($path)) return 0;
		if(is_file($path)) return filesize($path);
	}
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
  
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
  
    $bytes /= pow(1024, $pow);
  
    return round($bytes, $precision) . ' ' . $units[$pow];
	//return $bytes;
}

function getRealIpAddr()
{
    if(!empty($_SERVER['HTTP_CLIENT_IP']))//check ip from share internet
    { 
		$ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))//to check ip is pass from proxy
    { 
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    { 
		$ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function fetchSetting($mixVal)
{
	$aryReturn=array();
	$strSetting='';
	if(is_array($mixVal) && count($mixVal)>0)
	{
		$strSetting="'".implode("', '",$mixVal)."'";
	}
	elseif(trim($mixVal)!='')
	{
		$strSetting="'".$mixVal."'";
	}
	if(trim($strSetting)!='')
	{
		global $db;
		$arySetData=$db->getRows("select * from settings where `field` in (".$strSetting.")");
		if(is_array($arySetData) && count($arySetData)>0)
		{
			foreach($arySetData as $iSetData)
			{
				$aryReturn[$iSetData['field']]=$iSetData['value'];
			}
		}
	}
	return $aryReturn;
}

function getStatusImg($status)
{
	$aryImg=array(
				  '0'=>"status_inactive.png",
				  '1'=>"status_active.png"
				  );
	return '<img src="'.URL_ADMIN_IMG.$aryImg[$status].'" title="'.getStatusStr($status).'" />';
}

function getOptionImg($status)
{
	$aryImg=array(
				  '0'=>"cross.png",
				  '1'=>"tick.png"
				  );
	return '<img src="'.URL_ADMIN_IMG."icons/".$aryImg[$status].'" />';
}

function getStatusStr($val)
{
	if($val==0)
	{
		return "Inactive";
	}
	else
	{
		return "Active";
	}
}
function getOptionStr($val)
{
	if($val==0)
	{
		return "No";
	}
	else
	{
		return "Yes";
	}
}

function delete_directory($dirname)
{
	if (is_dir($dirname))
      $dir_handle = opendir($dirname);
   if (!$dir_handle)
      return false;
   while($file = readdir($dir_handle))
   {
      if ($file != "." && $file != "..")
	  {
         if (!is_dir($dirname.DS.$file))
            @unlink($dirname.DS.$file);
         else
            delete_directory($dirname.DS.$file);    
      }
   }
   closedir($dir_handle);
   @rmdir($dirname);
   return true;
}

function check_login($userType='User')
{
	if($userType=='User' && (!isset($_SESSION[LOGIN_USER]) || count($_SESSION[LOGIN_USER])==0))
		return false;
	elseif($userType=='Admin' && (!isset($_SESSION[LOGIN_ADMIN]) || count($_SESSION[LOGIN_ADMIN])==0))
		return false;
	return true;
}

function change_date_format_mdy($date){
//       list($year,$month,$day) = explode("-",$date);
//       $newdate = $month."-".$day."-".$year;
//       return $newdate;
//		 return DATE_FORMAT(date($date));
		 return date(DATE_FORMAT,strtotime($date));
}

function change_date_format_ymd($date){
       list($month,$day,$year) = explode("-",$date);
       $newdate = $year."-".$month."-".$day;
       return $newdate;
}

function msg($msg)
{
	if(count($msg))
	foreach($msg as $type => $content)

	if($msg[$type]!='')
	{
	 return '<div class="status '.$type.'">
        	<p class="closestatus"><a href="javascript:void(0);" onclick="$(\'.status\').remove()" title="Close">x</a></p>
        	<p><img src="'.URL_ADMIN_IMG.'icon_'.$type.'.png" align="absmiddle" >&nbsp;&nbsp;'.$content.'</p>
           </div>';
	}
}

function checksession($uname,$loc)
{
	if(!isset($uname))
	{
		header("location:$loc");
		exit;
	}
}
function StatusImg($stat)
{
	if($stat==1)
		return '<img src="'.URL_ADMIN_IMG."status_active.png".'" />';
	else
		return '<img src="'.URL_ADMIN_IMG."status_active.png".'" />';
}
function upload($src,$dest,$fname)
{
$fx = 1;
	if(!empty($fname))
	{
		while($fx == 1)
		{
			if(file_exists($dest.$fname))
			{
			$newname = substr($fname,0,strpos($fname,'.'));
			$ext = substr($fname,strpos($fname,'.'),strlen($fname));
			$fname =$newname."_".rand(0,99999).$ext; 
			}
			else
			{
			$fx=0;
			@move_uploaded_file($src,$dest.$fname);
			}			
		}
		return $fname;
	}
	else
	{
		return ""; 
	}	

}
function smart_resize_image( $file, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false )
  {
    if ( $height <= 0 && $width <= 0 ) {
      return false;
    }
	
//	echo " file : ".$file;
//	echo "<br />";
//	echo " width : ".$width;
//	echo "<br />";
//	echo " height : ".$height;
//	echo "<br />";
	
    $info = getimagesize($file);
//	echo " info : ".$info;
//	echo "<br />";
//	echo "<pre>";
//	print_r($info);
//	echo "</pre>";
//	exit;
    $image = '';

    $final_width = 0;
    $final_height = 0;
    list($width_old, $height_old) = $info;

    if ($proportional) {
      if ($width == 0) $factor = $height/$height_old;
      elseif ($height == 0) $factor = $width/$width_old;
      else $factor = min ( $width / $width_old, $height / $height_old);  

      $final_width = round ($width_old * $factor);
      $final_height = round ($height_old * $factor);

    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
    }

    switch ( $info[2] ) {
      case IMAGETYPE_GIF:
        $image = imagecreatefromgif($file);
      break;
      case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($file);
      break;
      case IMAGETYPE_PNG:
        $image = imagecreatefrompng($file);
      break;
      default:
        return false;
    }
   
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
       
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $trnprt_indx = imagecolortransparent($image);
   
      // If we have a specific transparent color
      if ($trnprt_indx >= 0) {
   
        // Get the original image's transparent color's RGB values
        $trnprt_color    = imagecolorsforindex($image, $trnprt_indx);
   
        // Allocate the same color in the new image resource
        $trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
   
        // Completely fill the background of the new image with allocated color.
        imagefill($image_resized, 0, 0, $trnprt_indx);
   
        // Set the background color for new image to transparent
        imagecolortransparent($image_resized, $trnprt_indx);
   
     
      }
      // Always make a transparent background color for PNGs that don't have one allocated already
      elseif ($info[2] == IMAGETYPE_PNG) {
   
        // Turn off transparency blending (temporarily)
        imagealphablending($image_resized, false);
   
        // Create a new transparent color for image
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
   
        // Completely fill the background of the new image with allocated color.
        imagefill($image_resized, 0, 0, $color);
   
        // Restore transparency blending
        imagesavealpha($image_resized, true);
      }
    }

    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
 
    if ( $delete_original ) {
      if ( $use_linux_commands )
        exec('rm '.$file);
      else
        @unlink($file);
    }
   
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }

    switch ( $info[2] ) {
      case IMAGETYPE_GIF:
        imagegif($image_resized, $output);
      break;
      case IMAGETYPE_JPEG:
        imagejpeg($image_resized, $output);
      break;
      case IMAGETYPE_PNG:
        imagepng($image_resized, $output);
      break;
      default:
        return false;
    }

    return true;
  }
function mail_template($to,$subject,$body,$email,$password)
{
	$resever = $to;
	$sub=$subject;
	$mainbody = $body;
	$sender = $email;
	$senderpwd = $password;
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->Host 	= 'mail.studentzhub.com';
	$mail->SMTPDebug= 1;
	$mail->SMTPAuth = true;
	$mail->Port 	= '25';
	$mail->Username	= $sender;
	$mail->Password = $senderpwd;
	$mail->SetFrom($sender,'Studentz Hub');
	$mail->Subject    = $sub;
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
	
	$mail->MsgHTML($mainbody);
	$mail->AddAddress($resever, '');
	$mail->Send();   	
	$mail->ClearAddresses(); 
}
function super_unique($array)
{
  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

  foreach ($result as $key => $value)
  {
    if ( is_array($value) )
    {
      $result[$key] = super_unique($value);
    }
  }

  return $result;
}


function substring($string,$reqLength)
{
	$newString = '';
	$length = strlen($string);
	if($reqLength < $length)
	{
		$newString .= substr($string,0,$reqLength);
		$newString .= "...";
	}
	else
	{
		$newString = $string;
	}
	return $newString;
}

function getCheckedImg($status)
{
	$aryImg=array(
				  '0'=>"cross.png",
				  '2'=>"status_reject.png",
				  '1'=>"checked.png"
				  );
	return '<img src="'.URL_ADMIN_IMG.$aryImg[$status].'" title="'.getStatusStr($status).'" />';
}

function daysDifference($startDate,$endDate)
{
	$diff = strtotime($endDate)-strtotime($startDate);
	$sec = 3600 * 24;
	$temp = $diff/$sec;
	$days = ceil($temp);
	if($days > 0)
	{
		return $days." Days";
	}
	else
	{
		$days = 'Time Line Over';
		return $days;
	}
}

function resizeBySize($file,$width,$height,$path,$proportional)
{
	$imgData = getimagesize($path.$file);
	
	$imgReturn = '';
	if($imgData[0] > $width && $imgData[1] > $height)
	{
		$imgReturn = smart_resize_image($path.$file.'',$width,$height,$proportional);
	}
	elseif($imgData[0] > $width && $imgData[1] < $height)
	{
		$imgReturn = smart_resize_image($path.$file.'',$width,$imgData[1],$proportional);
	}
	elseif($imgData[0] < $width && $imgData[1] > $height)
	{
		$imgReturn = smart_resize_image($path.$file.'',$imgData[0],$height,$proportional);
	}
	return $imgReturn;
}
function watermark_image($oldimage_name, $new_image_name){
    global $image_path;
    list($owidth,$oheight) = getimagesize($oldimage_name);
    $width = 850;
	$height = 520;    
    $im = imagecreatetruecolor($width, $height);
    $img_src = imagecreatefromjpeg($oldimage_name);
    imagecopyresampled($im, $img_src, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
    $watermark = imagecreatefrompng($image_path);
    list($w_width, $w_height) = getimagesize($image_path);        
    $pos_x = $width - $w_width; 
    $pos_y = $height - $w_height;
    imagecopy($im, $watermark, $pos_x, $pos_y, 0, 0, $w_width, $w_height);
    imagejpeg($im, $new_image_name, 100);
    imagedestroy($im);
    unlink($oldimage_name);
    return true;
}

function watermark_text($oldimage_name, $new_image_name){
    global $font_path, $font_size, $water_mark_text_1, $water_mark_text_2;
    list($owidth,$oheight) = getimagesize($oldimage_name);
    $width = $height = 300;    
    $image = imagecreatetruecolor($width, $height);
    $image_src = imagecreatefromjpeg($oldimage_name);
    imagecopyresampled($image, $image_src, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
   // $black = imagecolorallocate($image, 0, 0, 0);
    $blue = imagecolorallocate($image, 79, 166, 185);
   // imagettftext($image, $font_size, 0, 30, 190, $black, $font_path, $water_mark_text_1);
    imagettftext($image, $font_size, 0, 68, 190, $blue, $font_path, $water_mark_text_2);
    imagejpeg($image, $new_image_name, 100);
    imagedestroy($image);
    unlink($oldimage_name);
    return true;
}
function getPagingFront($refUrl,$aryOpts,$pgCnt,$curPg)
{
	$maxPage = 6;
	$downto = 1;
	if($pgCnt>=$maxPage)
	{
		$upto = $maxPage;
	}
	else
	{
		$upto = $pgCnt;
	}
	
	$return='';
	$return.='<ul>';
	if($curPg>1)
	{
		if($pgCnt == $curPg)
		{
			$upto = $curPg;
		}
		elseif($pgCnt>=$maxPage)
		{
			$upto = $curPg + 5;
			if($upto > $pgCnt)
			{
				$upto = $pgCnt;
			}
		}
		else
		{
			$upto = $pgCnt;
		}
		if($curPg >= 6)
		{
			$downto = $curPg - 5;
		}
		
		$aryOpts['pg']=1;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">First</a></li>';
		
		$aryOpts['pg']=$curPg-1;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">Prev</a></li>';
	}
	for($i=$downto;$i<=$upto;$i++)
	{
		$aryOpts['pg']=$i;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'"';
		if($curPg==$i) $return.='class="active"';
		$return.='" >'.$i.'</a></li>';
	}
	if($curPg<$pgCnt)
	{
		
		$aryOpts['pg']=$curPg+1;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">Next</a></li>';
		$aryOpts['pg']=$pgCnt;
		$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">Last</a></li>';
		$aryOpts['pg']=1;
		//$return.='<li><a href="'.$refUrl.getQueryString($aryOpts).'">First</a></li>';
	}

	$return.='<div class="clearfix"></div></ul>';
	return $return;
}
function href($page,$param="")
{
	$url = explode(".",$page);
	$url = $url[0];
	if($param!='')
	{
		$linkParam = end(explode("=",$param));
		return 	URL_ROOT.$url."/".$linkParam."/";
	}
	else
	{
		return 	URL_ROOT.$url."/";
	}
}
?>
<?php

	set_time_limit (0);
	//关闭缓存
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
  session_start();
  $_SESSION['userinfo']['id']='123456';
	include_once('./uploadClass.php');

	$ip_path = './uploads/'.$_SESSION['userinfo']['id'];
	$save_path = './uploads/'.$_SESSION['userinfo']['id'];
	
	$uploader =  new Vupload;
	$uploader->set('path',$ip_path);
	//用于断点续传，验证指定分块是否已经存在，避免重复上传
	if(isset($_POST['status'])){
		if($_POST['status'] == 'chunkCheck'){
			$target =  $ip_path.'/'.$_POST['name'].'/'.$_POST['chunkIndex'];
			if(file_exists($target) && filesize($target) == $_POST['size']){
				die('{"ifExist":1}');
			}
			die('{"ifExist":0}');

		}elseif($_POST['status'] == 'md5Check'){

			//todo 模拟持久层查询
			$dataArr = array(
				'b0201e4d41b2eeefc7d3d355a44c6f5a' => 'kazaff2.jpg'
			);

			if(isset($dataArr[$_POST['md5']])){
				die('{"ifExist":1, "path":"'.$dataArr[$_POST['md5']].'"}');
			}
			die('{"ifExist":0}');
		}elseif($_POST['status'] == 'chunksMerge'){

			if($path = $uploader->chunksMerge($_POST['name'], $_POST['chunks'], $_POST['ext'])){
				//todo 把md5签名存入持久层，供未来的秒传验证
				session('video_path', $save_path.'/'.$path);
				die('{"status":1, "path": "'.$save_path.'/'.$path.'"}');
			}
			die('{"status":0}');
		}
	}

	if(($path = $uploader->upload('file', $_POST)) !== false){
		/* if(!session('video_path')){
			session('video_path', $save_path.'/'.$path);
		} */
		die('{"status":1, "path": "'.$save_path.'/'.$path.'"}');
	}
	die('{"status":0}');

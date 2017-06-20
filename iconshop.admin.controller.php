<?php

/**
 * @class  iconshopAdminController
 * @author 러키군 (admin@barch.kr)
 * @brief  iconshop 모듈의 admin controller class
 **/
class iconshopAdminController extends iconshop
{

	/**
	 * @brief 초기화
	 **/
	function init()
	{
	}

	/**
	 * @brief 기본수정
	 **/
	function procIconshopAdminModuleInsert()
	{
		$oModuleController = getController('module');

		// request 값을 모두 받음
		$args = Context::getRequestVars();
		$args->mid = 'iconshop';
		$args->module = 'iconshop';
		if(!$args->module_srl)
		{
			return new Object(-1, 'invalid_module');
		}

		// module_srl의 값에 따라 insert/update
		if($args->module_srl)
		{
			$output = $oModuleController->updateModule($args);
		}
		else
		{
			$output = $oModuleController->insertModule($args);
		}
		// 오류가 있으면 리턴
		if(!$output->toBool())
		{
			return $output;
		}

		$this->setMessage('success_updated');

		if(Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispIconshopAdminConfig'));
		}
	}

	/**
	 * @brief 추가설정
	 **/
	function procIconshopAdminConfig()
	{
		// 설정 정보 가져오기
		$iconshop_config = self::getConfig();

		// 변수 정리
		$args = Context::getRequestVars();
		$list_count = $args->list_count;
		if(!$list_count)
		{
			$list_count = 10;
		}
		$cols_list_count = $args->cols_list_count;
		if(!$cols_list_count)
		{
			$cols_list_count = 1;
		}

		// 기본 포인트 지정
		if(!$iconshop_config)
		{
			$iconshop_config = new stdClass();
		}
		$iconshop_config->list_count = $list_count;
		$iconshop_config->cols_list_count = $cols_list_count;
		$iconshop_config->member_info_print = $args->member_info_print;
		$iconshop_config->icon_width = $args->icon_width;
		$iconshop_config->icon_height = $args->icon_height;
		$iconshop_config->send_fee = $args->send_fee;
		$iconshop_config->sell_per = $args->sell_per;
		$iconshop_config->new_hour = $args->new_hour;
		$iconshop_config->member_max_count = $args->member_max_count;
		$iconshop_config->log_save_day = $args->log_save_day;
		$iconshop_config->sender_srl = $args->sender_srl;
		$iconshop_config->item_delete_event = $args->item_delete_event;
		$iconshop_config->item_delete_title = $args->item_delete_title;
		$iconshop_config->item_delete_message = $args->item_delete_message;


		// 저장
		$oModuleController = getController('module');
		$output = $oModuleController->insertModuleConfig('iconshop', $iconshop_config);
		if(!$output->toBool())
		{
			return $output;
		}

		$this->setMessage('success_updated');

		if(Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispIconshopAdminAddConfig'));
		}
	}

	/**
	 * @brief 상품 추가/수정
	 **/
	function procIconshopAdminIconInsert()
	{
		// 변수 정리
		$args = Context::getRequestVars();
		$obj = new stdClass();
		if(!$args->title)
		{
			return new Object(-1, 'null_title');
		}

		// icon_srl이 있으면 원본 데이터 가져오기
		$icon_data = null;
		if($args->icon_srl)
		{
			$oIconshopModel = getModel('iconshop');
			$icon_data = $oIconshopModel->getIconBySrl($args->icon_srl);
			if(!$icon_data)
			{
				return $this->ErrorMessage('invalid_icon');
			}
			$obj->file1 = $icon_data->file1;

			// 원본데이터의 file1이 없고, 업로드한 파일도 없으면..
			if((!$icon_data->file1) && (!$args->file1['tmp_name'] || !is_uploaded_file($args->file1['tmp_name'])))
			{
				return $this->ErrorMessage('null_file1');
			}
		}

		if($icon_data === null && (!$args->file1['tmp_name'] || !is_uploaded_file($args->file1['tmp_name'])))
		{
			return $this->ErrorMessage('null_file1');
		}


		// 레벨값이 최대레벨보다 높을경우..
		$oModuleModel = getModel('module');
		$point_config = $oModuleModel->getModuleConfig('point');
		$level_limit = (int)$args->level_limit;
		if($level_limit > $point_config->max_level)
		{
			$level_limit = $point_config->max_level;
		}

		// 선택한 그룹중 존재하지 않는그룹이 있을경우...
		$oMemberModel = getModel('member');
		$member_group_list = $oMemberModel->getGroups(0);
		$group_limit = array();
		$group_limit_list = $args->group_limit;
		if(is_array($group_limit_list))
		{
			foreach($group_limit_list as $key => $val)
			{
				if($member_group_list[$val])
				{
					$group_limit[] = $val;
				}
			}
		}

		// 데이터 세팅
		$obj->icon_srl = $icon_data->icon_srl;
		$obj->title = $args->title;
		$obj->total_count = (int)$args->total_count;
		$obj->price = (int)$args->price;
		$obj->buy_limit = ($args->buy_limit == "Y") ? "Y" : "N";
		$obj->send_limit = ($args->send_limit == "Y") ? "Y" : "N";
		$obj->sell_limit = ($args->sell_limit == "Y") ? "Y" : "N";
		$obj->point_limit = ($args->point_limit == "Y") ? "Y" : "N";
		$obj->minute_limit = ($args->minute_limit == "Y") ? "Y" : "N";
		$obj->event_limit = ($args->event_limit == "Y") ? "Y" : "N";
		$obj->minute = (int)$args->minute;
		$obj->event_start = (int)$args->event_start;
		$obj->event_end = (int)$args->event_end;
		$obj->level_limit = (int)$level_limit;
		$obj->group_limit = ($group_limit) ? implode(",", $group_limit) : "";

		// 첨부파일이 있을때의 처리
		$file1_obj = $args->file1;
		$file1_del = $args->file1_del;
		if(($file1_obj['tmp_name']) && ((!$icon_data->file1) || ($icon_data->file1 && $file1_del == "Y")))
		{
			// 이미지 파일이 아니면 무시
			if(!preg_match("/\.(jpg|jpeg|gif|png)$/i", $file1_obj['name'], $file1_ext))
			{
				return $this->ErrorMessage('invalid_image_format');
			}

			// 경로를 정해서 업로드
			$path = './files/iconshop/';

			// 디렉토리 생성
			if(!is_dir($path))
			{
				FileHandler::makeDir($path);
			}

			// 이미지 길이제한 구해옴
			$iconshop_config = $oModuleModel->getModuleConfig('iconshop');
			$max_width = $iconshop_config->icon_width;
			$max_height = $iconshop_config->icon_height;

			// 파일 정보 구함
			list($width, $height, $type, $attrs) = @getimagesize($file1_obj['tmp_name']);

			// 길이제한이 있을경우 리사이징
			if(($max_width && $max_height) && ($width > $max_width || $height > $max_height))
			{
				$filename = sprintf("%s%s.gif", $path, md5($file1_obj['name']));
				FileHandler::createImageFile($file1_obj['tmp_name'], $filename, $max_width, $max_height, 'gif');
			}
			else
			{
				$filename = sprintf("%s%s.%s", $path, md5($file1_obj['name']), $file1_ext[1]);
				//TODO(bJRAmbo) : check again
				@move_uploaded_file($file1_obj['tmp_name'], $filename);
			}
			$obj->file1 = $filename;

			// 변경에 체크했을경우 기존파일 삭제
			if($file1_del == 'Y')
			{
				FileHandler::removeFile($icon_data->file1);
			}
		}

		// icon_data가 있으면 Update, 없으면 Insert
		$oIconshopController = getController('iconshop');
		if($obj->icon_srl)
		{
			$oIconshopController->updateIcon($obj);
		}
		else
		{
			$oIconshopController->insertIcon($obj);
		}

		$this->setMessage('success_saved');

		if(Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispIconshopAdminIconList'));
		}
	}

	/**
	 * @brief 상품삭제
	 **/
	function procIconshopAdminIconDelete()
	{
		$oIconshopModel = getModel('iconshop');
		$oIconshopController = getController('iconshop');

		$icon_srls = Context::get('icon_srls');
		if(!$icon_srls)
		{
			return new Object(-1, 'msg_cart_is_null');
		}

		$icon_srl_list = explode(",", $icon_srls);
		foreach($icon_srl_list as $key => $val)
		{
			// 원본데이터 가져오기
			$icon_data = $oIconshopModel->getIconBySrl($val);
			if(!$icon_data)
			{
				continue;
			}

			// 데이터 삭제
			$oIconshopController->deleteIcon($icon_data->icon_srl);

			// 첨부파일 삭제
			FileHandler::removeFile($icon_data->file1);

			// 회원이 보유한 아이콘 삭제
			$args = new stdClass();
			$args->icon_srl = $icon_data->icon_srl;
			$oIconshopController->deleteMemberIcon($args);
			// 로그삭제
			$oIconshopController->deleteLog($args);
		}
		$this->setMessage('success_deleted');
	}

	/**
	 * @brief 회원의 아이콘삭제
	 **/
	function procIconshopAdminMemberIconDelete()
	{
		$oIconshopModel = getModel('iconshop');
		$oIconshopController = getController('iconshop');

		$data_srls = Context::get('data_srls');
		if(!$data_srls)
		{
			return new Object(-1, 'msg_cart_is_null');
		}

		$data_srl_list = explode(",", $data_srls);
		foreach($data_srl_list as $key => $val)
		{
			// 원본데이터 가져오기
			$icon_data = $oIconshopModel->getMemberIconByDataSrl($val);
			if(!$icon_data)
			{
				continue;
			}

			// 데이터 삭제
			$args = new stdClass();
			$args->data_srl = $icon_data->data_srl;
			$oIconshopController->deleteMemberIcon($args);
		}
		$this->setMessage('success_deleted');
	}

	/**
	 * @brief 로그삭제
	 **/
	function procIconshopAdminLogDelete()
	{
		$data_srls = Context::get('data_srls');
		if(!$data_srls)
		{
			return new Object(-1, 'msg_cart_is_null');
		}

		$data_srl_list = explode(",", $data_srls);
		$oIconshopController = getController('iconshop');
		foreach($data_srl_list as $key => $val)
		{
			// 데이터 삭제
			$args = new stdClass();
			$args->data_srl = $val;
			$oIconshopController->deleteLog($args);
		}
		$this->setMessage('success_deleted');
	}

	/**
	 * @brief 로그초기화
	 **/
	function procIconshopAdminLogReset()
	{
		$oIconshopController = getController('iconshop');
		$oIconshopController->resetLog();
		$this->setMessage('success_deleted');
	}

	/**
	 * TODO: Check again
	 * @에러처리를 따로 하기위한 함수 (insert/update시 사용)
	 * reload - 1일경우 메세지 출력후 새로고침
	 **/
	function ErrorMessage($msg, $reload = 0)
	{
		$message = (Context::getLang($msg)) ? Context::getLang($msg) : $msg;
		Context::set('message', $message);
		Context::set('reload', $reload);
		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile("top_refresh.html");
	}
}

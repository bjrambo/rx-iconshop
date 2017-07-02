<?php

/**
 * @class  iconshopController
 * @author 러키군 (admin@barch.kr)
 * @brief  iconshop 모듈의 Controller class
 **/
class iconshopController extends iconshop
{

	/**
	 * @brief 초기화
	 **/
	function init()
	{
	}

	/**
	 * @brief 아이콘 구입처리
	 **/
	function procIconshopIconBuy()
	{
		$oPointModel = getModel('point');
		$oModuleModel = getModel('module');
		$oIconshopModel = getModel('iconshop');

		// 권한 체크
		$logged_info = Context::get('logged_info');
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'msg_not_permitted');
		}

		$obj = Context::getRequestVars();
		foreach($obj as $key => $val)
		{
			$matchBool = preg_match('/_([0-9]+)/u', $key);
			if($matchBool)
			{
				$objName = preg_replace('/_([0-9]+)/u', '', $key);
				$obj->{$objName} = $val;
				unset($obj->{$key});
			}
		}

		if(!$obj->icon_srl)
		{
			return new Object(-1, 'invalid_icon');
		}
		if($obj->is_selected != 'Y')
		{
			$obj->is_selected = 'N';
		}

		// 포인트/레벨을 구해옴
		$point_config = $oModuleModel->getModuleConfig('point');
		$logged_info->point = $oPointModel->getPoint($logged_info->member_srl);
		$logged_info->point_level = $oPointModel->getLevel($logged_info->point, $point_config->level_step);

		// 원본데이터 가져오기
		$icon_data = $oIconshopModel->getIconBySrl($obj->icon_srl);
		if(!$icon_data)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 이미 보유하고 있는지 검사..
		$member_icon_data = $oIconshopModel->getMemberIconByIconSrl($icon_data->icon_srl, $logged_info->member_srl);
		if($member_icon_data->data_srl)
		{
			return new Object(-1, 'already_icon');
		}

		// 조건:그룹 검사
		if($icon_data->group_limit && !$oIconshopModel->groupCheck($logged_info->group_list, $icon_data->group_limit_list))
		{
			return new Object(-1, 'group_limit_error');
		}

		// 조건:갯수 검사
		if(!$icon_data->total_count)
		{
			return new Object(-1, 'count_limit_error');
		}
		// 조건:포인트 검사
		if($icon_data->price > $logged_info->point)
		{
			return new Object(-1, 'point_limit_error');
		}
		// 조건:최대 보유갯수 검사
		$iconshop_config = self::getConfig();
		$logged_info->icon_count = $oIconshopModel->getMemberIconCount($logged_info->member_srl);
		if(($iconshop_config->member_max_count) && ($logged_info->icon_count >= $iconshop_config->member_max_count))
		{
			return new Object(-1, 'max_count_error');
		}

		$data_output = $this->insertIcondata($logged_info, $obj, $icon_data);
		if(!$data_output->toBool())
		{
			return $data_output;
		}

		// 회원의 포인트 차감
		if($icon_data->price && $icon_data->point_limit == "Y")
		{
			$oPointController = getController('point');
			$oPointController->setPoint($logged_info->member_srl, $icon_data->price, 'minus');
		}

		// 상품의 갯수 -1
		if($icon_data->total_count != -1)
		{
			$icon_data->total_count = $icon_data->total_count - 1;
			$this->updateIcon($icon_data);
		}
		// 대표아이콘 설정시 딴 아이콘의 is_selected 변경
		if($obj->is_selected == "Y")
		{
			$this->updateIsSelected($logged_info->member_srl, $icon_data->icon_srl);
		}

		// 구입로그 남기기
		$args = new stdClass();
		$args->data_srl = $data_output->data_srl;
		$args->icon_srl = $icon_data->icon_srl;
		$args->category_srl = 1;
		$args->sender_srl = 0;
		$args->receive_srl = $logged_info->member_srl;
		$args->point = $icon_data->price;
		$args->content = sprintf("%s(%s) [%s] 구입", $logged_info->user_id, $logged_info->nick_name, $icon_data->title);
		$this->insertLog($args);

		// 성공 메세지 등록
		$this->setMessage("success_buy");

		if(Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'mid', 'iconshop', 'act', 'dispIconshopMyIcon'));
		}

	}

	/**
	 * @brief 아이콘 선물처리
	 **/
	function procIconshopIconSend()
	{
		$oPointModel = getModel('point');
		$oMemberModel = getModel('member');
		$oModuleModel = getModel('module');
		$oIconshopModel = getModel('iconshop');

		// 권한 체크
		$logged_info = Context::get('logged_info');
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'msg_not_permitted');
		}

		// 넘어온 변수 체크
		$obj = Context::getRequestVars();

		if(!$obj->icon_srl)
		{
			return new Object(-1, 'invalid_icon');
		}
		if(!$obj->receive_srl)
		{
			return new Object(-1, 'invalid_receive');
		}
		if($obj->send_message != "Y")
		{
			$obj->send_message = "N";
		}

		// 원본데이터 가져오기
		$icon_data = $oIconshopModel->getIconBySrl($obj->icon_srl);
		if(!$icon_data)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 보낸이의 포인트/레벨을 구해옴
		$point_config = $oModuleModel->getModuleConfig('point');
		$logged_info->point = $oPointModel->getPoint($logged_info->member_srl);
		$logged_info->point_level = $oPointModel->getLevel($logged_info->point, $point_config->level_step);

		// 받는이의 정보 구해옴
		$receive_info = $oMemberModel->getMemberInfoByMemberSrl($obj->receive_srl);
		if(!$receive_info->member_srl)
		{
			return new Object(-1, 'invalid_receive');
		}
		if($receive_info->member_srl == $logged_info->member_srl)
		{
			return new Object(-1, 'send_target_error');
		}
		$receive_info->point = $oPointModel->getPoint($receive_info->member_srl);
		$receive_info->point_level = $oPointModel->getLevel($receive_info->point, $point_config->level_step);

		// 조건:수량 검사
		if(!$icon_data->total_count)
		{
			return new Object(-1, 'count_limit_error');
		}

		// 이미 보유하고 있는지 검사.. (받는이)
		$member_icon_data = $oIconshopModel->getMemberIconByIconSrl($icon_data->icon_srl, $receive_info->member_srl);
		if($member_icon_data->data_srl)
		{
			return new Object(-1, 'already_icon');
		}

		// 조건:최대 보유갯수 검사 (받는이)
		$iconshop_config = self::getConfig();
		$receive_info->icon_count = $oIconshopModel->getMemberIconCount($receive_info->member_srl);
		if(($iconshop_config->member_max_count) && ($receive_info->icon_count >= $iconshop_config->member_max_count))
		{
			return new Object(-1, 'max_count_error');
		}

		// 조건:그룹 검사 (보낸이/받는이)
		if($icon_data->group_limit)
		{
			if(!$oIconshopModel->groupCheck($logged_info->group_list, $icon_data->group_limit_list))
			{
				return new Object(-1, 'group_limit_error');
			}
			if(!$oIconshopModel->groupCheck($receive_info->group_list, $icon_data->group_limit_list))
			{
				return new Object(-1, 'group_limit_error');
			}
		}

		// 조건:포인트 검사 (보낸이)
		if($icon_data->price > $logged_info->point)
		{
			return new Object(-1, 'point_limit_error');
		}

		$data_output = $this->insertIcondata($receive_info, $obj, $icon_data);
		if(!$data_output->toBool())
		{
			return $data_output;
		}

		// 모듈설정 가져옴
		$point = $icon_data->price;
		$send_fee = (int)$iconshop_config->send_fee;
		if($point && $send_fee)
		{
			$point += floor($point * $send_fee / 100);
		}

		// 회원의 포인트 차감
		if($icon_data->price && $icon_data->point_limit == "Y")
		{
			$oPointController = getController('point');
			$oPointController->setPoint($logged_info->member_srl, $point, 'minus');
		}

		// 상품의 갯수 -1
		if($icon_data->total_count != -1)
		{
			$icon_data->total_count = $icon_data->total_count - 1;
			$this->updateIcon($icon_data);
		}

		// 쪽지발송...
		if($obj->send_message == "Y" && $obj->content)
		{
			$title = sprintf(Context::getLang('send_message_title'), $logged_info->nick_name, $icon_data->title);
			$content = nl2br(trim(removeHackTag($obj->content)));
			$oCommunicationController = getController('communication');
			$oCommunicationController->sendMessage($logged_info->member_srl, $receive_info->member_srl, $title, $content);
		}

		// 선물로그 남기기
		$args = new stdClass();
		$args->data_srl = $data_output->data_srl;
		$args->icon_srl = $icon_data->icon_srl;
		$args->category_srl = 2;
		$args->sender_srl = $logged_info->member_srl;
		$args->receive_srl = $receive_info->member_srl;
		$args->point = $point;
		$args->content = sprintf("%s(%s) → %s(%s) [%s] 선물", $logged_info->user_id, $logged_info->nick_name, $receive_info->user_id, $receive_info->nick_name, $icon_data->title);
		$this->insertLog($args);

		// 성공 메세지 등록
		$this->setMessage("success_send");

		if(Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'mid', 'iconshop'));
		}
	}

	function insertIcondata($member_info, $obj, &$icon_data)
	{
		$iconshop_config = self::getConfig();
		$args = new stdClass();
		$args->icon_srl = $icon_data->icon_srl;
		$args->member_srl = $member_info->member_srl;
		$args->user_id = $member_info->user_id;
		$args->nick_name = $member_info->nick_name;
		$args->is_selected = $obj->is_selected;

		if($iconshop_config->day_price_use === 'Y')
		{
			$price = getModel('iconshop')->getDayPriceByKey($obj->day_price_key);
			$icon_data->price = $icon_data->price + $price;

			if($obj->day_price_key)
			{
				$args->day_limit = 'Y';
				$args->end_date = date("YmdHis", strtotime("+$args->day_price_key days", strtotime("now")));
			}
			else
			{
				$args->day_limit = 'N';
				$args->end_date = 0;
			}
		}
		$data_output = $this->insertMemberIcon($args);
		return $data_output;
	}

	/**
	 * @brief 대표아이콘 설정
	 **/
	function procIconshopIconSelected()
	{
		$oIconshopModel = getModel('iconshop');

		// 권한 체크
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'msg_not_permitted');
		}

		$logged_info = Context::get('logged_info');

		// 넘어온 변수 체크
		$params = Context::gets('data_srl');
		if(!$params->data_srl)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 보유하고 있는지 검사..
		$member_icon_data = $oIconshopModel->getMemberIconByDataSrl($params->data_srl);
		if(!$member_icon_data->data_srl || $member_icon_data->member_srl != $logged_info->member_srl)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 이미 대표아이콘이면..
		if($member_icon_data->is_selected == 'Y')
		{
			return new Object(-1, 'already_isselected');
		}

		// 회원DB에서 변경
		$member_icon_data->is_selected = 'Y';
		$this->updateMemberIcon($member_icon_data);

		// 딴 아이콘의 is_selected 변경
		$this->updateIsSelected($logged_info->member_srl, $member_icon_data->icon_srl);

		// 성공 메세지 등록
		$this->setMessage('success_isselected');
	}

	/**
	 * @brief 아이콘 되팔기처리
	 **/
	function procIconshopIconSell()
	{
		$oIconshopModel = getModel('iconshop');

		// 권한 체크
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'msg_not_permitted');
		}
		$logged_info = Context::get('logged_info');

		// 넘어온 변수 체크
		$params = Context::gets('data_srl');
		if(!$params->data_srl)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 보유하고 있는지 검사..
		$member_icon_data = $oIconshopModel->getMemberIconByDataSrl($params->data_srl);
		if(!$member_icon_data->data_srl || $member_icon_data->member_srl != $logged_info->member_srl)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 상품정보 구해옴
		$icon_data = $oIconshopModel->getIconBySrl($member_icon_data->icon_srl);

		// 조건: 판매가능여부
		if($icon_data->sell_limit != 'Y')
		{
			return new Object(-1, 'sell_limit_error');
		}

		// 포인트차감이 Y일경우 포인트 +
		if($icon_data->point_limit == 'Y')
		{
			// 이 상품에 대한 로그정보 구해옴
			$log_data = $oIconshopModel->getLogByDataSrl($member_icon_data->data_srl);
			$point = (int)$log_data->point; // 구입시 소모한 포인트
			if(!$log_data->data_srl)
			{
				$point = (int)$icon_data->price;
			} // 로그기록이 없으면 현재상품의 가격으로 대체...

			// 모듈설정 가져옴
			$iconshop_config = self::getConfig();
			$sell_per = (int)$iconshop_config->sell_per;

			// 돌려줄 포인트가 있으면 point+
			if($point && $sell_per)
			{
				$point = floor($point * $sell_per / 100);
				$oPointController = getController('point');
				$oPointController->setPoint($logged_info->member_srl, $point, 'add');
			}
		}
		else
		{
			$point = 0;
		}

		// 상품 회원DB에서 삭제
		$args = new stdClass();
		$args->data_srl = $member_icon_data->data_srl;
		$args->member_srl = $logged_info->member_srl;
		$this->deleteMemberIcon($args);

		$args = new stdClass();
		$args->icon_srl = $icon_data->icon_srl;
		$args->total_count = $icon_data->total_count + 1;
		$output = executeQuery('iconshop.updateIconSetTotalCount', $args);
		if(!$output->toBool())
		{
			return $output;
		}
		// 기존 상품들의 호환성을 위해서 추가.
		else
		{
			if($args->total_count > $icon_data->set_total_count)
			{
				$args = new stdClass();
				$args->icon_srl = $icon_data->icon_srl;
				$args->set_total_count = (int)$args->total_count;
				$output = executeQuery('iconshop.updateIconSetTotalCount', $args);
				if(!$output->toBool())
				{
					return $output;
				}
			}
		}

		// 판매로그 남기기
		$args = new stdClass();
		$args->data_srl = $member_icon_data->data_srl;
		$args->icon_srl = $member_icon_data->icon_srl;
		$args->category_srl = 3;
		$args->sender_srl = 0;
		$args->receive_srl = $logged_info->member_srl;
		$args->point = $point;
		$args->content = sprintf("%s(%s) [%s] 판매", $logged_info->user_id, $logged_info->nick_name, $icon_data->title);
		$this->insertLog($args);

		// 성공 메세지 등록
		$msg_code = sprintf(Context::getLang('success_sell'), $point);
		$this->setMessage($msg_code);
	}

	/**
	 * @brief 아이콘 삭제처리
	 **/
	function procIconshopIconDelete()
	{
		$oIconshopModel = getModel('iconshop');

		// 권한 체크
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'msg_not_permitted');
		}
		$logged_info = Context::get('logged_info');

		// 넘어온 변수 체크
		$params = Context::gets('data_srl');
		if(!$params->data_srl)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 보유하고 있는지 검사..
		$member_icon_data = $oIconshopModel->getMemberIconByDataSrl($params->data_srl);
		if(!$member_icon_data->data_srl || $member_icon_data->member_srl != $logged_info->member_srl)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 상품정보 구해옴
		$icon_data = $oIconshopModel->getIconBySrl($member_icon_data->icon_srl);

		// 상품 회원DB에서 삭제
		$args = new stdClass();
		$args->data_srl = $member_icon_data->data_srl;
		$args->member_srl = $logged_info->member_srl;
		$this->deleteMemberIcon($args);

		$args = new stdClass();
		$args->icon_srl = $icon_data->icon_srl;
		$args->total_count = $icon_data->total_count + 1;
		$output = executeQuery('iconshop.updateIconSetTotalCount', $args);
		if(!$output->toBool())
		{
			return $output;
		}
		// 호환성을 위해서 추가함.
		else
		{
			if($args->total_count > $icon_data->set_total_count)
			{
				$args = new stdClass();
				$args->icon_srl = $icon_data->icon_srl;
				$args->set_total_count = (int)$args->total_count;
				$output = executeQuery('iconshop.updateIconSetTotalCount', $args);
				if(!$output->toBool())
				{
					return $output;
				}
			}
		}

		// 삭제로그 남기기
		$args = new stdClass();
		$args->data_srl = $member_icon_data->data_srl;
		$args->icon_srl = $member_icon_data->icon_srl;
		$args->category_srl = 4;
		$args->sender_srl = 0;
		$args->receive_srl = $logged_info->member_srl;
		$args->point = 0;
		$args->content = sprintf("%s(%s) [%s] 삭제", $logged_info->user_id, $logged_info->nick_name, $icon_data->title);
		$this->insertLog($args);

		// 성공 메세지 등록
		$this->setMessage('success_deleted');
	}

	function insertIcon($args)
	{
		$args->icon_srl = getNextSequence();
		$output = executeQuery('iconshop.insertIcon', $args);
		if(!$output->toBool())
		{
			return $output;
		}
	}

	function updateIcon($args)
	{
		$output = executeQuery('iconshop.updateIcon', $args);
		if(!$output->toBool())
		{
			return $output;
		}
	}

	function deleteIcon($icon_srl)
	{
		$args = new stdClass();
		$args->icon_srl = $icon_srl;
		$output = executeQuery('iconshop.deleteIcon', $args);
		if(!$output->toBool())
		{
			return $output;
		}
	}

	function insertMemberIcon($args)
	{
		$args->data_srl = getNextSequence();
		$output = executeQuery('iconshop.insertMemberIcon', $args);

		return $output;
	}

	function updateMemberIcon($args)
	{
		$output = executeQuery('iconshop.updateMemberIcon', $args);
		if(!$output->toBool())
		{
			return $output;
		}
	}

	// icon_srl을 제외한 회원의 모든아이콘 is_selected N...
	function updateIsSelected($member_srl, $icon_srl)
	{
		$args = new stdClass();
		$args->member_srl = $member_srl;
		$args->icon_srl = $icon_srl;
		$output = executeQuery('iconshop.updateIsSelected', $args);
		if(!$output->toBool())
		{
			return $output;
		}
	}

	function deleteMemberIcon($args)
	{
		$output = executeQuery('iconshop.deleteMemberIcon', $args);
		if(!$output->toBool())
		{
			return $output;
		}
	}

	/**
	 * @brief 로그처리 함수
	 * category_srl (1구매 2선물 3되팔기 4버리기)
	 **/
	function insertLog($args)
	{
		if(!$args->data_srl)
		{
			return false;
		}
		$output = executeQuery('iconshop.insertLog', $args);
		if(!$output->toBool())
		{
			return $output;
		}
	}

	function deleteLog($args)
	{
		$output = executeQuery('iconshop.deleteLog', $args);
		if(!$output->toBool())
		{
			return $output;
		}
	}

	function resetLog()
	{
		$output = executeQuery('iconshop.deleteLog');
		if(!$output->toBool())
		{
			return $output;
		}
	}
}

<?php

/**
 * @class  iconshop
 * @author 러키군 (admin@barch.kr)
 * @brief  iconshop 모듈의 high class
 **/
class iconshop extends ModuleObject
{
	public static $config = NULL;

	public static function getConfig()
	{
		if(self::$config === NULL)
		{
			$config = getModel('module')->getModuleConfig('iconshop');
			if(!$config)
			{
				$config = new stdClass();
			}
			if(!$config->icon_width)
			{
				$config->icon_width = 20;
			}
			if(!$config->icon_height)
			{
				$config->icon_height = 20;
			}
			if(!$config->send_fee)
			{
				$config->send_fee = 0;
			}
			if(!$config->sell_per)
			{
				$config->sell_per = 50;
			}
			if(!$config->log_save_day)
			{
				$config->log_save_day = 7;
			}
			if(!$config->new_hour)
			{
				$config->new_hour = 24;
			}
			if(!$config->member_max_count)
			{
				$config->member_max_count = 100;
			}
			if(!$config->list_count)
			{
				$config->list_count = 10;
			}
			if(!$config->cols_list_count)
			{
				$config->cols_list_count = 2;
			}
			if(!$config->member_info_print)
			{
				$config->member_info_print = "Y";
			}
			if(!$config->item_delete_event)
			{
				$config->item_delete_event = "N";
			}
			if(!$config->item_delete_title)
			{
				$config->item_delete_title = "[nick_name]님이 구입하신 [[icon_title]] 아이콘의 시간이 만료 되었습니다.";
			}
			if(!$config->item_delete_message)
			{
				$config->item_delete_message = "[nick_name]님이 구입하신 [[icon_title]] 아이콘의 시간이 만료되어 삭제 되었습니다.\n\n[[end_date]]";
			}
			if(!$config->use_cache)
			{
				$config->use_cache = 'N';
			}

			self::$config = $config;
		}

		return self::$config;
	}

	protected static function setConfig($config)
	{
		$oModuleController = getController('module');
		$output = $oModuleController->insertModuleConfig('iconshop', $config);

		return $output;
	}

	protected static function getIconShopModuleInfo()
	{
		if($oCacheHandler = self::getCacheHandler())
		{
			if(($module_info = $oCacheHandler->get($oCacheHandler->getGroupKey('iconshop', "iconshop:iconshopinfo"), time() - 86400)) !== false)
			{
				return $module_info;
			}
		}

		$args = new stdClass();
		$args->module = 'iconshop';
		$output = executeQuery('iconshop.getIconshopModuleSrl', $args);
		if(is_array($output->data) || !$output->data->module_srl)
		{
			return false;
		}

		$module_srl = $output->data->module_srl;
		$module_info = getModel('module')->getModuleInfoByModuleSrl($module_srl);

		if($oCacheHandler)
		{
			$oCacheHandler->put($oCacheHandler->getGroupKey('iconshop', "iconshop:iconshopinfo"), $module_info, 86400);
		}

		return $module_info;
	}

	protected static function clearCacheByIconshopInfo()
	{
		$oCacheHandler = self::getCacheHandler();
		if(!$oCacheHandler)
		{
			return;
		}
		$oCacheHandler->delete($oCacheHandler->getGroupKey('iconshop', "iconshop:iconshopinfo"));
	}

	protected static function getCacheHandler()
	{
		static $oCacheHandler = null;
		if($oCacheHandler === null)
		{
			if(self::getConfig()->use_cache !== 'Y')
			{
				$oCacheHandler = false;
			}
			else
			{
				$oCacheHandler = CacheHandler::getInstance('object');
				if(!$oCacheHandler->isSupport())
				{
					$oCacheHandler = false;
				}
			}
		}
		return $oCacheHandler;
	}

	/**
	 * @brief 설치시 추가 작업이 필요할시 구현
	 **/
	function moduleInstall()
	{
		$oDB = DB::getInstance();

		$oModuleController = getController('module');
		$oAddonAdminController = getAdminController('addon');

		// iconshop 모듈에서 사용할 디렉토리 생성
		FileHandler::makeDir('./files/iconshop');

		if($oDB->isTableExists("iconshop_admin"))
		{
			$output = executeQueryArray('iconshop.getPreviousIcons');
			if($output->toBool())
			{
				return $output;
			}
			$oDB->begin();
			if($output->data)
			{
				foreach($output->data as $val)
				{
					$args = new stdClass();
					$args->icon_srl = $val->icon_srl;
					$args->title = $val->title;
					$args->total_count = $val->total_count;
					if(!$val->set_total_count)
					{
						$args->set_total_count = $val->total_count;
					}
					else
					{
						$args->set_total_count = $val->set_total_count;
					}
					$args->price = $val->price;
					$args->regdate = $val->regdate;
					$args->file1 = $val->file1;
					$args->extra_vars = serialize(new stdClass());
					$insert_output = executeQuery('iconshop.insertIcon', $args);
					if(!$insert_output->toBool())
					{
						$oDB->rollback();
						return $insert_output;
					}
				}
			}
			$oDB->commit();
			$oDB->dropTable('iconshop_admin');
		}

		// iconshop 모듈 생성
		$iconshop_info = self::getIconShopModuleInfo();
		if(!$iconshop_info->module_srl)
		{
			$args = new stdClass();
			$args->mid = 'iconshop';
			$args->module = 'iconshop';
			$args->browser_title = '아이콘샵';
			$args->site_srl = 0;
			$args->skin = 'default';
			$oModuleController->insertModule($args);
		}

		// 아이콘샵 모듈의 기본설정 저장
		$config = new stdClass();
		$config->icon_width = 20;
		$config->icon_height = 20;
		$config->send_fee = 0;
		$config->sell_per = 50;
		$config->log_save_day = 7;
		$config->new_hour = 24;
		$config->member_max_count = 100;
		$config->list_count = 10;
		$config->cols_list_count = 2;
		$config->member_info_print = "Y";
		$config->item_delete_event = "N";
		$config->item_delete_title = "[nick_name]님이 구입하신 [[icon_title]] 아이콘의 시간이 만료 되었습니다.";
		$config->item_delete_message = "[nick_name]님이 구입하신 [[icon_title]] 아이콘의 시간이 만료되어 삭제 되었습니다.\n\n[[end_date]]";
		$oModuleController->insertModuleConfig('iconshop', $config);

		// 회원 모듈 설정에서 이미지 마크 사용시...
		$member_config = getModel('member')->getMemberConfig();
		if($member_config->image_mark == 'Y')
		{
			$member_config->image_mark = "N";
			$oModuleController->insertModuleConfig('member', $member_config);
		}

		// 대표아이콘 출력 애드온 활성화 시키기
		$site_module_info = Context::get('site_module_info');
		$oAddonAdminController->doActivate('member_icon_print', $site_module_info->site_srl);
		$oAddonAdminController->makeCacheFile($site_module_info->site_srl);

		return new Object();
	}

	/**
	 * @brief 설치가 이상이 없는지 체크하는 method
	 **/
	function checkUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');
		$oAddonAdminModel = getAdminModel('addon');

		// 2010. 06. 25 point_limit 추가
		if($oDB->isTableExists("iconshop_admin"))
		{
			return true;
		}

		if($oDB->isTableExists('iconshop_member'))
		{
			return true;
		}

		if(!$oDB->isColumnExists('iconshop_items', 'category_srl'))
		{
			return true;
		}

		if(!$oDB->isIndexExists('iconshop_items', 'idx_category_srl'))
		{
			return true;
		}

		if(!$oDB->isColumnExists('iconshop_user_item', 'category_srl'))
		{
			return true;
		}

		if(!$oDB->isColumnExists('iconshop_items', 'member_srl'))
		{
			return true;
		}

		if(!$oDB->isIndexExists('iconshop_user_item', 'idx_category_srl'))
		{
			return true;
		}


		// iconshop 모듈에서 사용할 디렉토리가 없으면
		if(!is_dir('./files/iconshop'))
		{
			return true;
		}

		// iconshop 모듈이 존재하지 않으면...
		$iconshop_info = self::getIconShopModuleInfo();
		if(!$iconshop_info->module_srl)
		{
			return true;
		}

		// iconshop 모듈의 기본설정이 없으면...
		$iconshop_config = $oModuleModel->getModuleConfig('iconshop');
		if(!$iconshop_config)
		{
			return true;
		}

		// 회원 모듈 설정에서 이미지 마크 사용시...
		$member_config = $oModuleModel->getModuleConfig('member');
		if($member_config->image_mark == 'Y')
		{
			return true;
		}

		// 대표아이콘 출력 애드온이 비활성화 되있으면..
		$site_module_info = Context::get('site_module_info');
		if(!$oAddonAdminModel->isActivatedAddon('member_icon_print', $site_module_info->site_srl))
		{
			return true;
		}
	}

	/**
	 * @brief 업데이트 실행
	 **/
	function moduleUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$oAddonAdminController = getAdminController('addon');

		if($oDB->isTableExists("iconshop_admin"))
		{
			$output = executeQueryArray('iconshop.getPreviousIcons');
			if(!$output->toBool())
			{
				return $output;
			}
			$oDB->begin();
			if($output->data)
			{
				foreach($output->data as $val)
				{
					$args = new stdClass();
					$args->icon_srl = $val->icon_srl;
					$args->title = $val->title;
					$args->total_count = $val->total_count;
					if(!$val->set_total_count)
					{
						$args->set_total_count = $val->total_count;
					}
					else
					{
						$args->set_total_count = $val->set_total_count;
					}
					$args->price = $val->price;
					$args->regdate = $val->regdate;
					$args->file1 = $val->file1;
					$args->extra_vars = serialize(new stdClass());
					$insert_output = executeQuery('iconshop.insertIcon', $args);
					if(!$insert_output->toBool())
					{
						$oDB->rollback();
						return $insert_output;
					}
				}
			}
			$oDB->commit();
			$oDB->dropTable('iconshop_admin');
			$admin_database_file = $this->module_path . 'schemas/iconshop_admin.xml';
			$admin_delete_output = FileHandler::removeFile($admin_database_file);
			if(!$admin_delete_output->toBool())
			{
				return $admin_delete_output;
			}

		}

		if($oDB->isTableExists('iconshop_member'))
		{
			$output = executeQueryArray('iconshop.getPreviousUserIcons');
			if(!$output->toBool())
			{
				return $output;
			}
			$oDB->begin();
			if($output->data)
			{
				foreach($output->data as $val)
				{
					$args = new stdClass();
					$args->data_srl = $val->data_srl;
					$args->icon_srl = $val->icon_srl;
					$args->member_srl = $val->member_srl;
					$args->is_selected = $val->is_selected;
					$args->day_limit = $val->minuted_limit;
					$args->start_date = $val->start_date;
					$args->end_date = $val->end_date;
					$args->ipaddress = $val->ipaddress;

					$insert_output = executeQuery('iconshop.insertMemberIcon', $args);
					if(!$insert_output->toBool())
					{
						$oDB->rollback();
						return $insert_output;
					}
				}
			}
			$oDB->commit();
			$oDB->dropTable('iconshop_member');
			$member_database_file = $this->module_path . 'schemas/iconshop_member.xml';
			$member_delete_output = FileHandler::removeFile($member_database_file);
			if(!$member_delete_output->toBool())
			{
				return $member_delete_output;
			}
		}

		if(!$oDB->isColumnExists('iconshop_items', 'category_srl'))
		{
			$oDB->addColumn('iconshop_items', 'category_srl', 'number', '11', '0', true);
		}

		if(!$oDB->isColumnExists('iconshop_items', 'member_srl'))
		{
			$oDB->addColumn('iconshop_items', 'member_srl', 'number', '11', '0', true);
		}

		if(!$oDB->isColumnExists('iconshop_user_item', 'category_srl'))
		{
			$oDB->addColumn('iconshop_user_item', 'category_srl', 'number', '11', '0', true);
		}

		if(!$oDB->isIndexExists('iconshop_items', 'idx_category_srl'))
		{
			$oDB->addIndex('iconshop_items', 'idx_category_srl', array('category_srl'));
		}

		if(!$oDB->isIndexExists('iconshop_user_item', 'idx_category_srl'))
		{
			$oDB->addIndex('iconshop_user_item', 'idx_category_srl', array('category_srl'));
		}

		// iconshop 모듈에서 사용할 디렉토리 생성
		if(!is_dir('./files/iconshop'))
		{
			FileHandler::makeDir('./files/iconshop');
		}

		// iconshop 모듈 생성
		$iconshop_info = self::getIconShopModuleInfo();
		if(!$iconshop_info->module_srl)
		{
			$args = new stdClass();
			$args->mid = 'iconshop';
			$args->module = 'iconshop';
			$args->browser_title = '아이콘샵';
			$args->site_srl = 0;
			$args->skin = 'default';
			$oModuleController->insertModule($args);
		}

		// 아이콘샵 모듈의 기본설정 저장
		$iconshop_config = $oModuleModel->getModuleConfig('iconshop');
		if(!$iconshop_config)
		{
			$config = new stdClass();
			$config->icon_width = 16;
			$config->icon_height = 16;
			$config->send_fee = 0;
			$config->sell_per = 50;
			$config->log_save_day = 7;
			$config->new_hour = 24;
			$config->list_count = 10;
			$config->cols_list_count = 2;
			$config->member_info_print = "Y";
			$config->member_max_count = 100;
			$config->item_delete_event = "Y";
			$config->item_delete_title = "[nick_name]님이 구입하신 '[icon_title]' 아이콘이 삭제 되었습니다.";
			$config->item_delete_message = "[nick_name]님이 구입하신 '[icon_title]' 아이콘의 기간이 만료되어 삭제 되었습니다.";
			$oModuleController->insertModuleConfig('iconshop', $config);
		}

		// 회원 모듈 설정에서 이미지 마크 사용시...
		$member_config = getModel('member')->getMemberConfig();
		if($member_config->image_mark == 'Y')
		{
			$member_config->image_mark = "N";
			$oModuleController->insertModuleConfig('member', $member_config);
		}

		// 대표아이콘 출력 애드온이 비활성화 되있으면..
		$site_module_info = Context::get('site_module_info');
		$output = $oAddonAdminController->doActivate('member_icon_print', $site_module_info->site_srl);
		if(!$output->toBool())
		{
			return $output;
		}
		$oAddonAdminController->makeCacheFile($site_module_info->site_srl);

		return new Object(0, 'success_updated');
	}

	function moduleUninstall()
	{
		// iconshop 모듈에서 사용한 디렉토리 삭제
		FileHandler::removeDir(_XE_PATH_ . 'files/iconshop');

		return new Object();
	}

	/**
	 * @brief 캐시 파일 재생성
	 **/
	function recompileCache()
	{
	}
}

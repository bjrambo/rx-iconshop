<?php
require_once(_XE_PATH_.'modules/iconshop/iconshop.view.php');

class iconshopMobile extends iconshopView
{
	function init()
	{
		$oModuleModel = getModel('module');
		$iconshop_info = self::getIconShopModuleInfo();

		// 설정 변수 지정
		$template_path = sprintf("%sm.skins/%s/", $this->module_path, $iconshop_info->mskin);
		if(!is_dir($template_path) || !$iconshop_info->mskin)
		{
			$iconshop_info->mskin = 'default';
			$template_path = sprintf("%sm.skins/%s/", $this->module_path, $iconshop_info->mskin);
		}
		$this->setTemplatePath($template_path);
	}

}

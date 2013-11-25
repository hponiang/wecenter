<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2013 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|   
+---------------------------------------------------------------------------
*/

define('IN_AJAX', TRUE);


if (!defined('IN_ANWSION'))
{
	die;
}

define('IN_MOBILE', true);

class weixin extends AWS_CONTROLLER
{
	public function get_access_rule()
	{
		$rule_action['rule_type'] = 'white';
		$rule_action['actions'] = array();
		
		return $rule_action;
	}
	
	public function setup()
	{
		HTTP::no_cache_header();
	}
	
	public function authorization_action()
	{
		if ($_GET['code'])
		{
			if ($access_token = json_decode(curl_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . AWS_APP::config()->get('weixin')->app_id . '&secret=' . AWS_APP::config()->get('weixin')->app_secret . '&code=' . $_GET['code'] . '&grant_type=authorization_code'), true))
			{
				if ($access_token['errcode'])
				{
					H::redirect_msg('Error: ' . $access_token['errcode'] . ' ' . $access_token['errmsg']);
				}
				
				$access_user = json_decode(curl_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token['access_token'] . '&openid=' . $access_token['openid']));
				
				TPL::assign('access_token', $access_token);
				TPL::assign('access_user', $access_user);
				
				TPL::output('m/weixin/authorization');
			}
		}
		
		H::redirect_msg(AWS_APP::lang()->_t('授权失败, 请返回重新操作'));
	}
}
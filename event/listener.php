<?php
/**
* phpBB Extension - marttiphpbb customcode
* @copyright (c) 2014 - 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\customcode\event;

use phpbb\event\data as event;
use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\request\request;
use phpbb\template\twig\twig as template;
use phpbb\user;
use phpbb\language\language;
use phpbb\template\twig\loader;

use marttiphpbb\customcode\model\customcode_directory;
use marttiphpbb\customcode\util\cnst;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var auth */
	protected $auth;

	/** @var config */
	protected $config;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var language */
	protected $language;

	/** @var loader */
	protected $loader;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/**
	 * @param auth $auth
	 * @param config $config
	 * @param request $request
	 * @param template $template
	 * @param user $user
	 * @param language $language
	 * @param loader $loader
	 * @param string $phpbb_root_path
	 * @param string $php_ext
	*/
	public function __construct(
		auth $auth,
		config $config,
		request $request,
		template $template,
		user $user,
		language $language,
		loader $loader,
		string $phpbb_root_path,
		string $php_ext
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->loader = $loader;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.page_header'		=> 'core_page_header',
			'core.page_footer'		=> 'core_page_footer',
			'core.append_sid'		=> 'core_append_sid',
		];
	}

	public function core_page_header(event $event)
	{
		error_log($this->phpbb_root_path . cnst::FOLDER);
		$this->loader->addSafeDirectory($this->phpbb_root_path . cnst::FOLDER);
		$this->template->assign_var('S_CUSTOMCODE', $this->config['tpl_allow_php'] ? false : true);
	}

	public function core_page_footer(event $event)
	{
		global $phpbb_admin_path; // core.admin_path doesn't seem to exist.

		$query_string = $this->user->page['query_string'];

		$params = $template_vars = [];
		parse_str($query_string, $params);

		foreach ($params as $name => $value)
		{
			$template_vars['CUSTOMCODE_PARAM_' . strtoupper($name)] = $value;
		}

		if (sizeof($template_vars))
		{
			$this->template->assign_vars($template_vars);
		}

		$show_events = ($this->request->variable('customcode_show_events', 0)) ? true : false;

		if ($show_events && $this->auth->acl_get('a_'))
		{
			$query_string = str_replace('&customcode_show_events=1', '&customcode_show_events=0', $query_string);
			$query_string = str_replace('customcode_show_events=1', 'customcode_show_events=0', $query_string);

			$this->template->assign_var('U_CUSTOMCODE_HIDE_EVENTS', append_sid($this->user->page['page_name'], $query_string));

			$customcode_directory = new customcode_directory($this->language, $this->phpbb_root_path);
			$filenames = $customcode_directory->get_filenames();

			$template_edit_urls = [];
			$params = array(
				'i'			=> '-marttiphpbb-customcode-acp-main_module',
				'mode'		=> 'edit',
			);

			foreach ($filenames as $filename)
			{
				$params['filename'] = $filename;
				$this->template->assign_var(
					'U_CUSTOMCODE_' . strtoupper($customcode_directory->get_basename($filename)),
					append_sid($phpbb_admin_path . 'index.' . $this->php_ext, $params, true, $this->user->session_id)
				);
			}

			$this->language->add_lang('common', 'marttiphpbb/customcode');
		}
	}

	public function core_append_sid(event $event)
	{
		$params = $event['params'];

		if (is_string($params))
		{
			if (strpos($params, 'customcode_show_events=0') !== false)
			{
				return;
			}
		}

		if ($this->request->variable('customcode_show_events', 0)
			&& $this->auth->acl_get('a_'))
		{
			if (is_string($params) && $params != '')
			{
				$params .= '&customcode_show_events=1';
			}
			else
			{
				if ($params === false)
				{
					$params = [];
				}
				if (isset($params['customcode_hide_events']))
				{
					$params = false;
				}
				else
				{
					$params['customcode_show_events'] = 1;
				}
			}
			$event['params'] = $params;
		}
	}
}

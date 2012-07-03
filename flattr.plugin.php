<?php
/**
 * Flattr plugin
 * adding a flattr autosubmit button to your posts
 *
 * @package flattr
 * @author fengor <fengor@fengors-realm.de>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link http://fengors-realm.de/habari/flattr 
 */

class Flattr extends Plugin {

	public function action_plugin_activation($file) {
		if ( Plugins::id_from_file( $file ) != Plugins::id_from_file( __FILE__ ) ) return;

		Options::set('flattr__add_button','true');
	}

	public function action_plugin_ui($plugin_id,$action) {
		if ($plugin_id != $this->plugin_id()) return;

		if ($action == _t('Configure')) {
			$ui = new FormUI(strtolower(get_class($this)));
			$flattr_uid = $ui->append('text','flattr_uid','flattr__uid',_t('Flattr username: '));
			$large_icon = $ui->append('select','large_icon','option:flattr__large_icon',_t('Show large image'));
			$large_icon->options = array('true'=>'Large Image','fals'=>'Small Image');
			$add_button = $ui->append('select','add_button','option:flattr__add_button',_t('Auto Insert: '));
			$add_button->options = array('true'=>'True','false'=>'False');
			
			$ui->append('submit','save',_t('Save'));
			$ui->out();
		}
	}

	public function filter_plugin_config($actions, $plugin_id) {
		if ($plugin_id == $this->plugin_id()) {
			$actions[]= _t('Configure');
		}
		return $actions;
	}

	public function filter_post_content_out($content, $post) {
		$add_button = Options::get('flattr__add_button');

		if ($add_button == 'true') {
			$content = $content . $this->create_autolink($post);
		}
		
		return $content;
	}

	public function theme_show_flattr($theme, $post) {
		return $this->create_autolink($post);
	}

	private function create_autolink($post) {
		$link = '<div class="flattr">';
		$site_title = Options::get('title');
		$flattr_uid = Options::get('flattr__uid');
		$show_large = Options::get('flattr__large_icon');

		$img_url = '';
		if ($show_large == 'true') {
			$img_url='https://api.flattr.com/button/flattr-badge-large.png';
		} else {
			$img_url='https://flattr.com/_img/icons/flattr_logo_16.png';
		}

		$link .= '<a href="https://flattr.com/submit/auto?user_id='.$flattr_uid.'&url='.urlencode($post->permalink).'&title='.urlencode($site_title.' - '.$post->title_out).'">';
		$link .= '<img src="'.$img_url.'" /></a>';
		$link .= '</div>';
		
		return $link;
	}
}
?>

<div class="social_links">
	<?php
	
if ($link->link_id):
	
	?>
	<select name="_ssl_network[site]" id="ssl_network_site">
		<option value="twitter">Twitter</option>
		<option value="flickr">Flickr</option>
		<option value="tumblr">Tumblr</option>
		<option value="lastfm">last.fm</option>
		<option value="facebook">facebook</option>
		<option value="plurk">Plurk</option>
		<option value="orkut">Orkut</option>
	</select>
	<input type="text" name="_ssl_network[url]" id="ssl_network_url" size="40" value="http://" /> <a href="javascript:void(0)" class="button-secondary incluir"> <?php echo __('Add','simply-social-links'); ?> </a>
	<p><?php _e('Example: <code>http://wordpress.org/</code> &#8212; don&#8217;t forget the <code>http://</code>'); ?></p>
	<?php
	
	$resultado = $wpdb->get_results($wpdb->prepare("select * from $wpdb->linkmeta where link_id = %d and meta_key like '_ssl_network%%' ", $link->link_id), ARRAY_A);
	
	?>
	<dl id="ssl_links_list">
		<h4><?php _e('List') ?>:</h4>
		<?php
	
	if (count($resultado)>0) :
	
		foreach($resultado as $linha):
			
			$padrao = '/^_ssl_network\[(.*)\]$/i';
			preg_match($padrao, $linha['meta_key'], $rede);
			$rede = $rede[1];
			$url = $linha['meta_value'];
			$mid = $linha['meta_id'];
			
			echo "<dt class=\"$rede\"></dt><dd>$url <a href=\"javascript:void(0);\" class=\"button-secondary hide-if-no-js \" onclick=\"ssl_delete_link(event, '$mid','{$link->link_id}')\">" . __('Delete','simply-social-links') . "</a></dd>";
			
		endforeach;
		
	endif;
	?></dl>
	<?php
else:
	_e('You need to save this link to add social network links in it. Or try editing another.');
endif;
	?>
	
</div>
// JavaScript Document

jQuery(document).ready(function() {
	jQuery('select#ssl_network_site').change(function(a){
		var rede = this.value;
		jQuery('input#ssl_network_url').removeClass().addClass(rede).focus();
	});
	
	
	jQuery('select#ssl_network_site').change();
	
	jQuery('a.incluir').click(ssl_add_link);
	
	
	
});

function ssl_add_link() {
	console.log('clicou');
	var site = jQuery('select#ssl_network_site').val();
	var url = jQuery('input#ssl_network_url').val();
	var link_id = jQuery('input[name=link_id]').val()
	
	jQuery('select#ssl_network_site, input#ssl_network_url').attr('disabled','disabled');
	
	var data = {
		action: 'add_social_network_link',
		site: site,
		url: url,
		link_id : link_id
	};
	
	var jqxhr = jQuery.ajax({
		url: ajaxurl,
		dataType: 'json',
		data: data
	})
	.success(function(a) {
		var botao_apagar = "<a href=\"javascript:void(0);\" class=\"button-secondary hide-if-no-js \" onclick=\"ssl_delete_link(event, '" + lind_id + "','{$link->link_id}')\">" . __('Delete','simply-social-links') . "</a>"
		
		
		var el = '<dt class="' + site + '"></dt><dd>' + url + '</dd>';
		
		var list = jQuery('#ssl_links_list');
		
		
		jQuery('#ssl_links_list').append(el);
		jQuery('input#ssl_network_url').val('http://');
		
	})
	.error(function(c, d) {
		alert("Erro: " + c.status + " - " + c.statusText);
	})
	.complete(function(e, f) {
		jQuery('select#ssl_network_site, input#ssl_network_url').attr('disabled','');
	});
	
	return true;
	
}

function ssl_delete_link(event, mid, lid) {
	
	var src = jQuery(event.target).parent();
	var ant = src.prev();

	
	var data = {
		action: 'delete_social_network_link',
		meta_id: mid,
		link_id: lid
	};
	
	var jqxhr = jQuery.ajax({
		url: ajaxurl,
		dataType: 'json',
		data: data
	})
	.success(function(a) {
		console.log(a);
		src.remove();
		ant.remove();
	})
	.error(function(c, d) {
		alert("Erro: " + c.status + " - " + c.statusText);
	})
	.complete(function(e) {
		console.log(e);
		jQuery('select#ssl_network_site, input#ssl_network_url').attr('disabled','');
	});
	
	return true;
	
}














































